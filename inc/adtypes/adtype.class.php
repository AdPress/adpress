<?php
/**
 * AdPress Ad Type abstract class
 *
 *
 * @package		Includes
 * @subpackage  Ad Type	
 * @since		1.2.0
 */

// Don't load directly
if ( !defined( 'ABSPATH') ) {
	die( '-1' );
}

abstract class WPAD_Adtype extends WPAD_Post_API
{
	/**
	 * Post Type id
	 *
	 * @var string
	 */
	public $post_type = 'wp_adpress_adtypes';

}
