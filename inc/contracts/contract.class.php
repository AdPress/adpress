<?php
/**
 * AdPress Contract abstract class
 *
 *
 * @package     Includes
 * @subpackage  Contracts	
 * @since       1.2.0
 */

// Don't load directly
if ( !defined( 'ABSPATH') ) {
	die( '-1' );
}

abstract class WPAD_Contract extends WPAD_Post_API
{
	/**
	 * Post Type id
	 *
	 * @var string
	 */
	public $post_type = 'wp_adpress_contracts';
}
