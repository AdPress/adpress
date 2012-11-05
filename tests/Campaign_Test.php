<?php

require_once 'F:/dev/adpressdev/wp-content/plugins/adpress/tests/test_base/init.php';
/*
 * Campain functions Tests
 */

class WP_Test_Campaign extends WP_UnitTestCase {

    public $plugin_url = 'F:/dev/adpressdev/wp-content/plugins/adpress/wp-adpress.php';
    /*
     * Campaign Parameters
     */
    static $campaign_id;
    static $settings = array(
        'name' => 'test campaign',
        'description' => 'This is the campaign description',
        'creation_time' => '1322472983',
        'state' => 'active'
    );
    static $ad_definition = array(
        'type' => 'image',
        'size' => array('width' => 200,'height' => 125 ),
        'number' => 6,
        'columns' => 2,
        'price' => 100,
        'contract' => 'clicks',
        'clicks' => 1500
    );
    /*
     * Ad Parameters
     */
    static $ad_id;
    static $ad_param = array();

    /**
     * Before Test Setup
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Before Class Init
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        $wp = new wp_adpress();
    }

    /**
     * General tests for the campaigns class
     */
    function test_campaignsClass() {
        global $wpadpress;
        $wpadpress::uninstall();
        $wpadpress->install();
        $this->assertEquals(wp_adpress_campaigns::new_campaign_id(), 1);
        $this->assertEquals(wp_adpress_campaigns::campaigns_number(), 0);
    }

    /**
     * Create a new Campaign
     * @depends test_campaignsClass
     */
    function test_createCampaign() {
        // Creates a new Empty Campaign
        $campaign = new wp_adpress_campaign(null, self::$settings, self::$ad_definition);
        $this->assertEquals($campaign->id, wp_adpress_campaigns::new_campaign_id());
        // Save the campaign to the database
        $campaign->save();
        // Check the new campaign id
        $this->assertEquals($campaign->id + 1, wp_adpress_campaigns::new_campaign_id());

        // Save the campaign id for later retrieval
        self::$campaign_id = $campaign->id;
    }

    /**
     * Load an existing Campaign
     * @depends test_createCampaign
     */
    function test_loadCampaign() {
        // Load an exisitng campaign
        $campaign = new wp_adpress_campaign(self::$campaign_id);
        $this->assertTrue($campaign->settings === self::$settings);
        $this->assertTrue($campaign->ad_definition === self::$ad_definition);
    }

    /**
     * Tests editing an existing campaign
     * @depends test_loadCampaign
     */
    function test_editCampaign() {
        // Load the campaign
        $campaign = new wp_adpress_campaign(self::$campaign_id);
        $campaign->settings['name'] = 'my campaign';
        $campaign->deactivate();
        $campaign->save();
        // Reload the campaign
        $reload = new wp_adpress_campaign(self::$campaign_id);
        $this->assertEquals($reload->settings['name'], 'my campaign');
        $this->assertEquals($reload->state(), 'inactive');
        // Reactivate the campaign
        $campaign->activate();
        $campaign->save();
    }

    /**
     * Tests creating a new ad unit
     * @depends test_editCampaign
     */
    function test_createAd() {
        // Create a new Ad Unit
        $ad = new wp_adpress_ad(null, self::$campaign_id, self::$ad_param);
        $ad->save();
        $this->assertEquals(wp_adpress_ads::new_ad_id(), $ad->id + 1);
        self::$ad_id = $ad->id;
    }

    /**
     * Tests loading an existing ad
     * @depends test_createAd
     */
    function test_loadAd() {
        // Load the Ad Unit
        $ad = new wp_adpress_ad(self::$ad_id);
        $this->assertTrue($ad->param === self::$ad_param);
        $this->assertEquals(self::$campaign_id, $ad->campaign_id);
    }

    /**
     * Tests editing an exisiting ad
     * @depends test_loadAd
     */
    function test_editAd() {
        // Load the Ad Unit
        $ad = new wp_adpress_ad(self::$ad_id);
        $ad->param = array('test' => 'test');
        $ad->campaign_id = 2;
        $ad->status = 'running';
        $ad->save();
        // Reload the Ad unit
        $reload = new wp_adpress_ad(self::$ad_id);
        $this->assertTrue($reload->param === array('test' => 'test'));
        $this->assertEquals($reload->campaign_id, 2);
    }

    /**
     * Tests removing the Ad Unit from the database
     * @depends test_editAd
     */
    function test_removeAd() {
        // Load the Ad Unit
        $ad = new wp_adpress_ad(self::$ad_id);
        $this->assertTrue($ad->destroy() != false);
    }

    /**
     * Deactivate a running campaign
     * @depends test_removeAd
     */
    function test_deactivateCampaign() {
        // Load the campaign
        $campaign = new wp_adpress_campaign(self::$campaign_id);

        // Get a random Ad unit
        $ad = new wp_adpress_ad(2);
        $ad->status = 'running';
        $ad->save();
        $this->assertFalse($campaign->deactivate());
    }

    /**
     * Tests the list ads function
     * @depends test_deactivateCampaign
     */
    function test_listAds() {
        $campaign = new wp_adpress_campaign(1);
        $this->assertEquals(count($campaign->list_ads()), 6);
       $this->assertEquals(count($campaign->list_ads('running')), 1);
        $this->assertEquals(count($campaign->list_ads('available')), 5);
    }

    /**
     * Test ListCampaigns function
     * @depends test_listAds
     */
    function test_listCampaigns() {
        $new_campaign = new wp_adpress_campaign(null);
        $new_campaign->deactivate();
        $new_campaign->save();
        $other_campaign = new wp_adpress_campaign(null);
        $other_campaign->activate();
        $other_campaign->save();
        $this->assertEquals(count(wp_adpress_campaigns::list_campaigns('active')), 2);
        $this->assertEquals(count(wp_adpress_campaigns::list_campaigns('inactive')), 1);
        $this->assertEquals(count(wp_adpress_campaigns::list_campaigns('all')), 3);
    }

    /**
     * Delete an existing Campaign
     * @depends test_listCampaigns
     */
    function test_deleteCampaign() {
        $campaign_1 = new wp_adpress_campaign(2);
        $campaign_1->remove();
        $campaign_2 = new wp_adpress_campaign(3);
        $campaign_2->deactivate();
        $campaign_2->remove();
        $this->assertEquals(count(wp_adpress_campaigns::list_campaigns('all')), 1);
    }

}

?>
