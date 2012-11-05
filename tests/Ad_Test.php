<?php

require_once 'F:/dev/adpressdev/wp-content/plugins/adpress/tests/test_base/init.php';
/*
 * Campain functions Tests
 */

class WP_Test_Ad extends WP_UnitTestCase
{

    public $plugin_url = 'F:/dev/adpressdev/wp-content/plugins/adpress/wp-adpress.php';
    static $campaign_id = 1;
    static $ads = array(1, 3, 4, 5);
    static $param1 = array(
        'image_link' => 'http://localhost/devpress/wp-content/uploads/2011/12/20344-1270041223.jpg',
        'url' => 'http://codecanyon.com'
    );
    static $param2 = array(
        'image_link' => 'http://localhost/devpress/wp-content/uploads/2011/12/69296-1311089365.jpg',
        'url' => 'http://themeforest.net'
    );
    static $param3 = array(
        'image_link' => 'http://localhost/devpress/wp-content/uploads/2011/12/68911-1310748714.jpg',
        'url' => 'http://activeden.net'
    );
    static $param4 = array(
        'http://bad_url',
        'url' => 'http://activeden.net'
    );

    /**
     * Before Test Setup
     */
    function setUp()
    {
        parent::setUp();
    }

    /**
     * Before Class Init
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $wp = new wp_adpress();
    }

    /**
     * Tests the register Ad function
     */
    function test_registerAd()
    {
        // Set the current user to buyer
        wp_set_current_user(2);
        $campaign = new wp_adpress_campaign(self::$campaign_id);
        /* Register a new Ad */
        $campaign->register_ad(self::$param1);
        $campaign->register_ad(self::$param2);
        $campaign->register_ad(self::$param3);
        $campaign->register_ad(self::$param4);
    }

    /**
     * Tests the unregiter Ad function
     * @depends test_registerAd
     */
    function test_unregisterAd()
    {
        $ad = new wp_adpress_ad(2);
        $ad->unregister_ad();
        $ad->save();
    }

    /**
     * Tests Ad Approving
     * @depends test_unregisterAd
     */
    function test_approveAd()
    {
        $ad1 = new wp_adpress_ad(self::$ads[0]);
        $ad1->approve();
        $ad1->save();
        $ad2 = new wp_adpress_ad(self::$ads[1]);
        $ad2->approve();
        $ad2->save();
        $ad3 = new wp_adpress_ad(self::$ads[2]);
        $ad3->approve();
        $ad3->save();
    }

    /**
     * Tests Ad rejection
     * @depends test_approveAd
     */
    function test_rejectAd()
    {
        $ad = new wp_adpress_ad(self::$ads[3]);
        $ad->reject();
        $ad->save();
    }

    /**
     * This function populates the first ad with random stats
     * @depends test_rejectAd
     */
    function test_AdStats()
    {
        $stats = array(
            'views' => array(
                '20111201' => 230,
                '20111202' => 250,
                '20111203' => 255,
                '20111204' => 240,
                '20111205' => 293,
                '20111206' => 247,
                '20111207' => 265,
                '20111208' => 255,
                '20111209' => 274,
                '20111210' => 254,
                '20111211' => 233,
                '20111212' => 240,
                '20111213' => 247,
                '20111214' => 255,
                '20111215' => 257,
                '20111216' => 260,
                '20111217' => 282,
                '20111218' => 304,
                '20111219' => 301,
                '20111220' => 257,
                '20111221' => 255,
                '20111222' => 269,
                '20111223' => 270,
                '20111224' => 274,
                '20111225' => 247,
                '20111226' => 265,
                '20111227' => 254,
                '20111228' => 236,
                '20111229' => 223,
                '20111230' => 240,
                '20111231' => 270,
                '20120101' => 230,
                '20120102' => 250,
                '20120103' => 255,
                '20120104' => 240,
                '20120105' => 293,
                '20120106' => 247,
                '20120107' => 265,
                '20120108' => 255,
                '20120109' => 274,
                '20120110' => 254,
                '20120111' => 233,
                '20120112' => 240,
                '20120113' => 247,
                '20120114' => 255,
                '20120115' => 257,
                '20120116' => 260,
                '20120117' => 282,
                '20120118' => 304,
                '20120119' => 301,
                '20120120' => 257,
                '20120121' => 255,
                '20120122' => 269,
                '20120123' => 270,
                '20120124' => 274,
                '20120125' => 247,
                '20120126' => 265,
                '20120127' => 254,
                '20120128' => 236,
                '20120129' => 223,
                '20120130' => 240,
                '20120131' => 270
            ),
            'hits' => array(
                '20111201' => 23,
                '20111202' => 30,
                '20111203' => 29,
                '20111204' => 21,
                '20111205' => 22,
                '20111206' => 29,
                '20111207' => 24,
                '20111208' => 21,
                '20111209' => 21,
                '20111210' => 23,
                '20111211' => 18,
                '20111212' => 7,
                '20111213' => 21,
                '20111214' => 29,
                '20111215' => 26,
                '20111216' => 26,
                '20111217' => 30,
                '20111218' => 36,
                '20111219' => 31,
                '20111220' => 21,
                '20111221' => 21,
                '20111222' => 22,
                '20111223' => 23,
                '20111224' => 20,
                '20111225' => 29,
                '20111226' => 25,
                '20111227' => 21,
                '20111228' => 20,
                '20111229' => 29,
                '20111230' => 21,
                '20111231' => 20,
                '20120101' => 23,
                '20120102' => 30,
                '20120103' => 29,
                '20120104' => 21,
                '20120105' => 22,
                '20120106' => 29,
                '20120107' => 24,
                '20120108' => 21,
                '20120109' => 21,
                '20120110' => 23,
                '20120111' => 18,
                '20120112' => 7,
                '20120113' => 21,
                '20120114' => 29,
                '20120115' => 26,
                '20120116' => 26,
                '20120117' => 30,
                '20120118' => 36,
                '20120119' => 31,
                '20120120' => 21,
                '20120121' => 21,
                '20120122' => 22,
                '20120123' => 23,
                '20120124' => 20,
                '20120125' => 29,
                '20120126' => 25,
                '20120127' => 21,
                '20120128' => 20,
                '20120129' => 29,
                '20120130' => 21,
                '20120131' => 20
            )
        );
        $ad = new wp_adpress_ad(self::$ads[0]);
        $ad->stats = $stats;
        $ad->save();
    }


}

?>
