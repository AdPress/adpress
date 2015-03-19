<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}


if (isset($_GET['action']) && $_GET['action'] === 'deactivate') {
	$id = $_GET['id'];
	$addons=	apply_filters('adpress_addons', array());

	foreach ($addons as $addon) {
		if ($addon['id'] === $id) {
			$plugin = array($addon['basename']);
		}
	}
	deactivate_plugins($plugin);
}


if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class wp_adpress_addons_table extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'singular'=> 'wp_list_text_link', //Singular label
			'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			//The code that goes before the table is here
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
		}
	}

	function get_columns() {
		return $columns= array(
			'col_name'=>__('Name', 'wp-adpress'),
			'col_description'=>__('Description', 'wp-adpress'),
			'col_version'=>__('Version', 'wp-adpress'),
			'col_author' => __('Author', 'wp-adpress'),
		);
	}


	function prepare_items() {
		global $_wp_column_headers;
		$screen = get_current_screen();

		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Fetch the items -- */
		$this->items = apply_filters('adpress_addons', array());
	}

	function column_col_name($item) {
		if (!isset($item['required'])) {
			$actions = array(
				'deactivate'      => sprintf('<a href="?page=%s&action=%s&id=%s">Deactivate</a>',$_REQUEST['page'],'deactivate',$item['id']),
			);
		} else {
			$actions = array(
				'required' => sprintf('<span>Required</span>'),
			);
		}
		if (isset($item['settings'])) {
			$actions['settings'] = sprintf('<a href="?page=%s">Settings</a>', $item['settings']);
		}
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
	}	  
	function column_col_description($item) {
		return $item['description'];   
	}	  
	function column_col_version($item) {
		return $item['version'];
	}	  
	function column_col_author($item) {
		return $item['author'];
	}	  
}
?>
   <div class="wrap" id="adpress">
   <div id="adpress-icon-addons" class="icon32"><br></div><h2><?php _e('Add-ons', 'wp-adpress'); ?></h2>

<?php
$wp_list_table = new wp_adpress_addons_table();
$wp_list_table->prepare_items();
$wp_list_table->display();
?>
   </div>


