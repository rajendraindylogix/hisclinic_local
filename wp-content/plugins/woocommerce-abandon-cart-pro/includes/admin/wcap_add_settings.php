<?php
/**
 * It will add all the settings needed for the plugin.
 * @author  Tyche Softwares
 * @package Abandoned-Cart-Pro-for-WooCommerce/Admin/Settings
 * @since 2.3.8
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists('Wcap_Add_Settings' ) ) {
    /**
     * It will add all the settings needed for the plugin.  
     * @since 5.0
     */
    class Wcap_Add_Settings {

        /**
         * It will add all the settings needed for the plugin. All settings are added using the WordPress settings api.
         * @hook admin_init
         * @since 2.3.8
         */
        public static function wcap_initialize_plugin_options() {

            // First, we register a section.
            add_settings_section(
                'ac_general_settings_section',         // ID used to identify this section and with which to register options
                __( 'Settings', 'woocommerce-ac' ),                  // Title to be displayed on the administration page
                array('Wcap_Add_Settings', 'wcap_general_options_callback' ), // Callback used to render the description of the section
                'woocommerce_ac_page'     // Page on which to add this section of options
            );

            add_settings_field(
                'ac_enable_cart_emails',
                __( 'Enable abandoned cart emails', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_enable_cart_emails_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Yes, enable the abandoned cart emails.', 'woocommerce-ac' ) )
            );
            add_settings_field(
                'ac_cart_abandoned_time',
                __( 'Cart abandoned cut-off time for logged-in users', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_cart_abandoned_time_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'For logged-in users consider cart abandoned after X minutes of item being added to cart & order not placed.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_cart_abandoned_time_guest',
                __( 'Cart abandoned cut-off time for guest users', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_cart_abandoned_time_guest_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'For guest users & visitors consider cart abandoned after X minutes of item being added to cart & order not placed.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_delete_abandoned_order_days',
                __( 'Automatically Delete Abandoned Orders after X days', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_delete_abandoned_orders_days_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Automatically delete abandoned cart orders after X days.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_email_admin_on_recovery',
                __( 'Email admin On Order Recovery', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_email_admin_on_recovery_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Sends email to Admin if an Abandoned Cart Order is recovered.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_disable_guest_cart_email',
                __( 'Do not track carts of guest users', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_disable_guest_cart_email_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Abandoned carts of guest users will not be tracked.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_track_guest_cart_from_cart_page',
                __( 'Start tracking from Cart Page', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_track_guest_cart_from_cart_page_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Enable tracking of abandoned products & carts even if customer does not visit the checkout page or does not enter any details on the checkout page like Name or Email. Tracking will begin as soon as a visitor adds a product to their cart and visits the cart page.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_disable_logged_in_cart_email',
                __( 'Do not track carts of logged-in users', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_disable_logged_in_cart_email_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'Abandoned carts of logged-in users will not be tracked.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'ac_capture_email_address_from_url',
                __( 'Capture Email address from URL', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_capture_email_address_from_url' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( 'If your site URL contain the same key, then it will capture it as an email address of customer.', 'woocommerce-ac' ) )
            );

            add_settings_field(
                'wcap_guest_cart_capture_msg',
                __( 'Message to be displayed for Guest users when tracking their carts', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_guest_cart_capture_msg_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( '<br>In compliance with GDPR, add a message on the Checkout page and Email Address Capture pop-up to inform Guest users of how their data is being used.<br><i>For example: Your email address will help us support your shopping experience throughout the site. Please check our Privacy Policy to see how we use your personal data.</i>', 'woocommerce-ac' ) )
            );
            
            add_settings_field(
                'wcap_logged_cart_capture_msg',
                __( 'Message to be displayed for registered users when tracking their carts.', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_logged_cart_capture_msg_callback' ),
                'woocommerce_ac_page',
                'ac_general_settings_section',
                array( __( '<br>In compliance with GDPR, add a message on the Shop & Product pages to inform Registered users of how their data is being used.<br><i>For example: Please check our Privacy Policy to see how we use your personal data.</i>', 'woocommerce-ac' ) )
            );
            
            /**
             * New section for the Adding the abandoned cart setting.
             * @since 4.7
             */

            add_settings_section(
                'ac_email_settings_section',           // ID used to identify this section and with which to register options
                __( 'Settings for abandoned cart recovery emails', 'woocommerce-ac' ),      // Title to be displayed on the administration page
                array('Wcap_Add_Settings', 'wcap_email_callback' ),// Callback used to render the description of the section
                'woocommerce_ac_page'     // Page on which to add this section of options
            );

            add_settings_field(
                'wcap_from_name',
                __( '"From" Name', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_from_name_callback' ),
                'woocommerce_ac_page',
                'ac_email_settings_section',
                array( 'Enter the name that should appear in the email sent.', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_from_email',
                __( '"From" Address', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_from_email_callback' ),
                'woocommerce_ac_page',
                'ac_email_settings_section',
                array( 'Email address from which the reminder emails should be sent. <strong>Note:</strong> This setting shall be applicable only when PHP mail function is used by your Hosting Provider. If SMTP mail plugins are used or if mail configuration is based on SMTP then this setting wont be applicable.', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_reply_email',
                __( 'Send Reply Emails to', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_reply_email_callback' ),
                'woocommerce_ac_page',
                'ac_email_settings_section',
                array( 'When a contact receives your email and clicks reply, which email address should that reply be sent to?', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_product_image_size',
                __( 'Product Image( H x W )', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_product_image_size_callback' ),
                'woocommerce_ac_page',
                'ac_email_settings_section',
                array( 'This setting affects the dimension of the product image in the abandoned cart reminder email.', 'woocommerce-ac' )
            );
            /*
             * New section for the Updating the cron job time
             * Since @: 4.0
             */

            add_settings_section(
                'ac_cron_job_settings_section',           // ID used to identify this section and with which to register options
                __( 'Setting for sending Emails & SMS using WP Cron', 'woocommerce-ac' ),      // Title to be displayed on the administration page
                array('Wcap_Add_Settings', 'wcap_cron_job_callback' ),// Callback used to render the description of the section
                'woocommerce_ac_page'     // Page on which to add this section of options
            );

            add_settings_field(
                'wcap_use_auto_cron',
                __( 'Send  Abandoned cart emails automatically using WP Cron', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_use_auto_cron_callback' ),
                'woocommerce_ac_page',
                'ac_cron_job_settings_section',
                array( 'Enabling this setting will send the abandoned cart reminder emails to the customer after the set time. If disabled, abandoned cart reminder emails will not be sent using WP Cron. You will need to set cron job manually from cPanel. If you are unsure how to set the cron job, please <a href= mailto:support@tychesoftwares.com>contact us</a> for it.', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_cron_time_duration',
                __( 'Run Automated WP Cron after X minutes', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_cron_time_duration_callback' ),
                'woocommerce_ac_page',
                'ac_cron_job_settings_section',
                array( 'The duration in minutes after which a WP Cron job will run automatically for sending the abandoned cart reminder emails & SMS to the customers.', 'woocommerce-ac' )
            );

            //Setting section and field for license options
            add_settings_section(
                'ac_general_license_key_section',
                __( 'Plugin License Options', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_general_license_key_section_callback' ),
                'woocommerce_ac_license_page'
            );

            add_settings_field(
                'edd_sample_license_key_ac_woo',
                __( 'License Key', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_edd_sample_license_key_ac_woo_callback' ),
                'woocommerce_ac_license_page',
                'ac_general_license_key_section',
                array( __( 'Enter your license key.', 'woocommerce-ac' ) )
             );

            add_settings_field(
                'activate_license_key_ac_woo',
                __( 'Activate License', 'woocommerce-ac' ),
                array( 'Wcap_Add_Settings', 'wcap_activate_license_key_ac_woo_callback' ),
                'woocommerce_ac_license_page',
                __( 'ac_general_license_key_section', 'woocommerce-ac' )
             );

            /**
             * New section for custom restrict settings
             * @since 4.1
             */
            add_settings_section(
                'ac_restrict_settings_section',           // ID used to identify this section and with which to register options
                __( 'Rules to exclude capturing abandoned carts', 'woocommerce-ac' ),      // Title to be displayed on the administration page
                array('Wcap_Add_Settings', 'wcap_custom_restrict_callback' ),// Callback used to render the description of the section
                'woocommerce_ac_page'     // Page on which to add this section of options
            );

            add_settings_field(
                'wcap_restrict_ip_address',
                __( 'Do not capture abandoned carts for these IP addresses', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_restrict_ip_address_callback' ),
                'woocommerce_ac_page',
                'ac_restrict_settings_section',
                array( 'The carts abandoned from these IP addresses will not be tracked by the plugin. Accepts wildcards, e.g <code>192.168.*</code> will block all IP addresses which starts from "192.168". <i>Separate IP addresses with commas.</i>', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_restrict_email_address',
                __( 'Do not capture abandoned carts for these email addresses', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_restrict_email_address_callback' ),
                'woocommerce_ac_page',
                'ac_restrict_settings_section',
                array( 'The carts abandoned using these email addresses will not be tracked by the plugin. <i>Separate email addresses with commas.</i>', 'woocommerce-ac' )
            );

            add_settings_field(
                'wcap_restrict_domain_address',
                __( 'Do not capture abandoned carts for email addresses from these domains', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_restrict_domain_address_callback' ),
                'woocommerce_ac_page',
                'ac_restrict_settings_section',
                array( 'The carts abandoned from email addresses with these domains will not be tracked by the plugin. <i>Separate email address domains with commas.</i>', 'woocommerce-ac' )
            );

            /**
             * New Settings for SMS Notifications
             */
            add_settings_section(
                'wcap_sms_settings_section',		// ID used to identify this section and with which to register options
                __( 'Twilio', 'woocommerce-ac' ),		// Title to be displayed on the administration page
                array( 'Wcap_Add_Settings', 'wcap_sms_settings_section_callback' ),		// Callback used to render the description of the section
                'woocommerce_ac_sms_page'				// Page on which to add this section of options
            );
            
            add_settings_field(
                'wcap_enable_sms_reminders',
                __( 'Enable SMS', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_enable_sms_reminders_callback' ),
                'woocommerce_ac_sms_page',
                'wcap_sms_settings_section',
                array( '<i>Enable the ability to send reminder SMS for abandoned carts.</i>', 'woocommerce-ac' )
            );
            
            add_settings_field(
                'wcap_sms_from_phone',
                __( 'From', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_sms_from_phone_callback' ),
                'woocommerce_ac_sms_page',
                'wcap_sms_settings_section',
                array( '<i>Must be a Twilio phone number (in E.164 format) or alphanumeric sender ID.</i>', 'woocommerce-ac' )
            );
            
            add_settings_field(
                'wcap_sms_account_sid',
                __( 'Account SID', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_sms_account_sid_callback' ),
                'woocommerce_ac_sms_page',
                'wcap_sms_settings_section',
                array( '' )
            );
            
            add_settings_field(
                'wcap_sms_auth_token',
                __( 'Auth Token', 'woocommerce-ac'  ),
                array( 'Wcap_Add_Settings', 'wcap_sms_auth_token_callback' ),
                'woocommerce_ac_sms_page',
                'wcap_sms_settings_section',
                array( '' )
            );

            // Finally, we register the fields with WordPress
            register_setting(
                'woocommerce_ac_settings',
                'ac_enable_cart_emails'
            );
            register_setting(
                'woocommerce_ac_settings',
                'ac_cart_abandoned_time',
                array ( 'Wcap_Add_Settings', 'wcap_cart_time_validation' )
            );
            register_setting(
                'woocommerce_ac_settings',
                'ac_cart_abandoned_time_guest',
                array ( 'Wcap_Add_Settings', 'wcap_cart_time_guest_validation' )
            );
            register_setting(
                'woocommerce_ac_settings',
                'ac_delete_abandoned_order_days',
                array ( 'Wcap_Add_Settings', 'wcap_delete_days_validation' )
            );
            register_setting(
                'woocommerce_ac_settings',
                'ac_email_admin_on_recovery'
            );
            register_setting(
                'woocommerce_ac_settings',
                'ac_email_admin_on_abandoned'
            );

            register_setting(
                'woocommerce_ac_settings',
                'ac_disable_guest_cart_email'
            );
            register_setting(
               'woocommerce_ac_settings',
               'ac_disable_logged_in_cart_email'
            );

            register_setting(
               'woocommerce_ac_settings',
               'ac_capture_email_address_from_url'
            );

            register_setting(
                'woocommerce_ac_settings',
                'wcap_guest_cart_capture_msg'
            );
            
            register_setting(
                'woocommerce_ac_settings',
                'wcap_logged_cart_capture_msg'
            );
            
            register_setting(
               'woocommerce_ac_settings',
               'ac_track_guest_cart_from_cart_page'
            );
            register_setting(
                'woocommerce_ac_license',
                'edd_sample_license_key_ac_woo'
            );

            register_setting(
                'woocommerce_ac_settings',
                'wcap_cron_time_duration'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_use_auto_cron'
            );

            register_setting(
                'woocommerce_ac_settings',
                'wcap_restrict_ip_address'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_restrict_email_address'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_restrict_domain_address'
            );

            register_setting(
                'woocommerce_ac_settings',
                'wcap_from_name'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_from_email'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_reply_email'
            );
            register_setting(
                'woocommerce_ac_settings',
                'wcap_product_image_height'
            );
            
            register_setting(
                'woocommerce_ac_settings',
                'wcap_product_image_width'
            );

            register_setting(
                'woocommerce_sms_settings',
                'wcap_enable_sms_reminders'
            );
            
            register_setting(
                'woocommerce_sms_settings',
                'wcap_sms_from_phone'
            );
            
            register_setting(
                'woocommerce_sms_settings',
                'wcap_sms_account_sid'
            );
            
            register_setting(
                'woocommerce_sms_settings',
                'wcap_sms_auth_token'
            );

            do_action( "wcap_add_new_settings" );
        }

        /**
         * Abandoned cart time field validation for loggedin users.
         * @param int | string $input Input of the field Abandoned cart cut off time
         * @return int | string $output Error message or the input value
         * @since 2.3.8
         */
        public static function wcap_cart_time_validation( $input ) {
            $output = '';
            if ( $input != '' && ( is_numeric( $input) && $input > 0  ) ) {
                $output = stripslashes( $input) ;
            } else {
                add_settings_error( 'ac_cart_abandoned_time', 'error found', __( 'Abandoned cart cut off time should be numeric and has to be greater than 0.', 'woocommerce-ac' ) );
            }
            return $output;
        }
        /**
         * Abandoned cart time field validation for guest users.
         * @param int | string $input input of the field Abandoned cart cut off time
         * @return int | string $output Error message or the input value
         * @since 2.3.8
         */
        public static function wcap_cart_time_guest_validation( $input ) {
            $output = '';
            if ( $input != '' && ( is_numeric( $input) && $input > 0  ) ) {
                $output = stripslashes( $input) ;
            } else {
                add_settings_error( 'ac_cart_abandoned_time_guest', 'error found', __( 'Abandoned cart cut off time should be numeric and has to be greater than 0.', 'woocommerce-ac' ) );
            }
            return $output;
        }
        /**
         * Validation for automatically delete abandoned carts after X days.
         * @param int | string $input input of the field Abandoned cart cut off time
         * @return int | string $output Error message or the input value
         * @since 2.3.8
         */
        public static function wcap_delete_days_validation( $input ) {
            $output = '';
            if ( $input == '' || ( is_numeric( $input ) && $input > 0 ) ) {
                $output = stripslashes( $input ) ;
            } else {
                add_settings_error( 'ac_delete_abandoned_order_days', 'error found', __( 'Automatically Delete Abandoned Orders after X days has to be greater than 0.', 'woocommerce-ac' ) );
            }
            return $output;
        }
        /**
         * Call back for the ac_general_settings_section section
         * @since 2.3.8
         */
        public static function wcap_general_options_callback() {

        }
        /**
         * Callback for enable cart emails field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_enable_cart_emails_callback( $args ) {
            // First, we read the option
            $enable_cart_emails = get_option( 'ac_enable_cart_emails' );
            // This condition added to avoid the notice displayed while Check box is unchecked.
            if  (isset( $enable_cart_emails ) &&  $enable_cart_emails == "" ) {
                $enable_cart_emails = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html = '<input type="checkbox" id="ac_enable_cart_emails" name="ac_enable_cart_emails" value="on" ' . checked( 'on', $enable_cart_emails, false ) . '/>';
            // Here, we'll take the first argument of the array and add it to a label next to the check box
            $html .= '<label for="ac_enable_cart_emails"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Abandoned cart time field for Logged in user.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_cart_abandoned_time_callback($args) {
            // First, we read the option
            $cart_abandoned_time = get_option( 'ac_cart_abandoned_time' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
                '<input type="text" id="ac_cart_abandoned_time" name="ac_cart_abandoned_time" value="%s" />',
                isset( $cart_abandoned_time ) ? esc_attr( $cart_abandoned_time ) : ''
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="ac_cart_abandoned_time"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Abandoned cart time field for Guest user.
         * @param array $args Argument given while adding the field
         * @since 4.0
         */
        public static function wcap_cart_abandoned_time_guest_callback($args) {
            // First, we read the option
            $cart_abandoned_time_guest = get_option( 'ac_cart_abandoned_time_guest' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<input type="text" id="ac_cart_abandoned_time_guest" name="ac_cart_abandoned_time_guest" value="%s" />',
            isset( $cart_abandoned_time_guest ) ? esc_attr( $cart_abandoned_time_guest ) : ''
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="ac_cart_abandoned_time_guest"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for deleting abandoned order after X days field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_delete_abandoned_orders_days_callback( $args ) {
            // First, we read the option
            $delete_abandoned_order_days = get_option( 'ac_delete_abandoned_order_days' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
                '<input type="text" id="ac_delete_abandoned_order_days" name="ac_delete_abandoned_order_days" value="%s" />',
                isset( $delete_abandoned_order_days ) ? esc_attr( $delete_abandoned_order_days ) : ''
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="ac_delete_abandoned_order_days"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for email admin on cart recovery field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_email_admin_on_recovery_callback( $args ) {
            // First, we read the option
            $email_admin_on_recovery = get_option( 'ac_email_admin_on_recovery' );
            // This condition added to avoid the notie displyed while Check box is unchecked.
            if ( isset( $email_admin_on_recovery ) && $email_admin_on_recovery == '' ) {
                $email_admin_on_recovery = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html='';
            printf(
                '<input type="checkbox" id="ac_email_admin_on_recovery" name="ac_email_admin_on_recovery" value="on"
                ' . checked( 'on', $email_admin_on_recovery, false ) . ' />'
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html .= '<label for="ac_email_admin_on_recovery"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for disable guest cart field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_disable_guest_cart_email_callback( $args ) {
            // First, we read the option
            $disable_guest_cart_email = get_option( 'ac_disable_guest_cart_email' );
            // This condition added to avoid the notie displyed while Check box is unchecked.
            if ( isset( $disable_guest_cart_email ) && $disable_guest_cart_email == '' ) {
                $disable_guest_cart_email = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html='';
            printf(
                '<input type="checkbox" id="ac_disable_guest_cart_email" name="ac_disable_guest_cart_email" value="on"
                '.checked( 'on', $disable_guest_cart_email, false ) . ' />'
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html .= '<label for="ac_disable_guest_cart_email"> ' . $args[0] . '</label> <br> <div id ="wcap_atc_disable_msg" class="wcap_atc_disable_msg"></div>';
            echo $html;
        }

        /**
         * Callback for disable logged-in cart email field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_disable_logged_in_cart_email_callback( $args ) {
            // First, we read the option
            $disable_logged_in_cart_email = get_option( 'ac_disable_logged_in_cart_email' );
            // This condition added to avoid the notice displyed while Check box is unchecked.
            if ( isset( $disable_logged_in_cart_email ) && $disable_logged_in_cart_email == '' ) {
                $disable_logged_in_cart_email = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html='';
            printf(
                '<input type="checkbox" id="ac_disable_logged_in_cart_email" name="ac_disable_logged_in_cart_email" value="on"
                '.checked( 'on', $disable_logged_in_cart_email, false ) . ' />'
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html .= '<label for="ac_disable_logged_in_cart_email"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Call back for capturing the email from URL.
         * @param array $args Argument given while adding the field
         * @since 7.6
         */
        public static function wcap_capture_email_address_from_url( $args ) {
            // First, we read the option
            $ac_capture_email_address_from_url = get_option( 'ac_capture_email_address_from_url' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
                '<input type="text" id="ac_capture_email_address_from_url" name="ac_capture_email_address_from_url" value="%s" />',
                isset( $ac_capture_email_address_from_url ) ? esc_attr( $ac_capture_email_address_from_url ) : ''
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="ac_capture_email_address_from_url_label"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Call back function for guest user cart capture message
         * @param array $args Argument for adding field details
         * @since 7.8
         */
        public static function wcap_guest_cart_capture_msg_callback( $args ) {
        
            $guest_msg = get_option( 'wcap_guest_cart_capture_msg' );
        
            $html = "<textarea rows='4' cols='80' id='wcap_guest_cart_capture_msg' name='wcap_guest_cart_capture_msg'>$guest_msg</textarea>";
        
            $html .= '<label for="wcap_guest_cart_capture_msg"> ' . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Call back function for registered user cart capture message
         * @param array $args Argument for adding field details
         * @since 7.8
         */
        public static function wcap_logged_cart_capture_msg_callback( $args) {
        
            $logged_msg = get_option( 'wcap_logged_cart_capture_msg' );
        
            $html = "<input type='text' class='regular-text' id='wcap_logged_cart_capture_msg' name='wcap_logged_cart_capture_msg' value='$logged_msg' />";
        
            $html .= '<label for="wcap_logged_cart_capture_msg"> ' . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for capturing guest cart which do not reach the checkout page.
         * @param array $args Argument given while adding the field
         * @since 2.7
         */
        public static function wcap_track_guest_cart_from_cart_page_callback( $args ) {
            // First, we read the option
            $disable_guest_cart_from_cart_page = get_option( 'ac_track_guest_cart_from_cart_page' );
            $disable_guest_cart_email          = get_option( 'ac_disable_guest_cart_email' );
            // This condition added to avoid the notice displyed while Check box is unchecked.
            if ( isset( $disable_guest_cart_from_cart_page ) && $disable_guest_cart_from_cart_page == '' ) {
                $disable_guest_cart_from_cart_page = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html     = '';
            $disabled = '';
            if ( isset( $disable_guest_cart_email ) && $disable_guest_cart_email == 'on' ) {
                $disabled                          = 'disabled';
                $disable_guest_cart_from_cart_page = 'off';
            }
            printf(
            '<input type="checkbox" id="ac_track_guest_cart_from_cart_page" name="ac_track_guest_cart_from_cart_page" value="on"
                '.checked( 'on', $disable_guest_cart_from_cart_page, false ) . '
                '.$disabled.' />' );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html .= '<label for="ac_track_guest_cart_from_cart_page"> ' . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Abandoned cart email settings.
         * @since 4.7
         */
        public static function wcap_email_callback () {

        }

        /**
         * Callback for Abandoned cart email from name setting.
         * @param array $args Argument given while adding the field
         * @since 4.7
         */
        public static function wcap_from_name_callback( $args ) {
            // First, we read the option
            $wcap_from_name = get_option( 'wcap_from_name' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<input type="text" id="wcap_from_name" name="wcap_from_name" value="%s" />',
            isset( $wcap_from_name ) ? esc_attr( $wcap_from_name ) : ''
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_from_name_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Abandoned cart email from email setting.
         * @param array $args Argument given while adding the field
         * @since 4.7
         */
        public static function wcap_from_email_callback( $args ) {
            // First, we read the option
            $wcap_from_email = get_option( 'wcap_from_email' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<input type="text" id="wcap_from_email" name="wcap_from_email" value="%s" />',
            isset( $wcap_from_email ) ? esc_attr( $wcap_from_email ) : ''
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_from_email_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Abandoned cart email reply to email setting.
         * @param array $args Argument given while adding the field
         * @since 4.7
         */
        public static function wcap_reply_email_callback( $args ) {
            // First, we read the option
            $wcap_reply_email = get_option( 'wcap_reply_email' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<input type="text" id="wcap_reply_email" name="wcap_reply_email" value="%s" />',
            isset( $wcap_reply_email ) ? esc_attr( $wcap_reply_email ) : ''
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_reply_email_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for Product image size in Abandoned cart email.
         * @param array $args Argument given while adding the field
         * @since 5.0
         */

        public static function wcap_product_image_size_callback( $args ) {
            // First, we read the option
            $wcap_product_image_height = get_option( 'wcap_product_image_height' );
            $wcap_product_image_width  = get_option( 'wcap_product_image_width' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            ?> <input type="text" id = "wcap_product_image_height" style= "width:50px" name="wcap_product_image_height" value="<?php echo $wcap_product_image_height; ?>" />
             <?php echo "x"; ?>
             <input type="text" id = "wcap_product_image_width" style = "width:50px" name="wcap_product_image_width" value="<?php echo $wcap_product_image_width; ?>" />
             px
            <?php
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_product_image_size"> '  . $args[0] . '</label>';
            echo $html;
        }
        /**
         * Callback for Cron job setting of the plugin
         * @since 4.0
         */
        public static function wcap_cron_job_callback () {

        }

        /**
         * Callback for use wp-cron for Abandoned cart email.
         * @param array $args Argument given while adding the field
         * @since 4.0
         */
        public static function wcap_use_auto_cron_callback( $args ) {
            // First, we read the option
            $enable_auto_cron = get_option( 'wcap_use_auto_cron' );


            // This condition added to avoid the notie displyed while Check box is unchecked.
            if( isset( $enable_auto_cron ) && '' == $enable_auto_cron ) {
                $enable_auto_cron = 'off';
            }
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            $html='';
            printf(
                '<input type="checkbox" id="wcap_use_auto_cron" name="wcap_use_auto_cron" value="on"
                '.checked( 'on', $enable_auto_cron, false ) . ' />'
            );
            //$html = '<input type="checkbox" id="wcap_use_auto_cron" name="wcap_use_auto_cron" value="on" ' . checked( 'on', $enable_auto_cron, false ) . '/>';
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html .= '<label for="wcap_use_auto_cron_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for setting the cron  interval for Abandoned cart email.
         * @param array $args Argument given while adding the field
         * @since 4.0
         */
        public static function wcap_cron_time_duration_callback( $args ) {
            // First, we read the option
            $wcap_cron_time_duration = get_option( 'wcap_cron_time_duration' );
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<input type="text" id="wcap_cron_time_duration" name="wcap_cron_time_duration" value="%s" />',
            isset( $wcap_cron_time_duration ) ? esc_attr( $wcap_cron_time_duration ) : ''
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_cron_time_duration"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for License plugin option
         * @since 2.3.8
         */
        public static function wcap_general_license_key_section_callback(){

        }

        /**
         * Callback for License key field.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_edd_sample_license_key_ac_woo_callback( $args ){
            $edd_sample_license_key_ac_woo_field = get_option( 'edd_sample_license_key_ac_woo' );
            printf(
                '<input type="text" id="edd_sample_license_key_ac_woo" name="edd_sample_license_key_ac_woo" class="regular-text" value="%s" />',
                isset( $edd_sample_license_key_ac_woo_field ) ? esc_attr( $edd_sample_license_key_ac_woo_field ) : ''
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="edd_sample_license_key_ac_woo"> '  . $args[0] . '</label>';
            echo $html;
        }
        /**
         * Callback for Activate License key button.
         * @param array $args Argument given while adding the field
         * @since 2.3.8
         */
        public static function wcap_activate_license_key_ac_woo_callback() {
            $license = get_option( 'edd_sample_license_key_ac_woo' );
            $status  = get_option( 'edd_sample_license_status_ac_woo' );
            ?>
                <form method="post" action="options.php">
                <?php if ( false !== $license ) { ?>
                    <?php if( $status !== false && $status == 'valid' ) { ?>
                        <span style="color:green;"><?php _e( 'active' ); ?></span>
                        <?php wp_nonce_field( 'edd_sample_nonce' , 'edd_sample_nonce' ); ?>
                        <input type="submit" class="button-secondary" name="edd_ac_license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"/>
                     <?php } else { ?>

                                <?php
                                wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>

                                <input type="submit" class="button-secondary" name="edd_ac_license_activate" value="<?php _e( 'Activate License' ); ?>"/>
                            <?php } ?>
                <?php } ?>
                </form>
            <?php
        }

        /**
         * Callback for custom restriction of the abandoned carts
         * @since 4.1
         */
        public static function wcap_custom_restrict_callback () {

        }

        /**
         * Callback for restrict IP address.
         * @param array $args Argument given while adding the field
         * @since 4.1
         */
        public static function wcap_restrict_ip_address_callback( $args ) {
            // First, we read the option
            $wcap_restrict_ip_address = get_option( 'wcap_restrict_ip_address' );
            $value = isset( $wcap_restrict_ip_address ) ? esc_attr( $wcap_restrict_ip_address ) : '';
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<textarea rows="4" cols="50" id="wcap_restrict_ip_address" name="wcap_restrict_ip_address" placeholder="Add an IP address" />' . $value .'</textarea>'
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_restrict_ip_address_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for restrict email address.
         * @param array $args Argument given while adding the field
         * @since 4.1
         */
        public static function wcap_restrict_email_address_callback( $args ) {
            // First, we read the option
            $wcap_restrict_email_address = get_option( 'wcap_restrict_email_address' );
            $email_value                 = isset( $wcap_restrict_email_address ) ? esc_attr( $wcap_restrict_email_address ) : '';
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
                '<textarea rows="4" cols="50" id="wcap_restrict_email_address" name="wcap_restrict_email_address" placeholder="Add an email address" />' . $email_value .'</textarea>'
            );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_restrict_email_address_label"> '  . $args[0] . '</label>';
            echo $html;
        }

        /**
         * Callback for restrict domain names.
         * @param array $args Argument given while adding the field
         * @since 4.1
         */
        public static function wcap_restrict_domain_address_callback( $args ) {
            // First, we read the option
            $wcap_restrict_domain_address = get_option( 'wcap_restrict_domain_address' );
            $domain_value                 = isset( $wcap_restrict_domain_address ) ? esc_attr( $wcap_restrict_domain_address ) : '';
            // Next, we update the name attribute to access this element's ID in the context of the display options array
            // We also access the show_header element of the options collection in the call to the checked() helper function
            printf(
            '<textarea rows="4" cols="50" id="wcap_restrict_domain_address" name="wcap_restrict_domain_address" placeholder="Add an email domain name (Ex. hotmail.com)" />' . $domain_value .'</textarea>'
                );
            // Here, we'll take the first argument of the array and add it to a label next to the checkbox
            $html = '<label for="wcap_restrict_domain_address_label"> '  . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for SMS Settings callback
         * @since 7.9
         */
        public static function wcap_sms_settings_section_callback() {
            _e( 'Configure your Twilio account settings below. Please note that due to some restrictions from Twilio, customers <i>may sometimes</i> receive delayed messages', 'woocommerce-ac' );
        }
        
        /**
         * Callback for enable SMS reminders
         * @param array $args Argument given while adding the field
         * @since 7.9
         */
        public static function wcap_enable_sms_reminders_callback( $args ) {
        
            $wcap_enable_sms = get_option( 'wcap_enable_sms_reminders' );
        
            if  (isset( $wcap_enable_sms ) &&  $wcap_enable_sms == "" ) {
                $wcap_enable_sms = 'off';
            }
        
            $html = '<input type="checkbox" id="wcap_enable_sms_reminders" name="wcap_enable_sms_reminders" value="on" ' . checked( 'on', $wcap_enable_sms, false ) . '/>';
        
            $html .= '<label for="wcap_enable_sms_reminders"> ' . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for From Phone Number
         * @param array $args Argument given while adding the field
         * @since 7.9
         */
        public static function wcap_sms_from_phone_callback( $args ) {
        
            $wcap_from_phone = get_option( 'wcap_sms_from_phone' );
        
            $html = "<input type='text' id='wcap_sms_from_phone' name='wcap_sms_from_phone' value='$wcap_from_phone'  />";
        
            $html .= '<label for="wcap_from_phone"> ' . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for Account SID
         * @param array $args Argument given while adding the field
         * @since 7.9
         */
        public static function wcap_sms_account_sid_callback( $args ) {
        
            $wcap_sms_account_sid = get_option( 'wcap_sms_account_sid' );
        
            $html = "<input type='text' style='width:60%;' id='wcap_sms_account_sid' name='wcap_sms_account_sid' value='$wcap_sms_account_sid'  />";
        
            $html .= '<label for="wcap_sms_account_sid"> ' . $args[0] . '</label>';
            echo $html;
        }
        
        /**
         * Callback for Auth Token
         * @param array $args Argument given while adding the field
         * @since 7.9
         */
        public static function wcap_sms_auth_token_callback( $args ) {
        
            $wcap_sms_auth_token = get_option( 'wcap_sms_auth_token' );
        
            $html = "<input type='text' style='width:60%;' id='wcap_sms_auth_token' name='wcap_sms_auth_token' value='$wcap_sms_auth_token'  />";
        
            $html .= '<label for="wcap_sms_auth_token"> ' . $args[0] . '</label>';
            echo $html;
        
        }
    }
}
?>