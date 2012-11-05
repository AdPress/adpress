/**
 * DocuKu Table Content generator
 *
 * @author: Abid Omar
 */

/**
 * Global Class
 */
function docx () {
	/*
	 * 0. Elements: This is useful for quick access of the different
	 * 				document elements and also to save memory
	 */
	this.loadElements();
	/*
	 * 1. Index: These functions structure the document into
	 * 			 an object and help generate the Index.
	 */
	this.createTree();
	this.generateIndex();
	this.generateHeaders();

	if (console) {console.info(this);}
}

/**
 * Create jQuery objects with the document elements
 */
docx.prototype.loadElements = function() {
	this.el = {};
	// Summary
	this.el.index = {
		el : $('#summary')
	}
};
/**
 * Generate a tree object using the sections
 */
docx.prototype.createTree = function() {
	var that = this;
	var tree = {};
	var max_level = 2;
	// Read the first level
	tree = readLevel(1);
	// Find the highest level
	max_level = find_max_level();

	/*
	 * Recurssion for the inside levels
	 */
	rec(tree, 1, max_level);
	this.el.index.tree = tree;
	this.el.index.max_level = max_level;
	/*
	 * Find the highest level
	 */
	function find_max_level() {
		var num = 2;
		$('.section').each( function() {
			var tmp = ($(this).attr('class').charAt($(this).attr('class').search('level_')+6));
			if (tmp > num) {
				num = tmp;
			}
		});
		return num;
	}

	/*
	 * Tree Traversal function
	 */
	function rec(tree, start, max) {
		var i = start + 1;
		for (item in tree) {
			tree[item].child = readLevel(i, tree[item].el);
			rec(tree[item].child, i);
			if (i === max) {
				return;
			}
		}
	}

	/**
	 * Read Level Generate the sections in a level into an object
	 */
	function readLevel(i, limit) {
		var level = {};
		$('.level_'+i, limit).each( function(j, val) {
			/*
			 * If there is no ID, generate one using the name
			 * or a random GUID.
			 */
			if (!$(this).attr('id')) {
				if ($(this).attr('name')) {
					var id = $(this).attr('name').replace(/ /gi, '').toLowerCase();
					$(this).attr('id', id);
				} else {
					$(this).attr('id', function() {
						var S4 = function() {
							return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
						};
						return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
					}());
				}
			}
			level['section_'+j] = {
				el: $(this),
				child: {}
			};
		});
		return level;
	}

};
/**
 * Generate the HTML Index
 */
docx.prototype.generateIndex = function() {
	var that = this;
	var index = $('<ol></ol>', {
		id:'index_table'
	});
	rec (this.el.index.tree, index);
	function rec (tree, ind) {
		for (li in tree) {
			var el = tree[li].el;
			var item = $('<li></li>');
			item.append('<a href="#'+el.attr('id')+'">'+el.attr('name')+'</a>');
			item.appendTo(ind);
			if (tree[li].child) {
				rec(tree[li].child, $('<ol></ol>').appendTo(ind));
			}
		}
	}

	this.el.index.el.append(index);
	this.el.index.table = $('#index_table');
};
/*
 * Generate Sections Headers
 */
docx.prototype.generateHeaders = function() {
	var that = this;
	rec(this.el.index.tree, 1.7, '');
	function rec (tree, x, cum) {
		var i = 1;
		for (li in tree) {
			var el = tree[li].el;
			var h2 = $('<div></div>');
			var cumul = cum + i + '.';
			h2.append('<h2>'+cumul+' '+el.attr('name')+'</h2><div class="clear"></div>');
			$('H2', h2).css('font-size', x + 'em');
			h2.prependTo(tree[li].el);
			if (tree[li].child) {
				rec(tree[li].child , (x * 0.76), cumul);
			}
			i++;
		}
	}
};
/**
 * Page Load
 */
$(document).ready( function() {
	// Creates a new instance of doc
	var documentation = new docx();
	window.doc = documentation;
});