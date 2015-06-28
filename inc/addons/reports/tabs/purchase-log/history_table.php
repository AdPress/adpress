<?php
/**
 * Payment History Table Class
 *
 * @package     Includes
 * @subpackage  Payments
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
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
 * wp_adpress_Payment_History_Table Class
 *
 * Renders the Payment History table on the Payment History page
 *
 */
class wp_adpress_Payment_History_Table extends WP_List_Table {

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
	 * Total number of complete payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $complete_count;

	/**
	 * Total number of pending payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $pending_count;

	/**
	 * Total number of refunded payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $refunded_count;

	/**
	 * Total number of failed payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $failed_count;

	/**
	 * Total number of cancelled payments
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $cancelled_count;

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

		$this->get_payment_counts();
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=adpress-reports' );
	}

	public function advanced_filters() {
		//$start_date = isset( $_GET['start-date'] )  ? sanitize_text_field( $_GET['start-date'] ) : null;
		//$end_date   = isset( $_GET['end-date'] )    ? sanitize_text_field( $_GET['end-date'] )   : null;
		$status     = isset( $_GET['status'] )      ? $_GET['status'] : '';
?>
	<div id="wp-adpress-payment-filters">
	<?php $this->search_box( __( 'Search', 'wp-adpress' ), 'wp-adpress-payments' ); ?>
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
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count . ')</span>';
		$complete_count = '&nbsp;<span class="count">(' . $this->complete_count . ')</span>';
		$refunded_count = '&nbsp;<span class="count">(' . $this->refunded_count . ')</span>';
		$failed_count   = '&nbsp;<span class="count">(' . $this->failed_count   . ')</span>';
		$cancelled_count   = '&nbsp;<span class="count">(' . $this->cancelled_count   . ')</span>';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'wp-adpress') . $total_count ),
			'publish'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'publish', 'paged' => FALSE ) ), $current === 'publish' ? ' class="current"' : '', __('Completed', 'wp-adpress') . $complete_count ),
			'pending'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'pending', 'paged' => FALSE ) ), $current === 'pending' ? ' class="current"' : '', __('Pending', 'wp-adpress') . $pending_count ),
			'refunded'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'refunded', 'paged' => FALSE ) ), $current === 'refunded' ? ' class="current"' : '', __('Refunded', 'wp-adpress') . $refunded_count ),
			'failed'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'failed', 'paged' => FALSE ) ), $current === 'failed' ? ' class="current"' : '', __('Failed', 'wp-adpress') . $failed_count ),
			'cancelled'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'cancelled', 'paged' => FALSE ) ), $current === 'cancelled' ? ' class="current"' : '', __('Cancelled', 'wp-adpress') . $cancelled_count ),
		);

		return apply_filters( 'wp_adpress_payments_table_views', $views );
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
			'ID'     	=> __( 'ID', 'wp-adpress' ),
			'email'  	=> __( 'Email', 'wp-adpress' ),
			'details'  	=> __( 'Details', 'wp-adpress' ),
			'amount'  	=> __( 'Amount', 'wp-adpress' ),
			'date'  	=> __( 'Date', 'wp-adpress' ),
			'user'  	=> __( 'User', 'wp-adpress' ),
			'status'  	=> __( 'Status', 'wp-adpress' ),
		);

		return apply_filters( 'wp_adpress_payments_table_columns', $columns );
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
			'ID' 		=> array( 'ID', true ),
			'amount' 	=> array( 'amount', false ),
			'date' 		=> array( 'date', false ),
		);
		return apply_filters( 'wp_adpress_payments_table_sortable_columns', $columns );
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
	public function column_default( $payment, $column_name ) {

		$ID = $payment->ID;

		switch ( $column_name ) {
		case 'amount' :
			$value   = intval( get_post_meta( $ID, 'wpad_payment_total', true ) );
			break;
		case 'date' :
			$date    = strtotime( $payment->post_date );
			$value   = date_i18n( get_option( 'date_format' ), $date );
			break;
		case 'status' :
			$value   = wp_adpress_get_payment_status( $payment, true );
			break;
		case 'details' :
			$value = '<a href="' . add_query_arg( 'id', $payment->ID, admin_url( 'admin.php?page=adpress-reports&view=view-order-details' ) ) . '">' . __( 'View Order Details', 'wp-adpress' ) . '</a>';
			break;
		default:
			$value = isset( $payment->$column_name ) ? $payment->$column_name : '';
			break;

		}
		return apply_filters( 'wp_adpress_payments_table_column', $value, $payment->ID, $column_name );
	}

	/**
	 * Render the Email Column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $payment Contains all the data of the payment
	 * @return string Data shown in the Email column
	 */
	public function column_email( $payment ) {

		$user_email = get_post_meta( $payment->ID, 'wpad_payment_user_email', true );

		$row_actions = array();

		$row_actions['edit'] = '<a href="' . add_query_arg( array( 'view' => 'view-order-details', 'id' => $payment->ID, 'action' => 'edit' ), $this->base_url ) . '">' . __( 'View', 'wp-adpress' ) . '</a>';

		$row_actions['delete'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'payment' => $payment->ID ), $this->base_url ), 'wp_adpress_payment_nonce') . '">' . __( 'Delete', 'wp-adpress' ) . '</a>';

		$row_actions = apply_filters( 'wp_adpress_payment_row_actions', $row_actions, $payment );

		if ( ! isset( $user_email ) ) {
			$user_email = __( '(unknown)', 'wp-adpress' );
		}

		$value = $user_email . $this->row_actions( $row_actions );

		return apply_filters( 'wp_adpress_payments_table_column', $value, $payment->ID, 'email' );
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
			'payment',
			$payment->ID
		);
	}

	/**
	 * Render the User Column
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $payment Contains all the data of the payment
	 * @return string Data shown in the User column
	 */
	public function column_user( $payment ) {

		$user_id = wp_adpress_get_payment_user_id( $payment->ID );

		$user_email = get_post_meta( $payment->ID, 'wpad_payment_user_email', true );

		if ( $user_id && $user_id > 0 ) {
			$user = get_userdata( $user_id ) ;
			$display_name = is_object( $user ) ? $user->display_name : __( 'guest', 'wp-adpress' );
		} else {
			$display_name = __( 'guest', 'wp-adpress' );
		}

		$value = '<a href="' . esc_url( add_query_arg( array( 'user' => $user_email, 'paged' => false ) ) ) . '">' . $display_name . '</a>';
		return apply_filters( 'wp_adpress_payments_table_column', $value, $payment->ID, 'user' );
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
			'set-status-publish'     => __( 'Set To Completed',      'wp-adpress' ),
			'set-status-refunded'    => __( 'Set To Refunded',       'wp-adpress' ),
			'set-status-failed'      => __( 'Set To Failed',         'wp-adpress' ),
			'set-status-cancelled'      => __( 'Set To Cancelled',         'wp-adpress' ),
		);

		return apply_filters( 'wp_adpress_payments_table_bulk_actions', $actions );
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
		$ids    = isset( $_GET['payment'] ) ? $_GET['payment'] : false;

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
				wp_adpress_delete_payment( $id );
			}

			if ( 'set-status-publish' === $this->current_action() ) {
				wp_adpress_update_payment_status( $id, 'publish' );
			}

			if ( 'set-status-refunded' === $this->current_action() ) {
				wp_adpress_update_payment_status( $id, 'refunded' );
			}

			if ( 'set-status-failed' === $this->current_action() ) {
				wp_adpress_update_payment_status( $id, 'failed' );
			} 

			if ( 'set-status-cancelled' === $this->current_action() ) {
				wp_adpress_update_payment_status( $id, 'cancelled' );
			}

			do_action( 'wp_adpress_payments_table_do_bulk_action', $id, $this->current_action() );
		}

	}

	/**
	 * Retrieve the payment counts
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function get_payment_counts() {

		global $wp_query;

		$args = array('post_type' => 'wp_adpress_payments');

		if( isset( $_GET['user'] ) ) {
			$args['author'] = get_user_by('id',  urldecode( $_GET['user'] ));
		} elseif( isset( $_GET['s'] ) ) {
			$args['s'] = urldecode( $_GET['s'] );
		}


		$query = new WP_Query( $args );
		$this->total_count = intval( $query->found_posts );

		// Published payments count
		$args['post_status'] = array( 'publish' );
		$query = new WP_Query( $args );
		$this->complete_count = intval( $query->found_posts );

		// Published payments count
		$args['post_status'] = array( 'pending' );
		$query = new WP_Query( $args );
		$this->pending_count = intval( $query->found_posts );

		// Refunded payments count
		$args['post_status'] = array( 'refunded' );
		$query = new WP_Query( $args );
		$this->refunded_count = intval( $query->found_posts );

		// Failed payments count
		$args['post_status'] = array( 'failed' );
		$query = new WP_Query( $args );	
		$this->failed_count  = intval( $query->found_posts );

		// Cancelled payments count
		$args['post_status'] = array( 'cancelled' );
		$query = new WP_Query( $args );	
		$this->cancelled_count  = intval( $query->found_posts );
	}

	/**
	 * Retrieve all the data for all the payments
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $payment_data Array of all the data for the payments
	 */
	public function payments_data() {

		// Query Parameters
		$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
		$per_page       = $this->per_page;
		$orderby 		= isset( $_GET['orderby'] )     ? $_GET['orderby']                           : 'ID';
		$order 			= isset( $_GET['order'] )       ? $_GET['order']                             : 'DESC';
		$order_inverse 	= $order == 'DESC'              ? 'ASC'                                      : 'DESC';
		$order_class 	= strtolower( $order_inverse );
		$user_email		= isset( $_GET['user'] )        ? $_GET['user']                              : null;
		$status 		= isset( $_GET['status'] )      ? $_GET['status']                            : 'any';
		$search         = isset( $_GET['s'] )           ? sanitize_text_field( $_GET['s'] )          : null;

		// Fetch the user ID
		$user = get_user_by( 'email', $user_email );
		if ($user) {
			$user_id = $user->ID;
		} else {
			$user_id = 0;
		}


		// Query Args Array
		$args = array(
			'post_type' => 'wp_adpress_payments',
			'posts_per_page' => $per_page,
			'paged' => $page,
			'orderby' => $orderby,
			'order' => $order,
			'author' => $user_id,
			'post_status' => $status,
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

		wp_reset_vars( array( 'action', 'payment', 'orderby', 'order', 's' ) );

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$data     = $this->payments_data();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		$this->_column_headers = array( $columns, $hidden, $sortable );

		switch ( $status ) {
		case 'publish':
			$total_items = $this->complete_count;
			break;
		case 'pending':
			$total_items = $this->pending_count;
			break;
		case 'refunded':
			$total_items = $this->refunded_count;
			break;
		case 'failed':
			$total_items = $this->failed_count;
			break;
		case 'cancelled':
			$total_items = $this->cancelled_count;
			break;
		case 'any':
			$total_items = $this->total_count;
			break;
		default:	
			$count       = wp_count_posts( 'wp_adpress_payments' );
			$total_items = $count->{$status};
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  	// WE have to calculate the total number of items
			'per_page'    => $this->per_page,                     	// WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $this->per_page )   // WE have to calculate the total number of pages
		)
	);
	}
}
