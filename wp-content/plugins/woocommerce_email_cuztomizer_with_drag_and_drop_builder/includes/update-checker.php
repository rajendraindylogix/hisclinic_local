<?php
/**
 *  WooCommerce Email Customizer with Drag and Drop Email Builder
 * Class UpdateChecker
 * @author Flycart Technologies LLP
 * @license GNU GPL V3 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

class UpdateChecker
{
    /**
     * Current Version of the Plugin [Local].
     * @var string
     */
    public $active_version = WOO_ECPB_VERSION;

    /**
     * For Holding the Active Version.
     * @var string
     */
    public $new_version;

    /**
     * Default URI for Checking Updates.
     * @var string
     */
    private $official_updates = 'http://demo.flycart.org/wpplugins.json';

    /**
     * URL to Update the Plugin.
     * @var string
     */
    private $update_url;

    /** Update Info.
     * @var mixed
     */
    private $updates;

    /**
     * Active Plugin File.
     * @var
     */
    private $plugin_file;

    /**
     * General Update Notice.
     * @var
     */
    private $update_notice;

    /**
     * Message for the User on Updating.
     * @var
     */
    private $update_message;

    /**
     * UpdateChecker constructor for Init checking process.
     *
     * @param string $file Current Plugin File.
     * @param string $domain Name Domain of the Plugin.
     */
    public function __construct($file, $domain)
    {
        global $pagenow;

        if ($pagenow != 'plugins.php') return false;

        if (!isset($this->official_updates)) return false;

        if (isset($file)) $this->plugin_file = $file;
        if(is_callable('curl_init')) {
            try {
//            $this->updates = file_get_contents($this->official_updates);
                $curl_options = array();
                $curl_options[CURLOPT_RETURNTRANSFER] = 1;
                $curl_options[CURLOPT_URL] = $this->official_updates;
                $curl_options[CURLOPT_USERAGENT] = 'WooCommerce Email Customizer';

                $curl = curl_init();
                curl_setopt_array($curl, $curl_options);

                // Send the request & save response to $resp
                $this->updates = curl_exec($curl);
                // Close request to clear up some resources
                curl_close($curl);
            } catch (Exception $e) {
                //
            }
        }
        if(!empty($this->updates)){
            if (is_string($this->updates)) {
                $this->updates = json_decode($this->updates, true);
            }
            $this->new_version = (isset($this->updates['woo-email-customizer-page-builder']['current-version']) ? $this->updates['woo-email-customizer-page-builder']['current-version'] : '');
            $this->update_url = (isset($this->updates['woo-email-customizer-page-builder']['update-url']) ? $this->updates['woo-email-customizer-page-builder']['update-url'] : '');

            $this->processUpdateChecks();
            add_action('admin_notices', array($this, 'pluginUpdateNotification'));
        }
    }

    /**
     * Trigger the Notification of the Plugin.
     *
     * @return string Html response.
     */
    public function pluginUpdateNotification()
    {
        if (!isset($this->update_notice['new'])) return false;

        if ($this->update_notice['new'] == false) return false;

        $notice = $this->update_message;
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e($notice, 'woo-email-customizer-page-builder'); ?></p>
        </div>
        <?php
    }

    /**
     * Overall Version checking process with available and new.
     */
    public function processUpdateChecks()
    {
        $woo_mail_builder = array();
        $notice = '';
        if (isset($this->updates)) {
            $record = $this->updates;
            $woo_mail_builder = array();
            // Simple Sanity Check.
            if (isset($record['woo-email-customizer-page-builder'])) {
                $woo_mail_builder['current'] = $this->active_version; // Local Current Version.
                $woo_mail_builder['new'] = false;
                // If New Update is Available, then update the Status.
                if (version_compare($this->new_version, $this->active_version, '>')) {
                    // Set Available to Latest Version.
                    $woo_mail_builder['current'] = $this->new_version; // Remote Current Version.
                    // Simple Hook to Define plugin status.
                    $woo_mail_builder['new'] = true;
                    // Available location to Update the Plugin.
                    $woo_mail_builder['update'] = $this->update_url;
                    $notice = esc_html__("New Version of WooCommerce Email Customizer with Drag and Drop Email Builder ", 'woo-email-customizer-page-builder').'<a href="' . $this->update_url . '" target="_blank">v' . $woo_mail_builder['current'] . '</a> '.esc_html__('is available !', 'woo-email-customizer-page-builder');
                }
            }
        }
        $this->update_message = $notice;
        $this->update_notice = $woo_mail_builder;
    }
}
