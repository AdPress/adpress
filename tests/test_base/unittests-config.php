<?php
/* Path to the WordPress codebase you'd like to test. Add a backslash in the end. */
define( 'ABSPATH', 'F:/dev/adpressdev/' );

define( 'DB_NAME', 'adpressdev' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '1stand.2agree' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );

/* Cron tries to make an HTTP request to the blog, which always fails, because tests are run in CLI mode only */
define( 'DISABLE_WP_CRON', true );

$GLOBALS['table_prefix'] = $table_prefix = 'wp_';
