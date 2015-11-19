<?php
/**
 * AdPress Campaign abstract class
 *
 * This class provides a global wrapper for the AdPress Campaign representation 
 *
 * @package		Includes
 * @subpackage  Campaigns	
 * @copyright	Copyright (c) 2014, Abid Omar 
 * @since		1.2.0
 */

// Don't load directly
if ( !defined( 'ABSPATH') ) {
	die( '-1' );
}

abstract class WPAD_Campaign extends WPAD_Post_API
{	
	/**
	 * Post Type id
	 *
	 * @var string
	 */
	public $post_type = 'wp_adpress_campaigns';

}
