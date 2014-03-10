<?php
/**
 * Misc Functions
 *
 * @package     Includes 
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Abid Omar 
 * @since       0.98 
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * Retrieve the time zone id
 * @since 0.98
 * @return string $time_zone the time zone id
 */
function wp_adpress_get_timezone_id() {
// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set return UTC
	if ( ! ( $utc_offset = 3600 * get_option( 'gmt_offset', 0 ) ) ) {
		return 'UTC';
	}

	// attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr( '', $utc_offset );

	// last try, guess timezone string manually
	if ( $timezone === false ) {

		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst &&  $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}
	}

	// fallback
	return 'UTC';
}
