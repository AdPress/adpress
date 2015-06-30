<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

if (!class_exists('wp_adpress_history')) {
	/**
	 * History Manager
	 *
	 * @package Includes
	 * @subpackage Settings
	 */
	class wp_adpress_history
	{

		/**
		 * History record id
		 * @var integer
		 */
		public $id;

		/**
		 * Ad Id
		 * @var integer
		 */
		public $ad_id;

		/**
		 * Campaign object
		 * @var object
		 */
		public $campaign;

		/**
		 * Ad Object
		 * @var object
		 */
		public $ad;

		/**
		 * Ad Approval time
		 * @var timestamp
		 */
		public $approved_at;

		/**
		 * Ad Expiry time
		 * @var timestamp
		 */
		public $expired_at;

		/**
		 * Create a new Ad History object
		 * @param integer $id
		 */
		function __construct( $id )
		{
			$this->populate_object($this->query_table($id));
		}

		/**
		 * Query the history table
		 * @param integer $id
		 * @return array
		 */
		private function query_table($id)
		{
			global $wpdb;
			$query = 'SELECT * FROM ' . self::history_table() . ';';
			$arr = $wpdb->get_row($query, ARRAY_A, $id - 1);
			return $arr;
		}

		/**
		 * Populate the History Object from the query
		 * @param array $query
		 */
		private function populate_object($query)
		{
			$settings = get_option('adpress_settings');
			$time_format = $settings['time_format'];
			$this->id = $query['id'];
			$this->ad_id = $query['ad_id'];
			$this->ad = unserialize($query['ad_info']);
			$this->campaign = unserialize($query['campaign_info']);
			$this->approved_at = date($time_format, $query['approved_at']);
			$this->expired_at = date($time_format, $query['expired_at']);
		}

		/**
		 * Records the history of an Ad
		 * @global object $wpdb
		 * @param object $ad
		 */
		static function record_history( $ad )
		{
			wp_adpress_insert_adhistory( $ad );	
		}


		static function empty_history()
		{
			global $wpdb;
			$query = $wpdb->query('TRUNCATE TABLE ' . self::history_table() . ';');
			return $query;
		}

		/**
		 * Return a new history id
		 * @global object $wpdb
		 * @return integer
		 */
		static function new_history_id()
		{
			global $wpdb;
			$max_id = $wpdb->get_var('SELECT MAX(id) FROM ' . self::history_table() . ' WHERE id is not null;');
			if ($max_id === NULL) {
				return 1;
			}
			$new_id = (int)$max_id + 1;
			return $new_id;
		}

		/**
		 * Returns the name of the history table
		 * @return string
		 */
		static function history_table()
		{
			global $wpdb;
			return $wpdb->prefix . 'adpress_history';
		}

		static function list_history()
		{
			global $wpdb;
			// Query
			$arr = array();
			$query = 'SELECT id FROM ' . self::history_table() . ';';
			$result = $wpdb->get_col($query);
			// History Objects
			for ($i = 0; $i < count($result); $i++) {
				$arr[] = new wp_adpress_history((int)$result[$i]);
			}
			// Return the array
			return $arr;
		}
	}
}

