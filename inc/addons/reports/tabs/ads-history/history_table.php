<?php
/**
 * Ads History Table Class
 *
 * @package     Addons 
 * @subpackage 	Reports 
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * wp_adpress_ads_history_table Class
 *
 * Renders the Payment History table on the Payment History page
 *
 */
class wp_adpress_ads_history_table extends WP_List_Table {

	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 20;

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $base_url;

	/**
	 * Total number of payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $total_count;	

	/**
	 * Get things started
	 *
	 * @since 1.0.0
	 * @uses EDD_Payment_History_Table::get_payment_counts()
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {

		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular'  => __('Items', 'wp-adpress'),    // Singular name of the listed records
			'plural'    => __('Item', 'wp-adpress'),    	// Plural name of the listed records
			'ajax'      => false             			// Does this table support ajax?
		) );

		$this->get_count();
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=adpress-reports&tab=ads_history' );
		$this->prepare_items();
	}

	public function advanced_filters() {	
		$status     = isset( $_GET['status'] )      ? $_GET['status'] : '';
?>
	<div id="wp-adpress-adshistory-filters">
	<?php $this->search_box( __( 'Search', 'wp-adpress' ), 'wp-adpress-adshistory' ); ?>
	</div>

<?php
	}

	/**
	 * Show the search field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?><br/>
		</p>
<?php
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

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'wp-adpress') . $total_count ),	
		);

		return apply_filters( 'wp_adpress_adshistory_table_views', $views );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'id'     	=> __( 'ID', 'wp-adpress' ),
			'approved'  => __( 'Approved At', 'wp-adpress' ),
			'expired'  	=> __( 'Expired at', 'wp-adpress' ),
			'buyer'  	=> __( 'Buyer', 'wp-adpress' ),	
		);

		return apply_filters( 'wp_adpress_adshistory_table_columns', $columns );
		return $columns;
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		$columns = array(
		);
		return apply_filters( 'wp_adpress_adshistory_table_sortable_columns', $columns );
		return $columns;
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @param array $item Contains all the data of the discount code
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $adhistory, $column_name ) {	
		$data = wp_adpress_get_adhistory( $adhistory->ID );	
		switch ( $column_name ) {	
		case 'id':
			return $data['adhistory_id'];
			break;
		case 'approved':
			return wp_adpress_format_time( $data['approved'] );
			break;
		case 'expired':
			return wp_adpress_format_time( $data['expired'] );
			break;
		case 'buyer':
			return $data['data']->user_id;
			break;
		default:
			break;
		}

		return apply_filters( 'wp_adpress_adshistory_table_column', $column_name );
	}

	public function column_buyer( $adhistory ) {
		$data = wp_adpress_get_adhistory( $adhistory->ID );
		$user_id = $data['data']->user_id;

		$user_email = '';

		if ( $user_id && $user_id > 0 ) {
			$user = get_userdata( $user_id ) ;
			$display_name = is_object( $user ) ? $user->display_name : __( 'guest', 'wp-adpress' );
		} else {
			$display_name = __( 'guest', 'wp-adpress' );
		}

		$value = '<a href="user-edit.php?user_id=' . $user_id . '">' . $display_name . '</a>';
		return apply_filters( 'wp_adpress_payments_table_column', $value, $adhistory->ID, 'buyer' );
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $payment Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	public function column_cb( $payment ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'id',
			$payment->ID
		);
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete'                 => __( 'Delete',                'wp-adpress' ),
		);

		return apply_filters( 'wp_adpress_adshistory_table_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function process_bulk_action() {
		// Get the Ids
		$ids    = isset( $_GET['id'] ) ? $_GET['id'] : false;

		// Set the action
		$action = $this->current_action();

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		if( empty( $action ) ) {
			return;
		}

		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				wp_adpress_delete_adhistory( $id );
			}

			do_action( 'wp_adpress_adshistory_table_do_bulk_action', $id, $this->current_action() );
		}

	}

	public function get_count() {
		global $wp_query;

		$args = array(
			'post_type' => 'wpad_adshistory',
			'post_status' => 'publish',
		);
		$query = new WP_Query( $args );
		$this->total_count = intval( $query->found_posts );		
	}

	/**
	 * Retrieve all the data for all the payments
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $payment_data Array of all the data for the payments
	 */
	public function adshistory_data() {

		// Query Parameters
		$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
		$per_page       = $this->per_page;
		$orderby 		= isset( $_GET['orderby'] )     ? $_GET['orderby']                           : 'ID';
		$order 			= isset( $_GET['order'] )       ? $_GET['order']                             : 'DESC';
		$order_inverse 	= $order == 'DESC'              ? 'ASC'                                      : 'DESC';
		$order_class 	= strtolower( $order_inverse );
		$search         = isset( $_GET['s'] )           ? sanitize_text_field( $_GET['s'] )          : null;	

		// Query Args Array
		$args = array(
			'post_type' => 'wpad_adshistory',
			'posts_per_page' => $per_page,
			'paged' => $page,
			'orderby' => $orderby,
			'order' => $order,
			'post_status' => 'publish',
			's' => $search,
		);

		// Return the Query posts
		$query = new WP_Query( $args );
		return $query->posts;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {

		wp_reset_vars( array( 'action', 'adhistory', 'orderby', 'order', 's' ) );

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$data     = $this->adshistory_data();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = $this->total_count;			

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,       
			'per_page'    => $this->per_page,   
			'total_pages' => ceil( $total_items / $this->per_page ),
		)
	);
	}
}
