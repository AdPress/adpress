<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
/**
 * Licensing System
 *
 * The licensing system has two parts: The first checks that the license is correct
 * and displays the notification accordingly. The second enables auto-updates.
 *
 *
 * @package Includes
 * @subpackage License
 */

if (!class_exists('wp_adpress_license')) {
    /**
     * License Class
     */
    class wp_adpress_license
    {
        function __construct()
        {

        }

        /**
         * Checks a username and license key validity. Return true if valid.
         *
         * @param $username
         * @param $license_key
         * @return bool
         */
        static function check_license($username, $license_key)
        {
            $request = wp_remote_get('http://marketplace.envato.com/api/edge/omarabid/6q1tgwuobif35kcdu58uyk1lyd4ejzqj/verify-purchase:' . $license_key . '.json');

            // Check that the response is valid
            if (is_wp_error($request)) {
                return false;
            }

            // Decode the response
            $result = json_decode($request['body'], ARRAY_A);

            // Check the license
            if (isset($result['verify-purchase'])) {
                $buyer = $result['verify-purchase']['buyer'];
                if (strtolower($buyer) === strtolower($username)) {
                    return true;
                }
                return false;
            }
            return false;
        }
    }
}