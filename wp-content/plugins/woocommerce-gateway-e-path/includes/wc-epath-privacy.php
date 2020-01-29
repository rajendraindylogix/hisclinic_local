<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
    return;
}

class WC_ePath_Privacy extends WC_Abstract_Privacy {

    public function __construct() {
        parent::__construct( __( 'e-Path', 'wcepath' ) );
    }

    /**
     * Gets the message of the privacy to display.
     *
     * @return string
     */
    public function get_privacy_message() {
        $content =
            '<p>' . sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy</a>.', 'wcepath' ), 'https://docs.woocommerce.com/privacy/' ) . '</p>' .
            '<p>' . __( 'Please see the <a href="http://e-path.com.au/privacy.html">e-Path Privacy Policy</a> for specific details.', 'woocommerce' ) . '</p>';
       return $content;
    }

}

new WC_ePath_Privacy();