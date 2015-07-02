module.exports = function( grunt ) {
	'use strict';

	// Load multiple grunt tasks using globbing patterns
	require('load-grunt-tasks')(grunt);

	grunt.initConfig( {
		pkg: grunt.file.readJSON('package.json'),

		// project directories
		dirs: {
			lang: 'i18n/languages',
		},

		// Check that textdomain is set
		checktextdomain: {
			options:{
				text_domain: 'wp-adpress',
				create_report_file: false,
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,3,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
					' __ngettext:1,2,3d',
					'__ngettext_noop:1,2,3d',
					'_c:1,2d',
					'_nc:1,2,4c,5d'
				]
			},
			files: {
				src: [
					'**/*.php', // Include all files

					'!node_modules/**', // Exclude node_modules/
					'!tests/**', // Exclude unit tests/
					'!bin/**', // Exclude Bin/
					'!i18n/**', // Exclude i18n/
					'!build/.*'// Exclude build/
				],
				expand: true
			}
		},

		// Generate the mo files
		potomo: {
			dist: {
				options: {
					poDel: false 
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.lang %>',
					src: ['*.po'],
					dest: '<%= dirs.lang %>',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Generate POT file
		makepot: {
			target: {
				options: {
					domainPath: 'i18n',               // Where to save the POT file.
					exclude: [],                      // List of files or directories to ignore.
					type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
				}
			}
		},

		// Clean up build directory
		clean: {
			main: ['build']
		},

		// Copy the plugin files into the build directory
		copy: {
			main: {
				src:  [
					'**',
					'!node_modules/**',
					'!bin/**',
					'!build/**',
					'!tests/**',
					'!help/**',
					'!.git/**',
					'!.idea/**',
					'!Gruntfile.js',
					'!package.json',
					'!phpunit.xml',
					'!.scrutinizer.yml',
					'!.travis.yml',
					'!.gitignore',
					'!.gitmodules',
					'!README.md',
					'!README.txt',
					'!install.txt',
					'!composer.json',
					'!composer.lock'
				],
				dest: 'build/<%= pkg.name %>/plugin/'
			},
			instructions: {
				src: [ 'install.txt' ],
				dest: 'build/<%= pkg.name %>/'
			}
		},

		// Shell Commands
		shell: {
			options: {
				execOptions: {
					maxBuffer: Infinity
				}
			},
			composer_install: {
				command: function() {
					var cmd = [
						'composer install',
						'composer dumpautoload'
						].join( '&&' );
						return cmd;
				}
			},
			lv1_addons: {
				command: function() {
					var cmd = [
						'git archive --remote=git@bitbucket.org:omarabid/wpad-customize.git --prefix=build/adpress/plugin/inc/addons/customize/ HEAD |  tar xvf -',
						'git archive --remote=git@bitbucket.org:omarabid/wpad-export.git --prefix=build/adpress/plugin/inc/addons/export/ HEAD |  tar xvf -',
						'git archive --remote=git@bitbucket.org:omarabid/wpad-paypal-standard.git --prefix=build/adpress/plugin/inc/addons/paypal-standard/ HEAD |  tar xvf -',
						'git archive --remote=git@bitbucket.org:omarabid/wpad-stats.git --prefix=build/adpress/plugin/inc/addons/stats/ HEAD |  tar xvf -',
						'git archive --remote=git@bitbucket.org:omarabid/wpad-reports.git --prefix=build/adpress/plugin/inc/addons/reports/ HEAD |  tar xvf -',
						'git archive --remote=git@bitbucket.org:omarabid/wpad-license.git --prefix=build/adpress/plugin/inc/addons/license/ HEAD |  tar xvf -'
						].join( '&&' );
						return cmd;
				}
			}
		},

		// Update the Plugin Version
		replace: {
			version: {
				src: [ 'wp-adpress.php' ],             // source files array (supports minimatch)
				dest: 'build/<%= pkg.name %>/plugin/',             // destination directory or file
				replacements: [{
					from: '{{@version}}',                   // string replacement
					to: '<%= pkg.version %>'
				}]
			}
		},

		//Compress build directory into <name>.zip 
		compress: {
			plugin: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>/<%= pkg.name %>-plugin.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/plugin/',
				src: ['**/*'],
				dest: '<% pkg.name %>/plugin/'
			},
			build: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: ['**/*', '!plugin/**'],
				dest: '<%= pkg.name %>/'
			}
		},

		// Release tool
		bump: {
			options: {
				files: [ 'package.json' ],
				updateConfigs: [],
				commit: true,
				commitMessage: 'Release v%VERSION%',
				commitFiles: [ 'package.json' ],
				createTag: true,
				tagName: 'v%VERSION%',
				tagMessage: 'Version %VERSION%',
				push: false,
				gitDescribeOptions: '--tags --always --abbrev=1 --dirty=-d',
				globalReplace: false,
				prereleaseName: false,
				regExp: false
			}
		}
	} );	

	// Build Tasks 
	grunt.registerTask( 'default', [ 'shell:composer_update', 'makepot', 'potomo', 'clean', 'copy', 'replace', 'compress' ] );
	grunt.registerTask( 'lv1', [ 'shell:composer_update', 'makepot', 'potomo', 'clean', 'copy', 'shell:lv1_addons', 'replace', 'compress' ] );

	// Tooling Tasks
	grunt.registerTask( 'report', ['checktextdomain'] );
};
