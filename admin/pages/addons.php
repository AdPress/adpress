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

	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 10;

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $base_url;

	/**
	 * Total number of addons
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $total_count;

	/**
	 * Total number of required addons 
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $required_count;

	/**
	 * Total number of optional addons 
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $optional_count;

	function __construct() {
		parent::__construct( array(
			'singular'=> 'wp_list_text_link', //Singular label
			'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );

		$this->get_addons_count();
		$this->base_url = admin_url( 'admin.php?page=adpress-addons' );
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $views All the views available
	 */
	public function get_views() {

		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$required_count  = '&nbsp;<span class="count">(' . $this->required_count . ')</span>';
		$optional_count = '&nbsp;<span class="count">(' . $this->optional_count . ')</span>';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'wp-adpress') . $total_count ),
			'required'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'required', 'paged' => FALSE ) ), $current === 'required' ? ' class="current"' : '', __('Required', 'wp-adpress') . $required_count ),
			'optional'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'optional', 'paged' => FALSE ) ), $current === 'optional' ? ' class="current"' : '', __('Optional', 'wp-adpress') . $optional_count ),
		);

		return apply_filters( 'wp_adpress_addons_table_views', $views );
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
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';		
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Fetch the items -- */
		$addons = apply_filters('adpress_addons', array());
		$required = array();
		$optional = array();

		foreach( $addons as $addon ) {
			if ( isset( $addon['required'] ) ) {
				$required[] = $addon;
			} else {
				$optional[] = $addon;
			}
		}

		switch( $status ) {
		case 'required':
			$this->items = $required;
			break;
		case 'optional':
			$this->items = $optional;
			break;
		default:
			// nothing to do
			$this->items = $addons;
			break;
		}
		
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

	public function get_addons_count() {
		$addons = apply_filters( 'adpress_addons', array() );
		$required = 0;
		$optional = 0;
		foreach( $addons as $addon ) {
			if ( isset( $addon['required'] ) ) {
				$required++;
			} else {
				$optional++;
			}
		}

		// Total Count
		$this->total_count = count( $addons );		
		$this->required_count = $required;
		$this->optional_count = $optional;
	}
}
?>
   <div class="wrap" id="adpress">
   <div id="adpress-icon-addons" class="icon32"><br></div><h2><?php _e('Add-ons', 'wp-adpress'); ?></h2>

<?php
$wp_list_table = new wp_adpress_addons_table();
$wp_list_table->views();
$wp_list_table->prepare_items();
$wp_list_table->display();
?>
   </div>


