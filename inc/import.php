<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
/**
 * Import/Export Feature
 *
 * @package Includes
 * @subpackge Settings
 */
if (!class_exists('wp_adpress_import')) {
    /**
     * Import/Export Class
     */
    class wp_adpress_import
    {
        /**
         * Message displayed to the user. Null by default
         * @var string
         */
        static $message = '';

        /**
         * Export Data function
         *
         * @static
         * @param $settings_data
         * @param $campaign_data
         * @param $history_data
         * @return string
         */
        static function export($settings_data, $campaign_data, $history_data)
        {
            $export = array();
            if ($settings_data) {
                $export['settings_data'] = array(
                    'general' => get_option('adpress_settings'),
                    'image' => get_option('adpress_image_settings'),
                    'link' => get_option('adpress_link_settings')
                );
            }
            if ($campaign_data) {
                $export['campaign_data'] = array(
                    'campaigns' => wp_adpress_campaigns::list_campaigns(),
                    'ads' => wp_adpress_ads::list_ads()
                );
            }
            if ($history_data) {
                $export['history_data'] = wp_adpress_history::list_history();
            }
            $export = base64_encode(serialize($export));
            return $export;
        }

        /**
         * Import Settings from file
         * @static
         * @param $settings_data
         * @param $campaign_data
         * @param $history_data
         * @param $data
         */
        static function import($settings_data, $campaign_data, $history_data, $data)
        {
            $import = unserialize(base64_decode($data));
            if ($settings_data) {
                delete_option('adpress_settings');
                delete_option('adpress_image_settings');
                delete_option('adpress_link_settings');
                add_option('adpress_settings', $import['settings_data']['general']);
                add_option('adpress_image_settings', $import['settings_data']['image']);
                add_option('adpress_link_settings', $import['settings_data']['link']);
            }
            if ($campaign_data) {
                // Remove all campaigns
                wp_adpress_campaigns::empty_campaigns();
                // Add back-up campaigns
                $campaigns = $import['campaign_data']['campaigns'];
                for ($i = 0; $i < count($campaigns); $i++) {
                    $campaign = $campaigns[$i];
                    $campaign->unlock();
                    $campaign->save();
                }
                // Remove all ads
                wp_adpress_ads::empty_ads();
                // Add back-up ads
                $ads = $import['campaign_data']['ads'];
                for ($i = 0; $i < count($ads); $i++) {
                    $ad = $ads[$i];
                    $ad->unlock();
                    $ad->save();
                }
            }
            if ($history_data) {
                //TODO: missing?
            }
        }

        /**
         * Reset the plug-in
         * @static
         * @param $settings_data boolean
         * @param $campaign_data boolean
         * @param $history_data boolean
         */
        static function reset($settings_data, $campaign_data, $history_data)
        {
            if ($settings_data) {
                delete_option('adpress_settings');
                delete_option('adpress_image_settings');
                delete_option('adpress_link_settings');
                add_option('adpress_settings', wp_adpress_install::$defaults);
                add_option('adpress_image_settings', wp_adpress_install::$img_defaults);
                add_option('adpress_link_settings', wp_adpress_install::$link_defaults);
            }
            if ($campaign_data) {
                wp_adpress_campaigns::empty_campaigns();
                wp_adpress_ads::empty_ads();
            }
            if ($history_data) {
                wp_adpress_history::empty_history();
            }
        }

        /**
         * Process user actions
         * @static
         * @param $action
         */
        static function process_action($action)
        {
            if (isset($action)) {
                switch ($action) {
                    case 'export':
                        if (!isset($_GET['settings_data']) && !isset($_GET['campaign_data']) && !isset($_GET['history_data'])) {
                            self::display_message('No Selection', 'Select Data to Export', 'adpress-icon-request_sent');
                        } else {
                            $data = self::export(isset($_GET['settings_data']), isset($_GET['campaign_data']), isset($_GET['history_data']));
                            $filename = ADPRESS_DIR . '/export.adf';
                            $handle = fopen($filename, 'w');
                            fwrite($handle, $data);
                            fclose($handle);
                            self::display_message('Settings Exported', 'Your settings were exported successfully. You can now <a href="' . ADPRESS_URLPATH . 'export.adf">download</a> your settings file.', 'adpress-icon-request_sent');
                        }
                        break;
                    case 'import':
                        if (!isset($_FILES['import_file']['tmp_name']) && !isset($_POST['settings_data']) && !isset($_POST['campaign_data']) && !isset($_POST['history_data'])) {
                            self::display_message('No Selection', 'Select Data to Import and the import file.', 'adpress-icon-request_sent');
                        } else {
                            $filename = $_FILES['import_file']['tmp_name'];
                            $handle = fopen($filename, 'r');
                            $contents = fread($handle, filesize($filename));
                            fclose($handle);
                            self::import(isset($_POST['settings_data']), isset($_POST['campaign_data']), isset($_POST['history_data']), $contents);
                            self::display_message('Settings Exported', 'Your settings were imported successfully.', 'adpress-icon-request_sent');
                        }
                        break;
                    case 'reset':
                        self::reset(isset($_GET['settings_data']), isset($_GET['campaign_data']), isset($_GET['history_data']));
                        break;
                }
            }
        }

        //TODO Seperate the view
        /**
         * Display a message to the user
         * @static
         * @param $title
         * @param $content
         * @param $icon
         */
        static function display_message($title, $content, $icon)
        {
            self::$message = '
        <div class="c-block" style="width: 475px;">
                    <div class="c-head">
                        <h3 id="' . $icon . '">' . $title . '</h3>
                    </div>
                    <p>' . $content . '</p>
         </div>
        ';
        }

        /**
         * Display Import/Export Page
         * @static
         */
        static function display_page()
        {
            if (isset($_GET['action'])) {
                self::process_action($_GET['action']);
            } elseif (isset($_POST['action'])) {
                self::process_action($_POST['action']);
            }


            $url = $_SERVER['SCRIPT_URI'];
            $message = self::$message;
            print <<<html
$message
<div class="c-block" style="width: 565px;">
            <div class="c-head">
                <h3>Export</h3>
            </div>
            <form method="GET" action="$url">
            <table class="form-table">
                <tbody>
                    <tr valign="top"><th scope="row"><label for="settings_data_1">Settings Data</label></th><td><input type="checkbox" name="settings_data" id="settings_data_1"></td></tr>
                    <tr valign="top"><th scope="row"><label for="campaign_data_1">Campaign Data</label></th><td><input type="checkbox" name="campaign_data" id="campaign_data_1"></td></tr>
                    <tr valign="top"><th scope="row"><label for="history_data_1">History Data</label></th><td><input type="checkbox" name="history_data" id="history_data_1"></td></tr>
                    <tr valign="top"><th scope="row">Export File</th><td><input type="submit" name="export_btn" class="button-secondary" value="Export" /></td></tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="export" />
            <input type="hidden" name="page" value="adpress-settings" />
            <input type="hidden" name="tab" value="import" />
            </form>
</div>
<div class="c-block" style="width: 565px;">
            <div class="c-head">
                <h3>Import</h3>
            </div>
            <form method="POST" action="$url" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr valign="top"><th scope="row"><label for="settings_data_2">Settings Data</label></th><td><input type="checkbox" name="settings_data" id="settings_data_2"></td></tr>
                    <tr valign="top"><th scope="row"><label for="campaign_data_2">Campaign Data</label></th><td><input type="checkbox" name="campaign_data" id="campaign_data_2"></td></tr>
                    <tr valign="top"><th scope="row"><label for="history_data_2">History Data</label></th><td><input type="checkbox" name="history_data" id="history_data_2"></td></tr>
                </tbody>
            </table>
            <table class="form-table">
                <tbody>
                    <tr valign="top"><td><input class="button-secondary" type="file" name="import_file"/></td></tr>
                    <tr valign="top"><td><input type="submit" name="import_btn" class="button-secondary" value="Import" /></td></tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="import" />
            <input type="hidden" name="page" value="adpress-settings" />
            <input type="hidden" name="tab" value="import" />
            </form>
</div>
<div class="c-block" style="width: 565px;">
            <div class="c-head">
                <h3>Reset Plug-in</h3>
            </div>
            <form method="GET" action="$url">
            <table class="form-table">
                <tbody>
                    <tr valign="top"><th scope="row"><label for="settings_data_3">Settings Data</label></th><td><input type="checkbox" name="settings_data" id="settings_data_3"></td></tr>
                    <tr valign="top"><th scope="row"><label for="campaign_data_3">Campaign Data</label></th><td><input type="checkbox" name="campaign_data" id="campaign_data_3"></td></tr>
                    <tr valign="top"><th scope="row"><label for="history_data_3">History Data</label></th><td><input type="checkbox" name="history_data" id="history_data_3"></td></tr>
                    <tr valign="top"><th scope="row">Reset</th><td><input type="submit" name="reset_btn" class="button-secondary" value="Reset" /></td></tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="reset" />
            <input type="hidden" name="page" value="adpress-settings" />
            <input type="hidden" name="tab" value="import" />
            </form>            
</div>
html;

        }
    }
}
?>