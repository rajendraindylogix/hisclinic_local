<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RightPress library legacy code support
 *
 * @class RightPress_Legacy
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Legacy')) {

class RightPress_Legacy
{

    // Singleton instance
    protected static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Live product price update label html
        add_filter('rightpress_product_price_live_update_label_html', array($this, 'product_price_live_update_label_html'));

        // Live product price update extra data
        add_filter('rightpress_product_price_live_update_extra_data', array($this, 'product_price_live_update_extra_data'));
    }

    /**
     * Live product price update labe html
     *
     * @access public
     * @param string $label_html
     * @return string
     */
    public function product_price_live_update_label_html($label_html)
    {

        return apply_filters('rightpress_live_product_price_update_label_html', $label_html);
    }

    /**
     * Live product price update extra data
     *
     * @access public
     * @param array $extra_data
     * @return array
     */
    public function product_price_live_update_extra_data($extra_data)
    {

        return apply_filters('rightpress_live_product_price_update_extra_data', $extra_data);
    }





}

RightPress_Legacy::get_instance();

}
