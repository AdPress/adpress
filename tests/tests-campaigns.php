<?php

class CampaignsTest extends WP_UnitTestCase {
	/**
	 * @var array
	 */
	static $campaign_settings = array (
		'name' => 'Test Campaign',
		'description' => 'Test Campaign',
		'state' => 'active',
	);

	/**
	 * @var array
	 */
	static $campaign_ad_definition = array(
		'number' => '6',
		'price' => '20',
		'type' => 'image',
		'size' => array( 
			'height' => 120,
			'width' => 120,
		),
		'columns' => 2,
		'contract' => 'clicks',
		'clicks' => 100,
		'rotation' => 2,
	);

	/**
	 * Create Campaign Tests
	 *
	 * @return void
	 */
	function testCreateCampaign() {	
		$id = wp_adpress_insert_new_campaign( self::$campaign_settings, self::$campaign_ad_definition );

		// Assert that False is not returned
		$this->assertThat( $id, $this->logicalNot( $this->isFalse( $id ) ) );
	}

	/**
	 * Create Campaign Tests
	 *
	 * @return void
	 */
	function testGetCampaign() {
		$id = wp_adpress_insert_new_campaign( self::$campaign_settings, self::$campaign_ad_definition );

		$campaign_data = wp_adpress_get_campaign( $id );

		// Assert that the Data inserted is the same as returned
		$this->assertEquals( $campaign_data['settings'], self::$campaign_settings );
		$this->assertEquals( $campaign_data['ad_definition'], self::$campaign_ad_definition );

		$campaign_data = wp_adpress_get_campaign( 2223 );;

		// Non-existent campaign
		$this->assertFalse( $campaign_data );
	}

	/**
	 * Update Campaign Tests
	 *
	 * @return void
	 */
	function testUpdateCampaign() {

	}

	/**
	 * Activate Campaign Tests
	 *
	 * @return void
	 */
	function testActivateCampaign() {

	}

	/**
	 * Deactivate Campaign Tests
	 *
	 * @return void
	 */
	function testDeactivateCampaign() {

	}

	/**
	 * Delete Campaign Tests
	 *
	 * @return void
	 */
	function testDeleteCampaign() {

	}
}
