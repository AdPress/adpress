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

abstract class WPAD_Campaign
{
	/**
	 * Campaign Id
	 *
	 * @access public
	 * @var int
	 */
	public $id;

	/**
	 * Campaign Name
	 *
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Campaign Description
	 *
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * Campaign Status (Active/Inactive)
	 *
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Campaign Linked Ad Type
	 *
	 * @access public
	 * @var WPAD_Ad_Type
	 */
	public $ad_type;

	/**
	 * Campaign Linked Contract
	 *
	 * @access public
	 * @var WPAD_Contract
	 */
	public $contract;

	/**
	 * Campaign Configuration
	 *
	 * @access public
	 * @var array
	 */
	public $config = array();

	public function __construct( $id = 0 ) {

	}	
}
