<?php
/**
 * AdPress Payment Gateway abstract class
 *
 * This class provides a global wrapper for the AdPress gateways
 *
 * @package		Includes
 * @subpackage	Gateways
 * @copyright	Copyright (c) 2014, Abid Omar 
 * @since		1.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

abstract class WPAD_Payment_Gateway
{
	/**
	 * Payment gateway settings 
	 *
	 * @access public
	 * @var string
	 */
	public $settings;

	/**
	 * Purchase Log Object
	 *
	 * @access public
	 * @var string
	 */
	public $purchase_log;

	/**
	 * Purchase Data. (Data about the Ad that the user purchased)
	 *
	 * @access public
	 * @var string
	 */
	public $checkout_data;

	/**
	 * Currency Code
	 *
	 * @access public
	 * @var string
	 */
	public $currency_code;

	/**
	 * Payment gateway constructor. Should use WPAD_Payment_Gateways::get( $gateway_name ) instead
	 * to get an instance of the gateway.
	 *
	 * @access public
	 * @return WPAD_Payment_Gateway
	 */
	public function __construct() {
		// Register Gateway		
		add_filter( 'wpad_payment_gateways', array( &$this, 'register_gateway' ) );

		// Register Gateway settings
		add_action( 'admin_init', array( &$this, 'setup_settings_form' ) ); 
	}

	public function register_gateway( $gateways ) {
		$gateways[$this->settings['id']] = $this->settings;

		return $gateways;
	}

	/**
	 * Display the payment gateway settings form as seen from
	 * the gateways settings area.
	 *
	 * @abstract
	 * @access public
	 * @since 1.0
	 * 
	 * @return void
	 */
	public function setup_form() {
?>
<form action="options.php" method="POST">
<?php settings_fields('adpress_gateway_' . $this->settings['id'] . '_settings'); ?>

			<div class="c-block" style="width: 650px;">
				<div class="c-head">
					<?php do_settings_sections('adpress_gateway_' . $this->settings['id'] . '_form_general'); ?>
				</div>
							 <p class="submit">
								<input name="Submit" type="submit" class="button-primary"
									   value="<?php esc_attr_e('Save Changes'); ?>"/>
							</p>
</form>
<?php
	}

	abstract public function setup_settings_form();

	/**
	 * Process and send payment details to payment gateways
	 *
	 * @abstract
	 * @access public
	 * @since 1.0
	 *
	 * @return void
	 */
	abstract public function process();

	/**
	 * Returns the HTML of the logo of the payment gateway.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @return mixed False if there's no html defined.
	 */
	public function get_button_html() {
		return false;
	}
}
