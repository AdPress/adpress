<?php
/**
 * An Abstract class for handling WordPress Custom Post Types 
 *
 * @class       WPAD_Posts_API 
 * @version     1.2.0
 * @package     Inc/Abstracts
 */
abstract class WPAD_Post_API 
{
	/**
	 * The post ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Post Type id
	 *
	 * @var string
	 */
	public $post_type = 'wpad';

	public function __construct( $id = null, $parent_id = null, $post_status = 'publish', $post_title = null ) {
		if ( $id ) {
			$this->id = $id;
		} else {
			$this->insert_new_post( $parent_id );
		}
	}

	public function insert_new_post( $parent_id, $post_status, $post_title ) {
		// Make sure the campaign is inserted with the correct timezone
		date_default_timezone_set( wp_adpress_get_timezone_id() );

		// Post Args
		$args = array (
			'post_title'    => $post_title,
			'post_status'   => $post_status,
			'post_type'     => $this->post_type,
			'post_parent'   =>  $parent_id,
			'post_date'     =>  null,
			'post_date_gmt' =>  null,
		);

		// Insert the Campaign Post
		$campaign = wp_insert_post( $args );
	}

	/**
	 * __get function.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function __get( $key ) {
		// post must exist first
		if ( $this->exists() ) {
			return false;
		}

		$value = get_post_meta( $this->id, $this->post_type . '_' . $key, true );		

		return $value;
	}

	/**
	 * __set function.
	 *
	 * @param string $key
	 * @param string $value
	 * @return void 
	 */
	public function __set( $key, $value ) {
		// post must exist first
		if ( $this->exists() ) {
			return false;
		}

		update_post_meta( $this->id, $this->post_type . '_' . $key, $value );
	}

	/**
	 * __isset function.
	 *
	 * @param string $key
	 * @return bool 
	 */
	public function __isset( $key ) {
		// post must exist first
		if ( $this->exists() ) {
			return false;
		}

		return metadata_exists( 'post', $this->id, $this->post_type . '_' . $key );

	}

	/**
	 * Returns whether or not the post exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return wp_adpress_post_exists( $this->id );
	}
}
