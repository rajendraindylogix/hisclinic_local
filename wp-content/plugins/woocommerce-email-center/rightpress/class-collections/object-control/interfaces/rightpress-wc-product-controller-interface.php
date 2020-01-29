<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_WC_Product_Controller_Interface')) {

/**
 * WooCommerce Product Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WC_Product_Controller_Interface
{

    /**
     * Get checkbox label
     *
     * @access public
     * @return string
     */
    public function get_checkbox_label();

    /**
     * Get checkbox description
     *
     * @access public
     * @return string
     */
    public function get_checkbox_description();

    /**
     * Check if subscriptions are enabled for product without loading object
     *
     * @access public
     * @param object|int $product
     * @return string
     */
    public function is_enabled_for_product($product);

    /**
     * Get product list custom column value
     *
     * @access public
     * @param int $post_id
     * @return string
     */
    public function get_product_list_shared_column_value($post_id);

    /**
     * Print product settings
     *
     * @access public
     * @return void
     */
    public function print_product_settings();

    /**
     * Print variation settings
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function print_variation_settings($loop, $variation_data, $variation);

    /**
     * Validate and sanitize product settings
     *
     * @access public
     * @param array $settings
     * @param object $object
     * @return array
     */
    public function sanitize_product_settings($settings, $object);



}
}
