<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price_Cart')) {

/**
 * RightPress Shared Product Price Cart
 *
 * @class RightPress_Product_Price_Cart
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Cart
{

    // Store cart item price change data in memory
    private $cart_item_price_changes = array();

    // Flags
    private $cart_loaded_from_session   = false;

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

        // No plugin uses this functionality
        if (!has_filter('rightpress_product_price_cart_item_price_changes_first_stage_callbacks') && !has_filter('rightpress_product_price_cart_item_price_changes_second_stage_callbacks') && !has_filter('rightpress_product_price_cart_item_price_changes_third_stage_callbacks')) {
            return;
        }

        // WooCommerce cart hooks
        RightPress_Product_Price::add_late_action('woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session'));
        RightPress_Product_Price::add_late_action('woocommerce_before_calculate_totals', array($this, 'before_calculate_totals'));
        RightPress_Product_Price::add_late_action('woocommerce_applied_coupon', array($this, 'applied_coupon'));
    }

    /**
     * =================================================================================================================
     * WOOCOMMERCE CART HOOKS
     * =================================================================================================================
     */

    /**
     * Cart loaded from session
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function cart_loaded_from_session($cart)
    {

        // Set flag
        $this->cart_loaded_from_session = true;

        // Maybe change cart item prices
        $this->maybe_change_cart_item_prices($cart);
    }

    /**
     * Before calculate totals
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function before_calculate_totals($cart)
    {

        // Maybe change cart item prices
        $this->maybe_change_cart_item_prices($cart);
    }

    /**
     * Applied coupon
     *
     * @access public
     * @param string $coupon_code
     * @return void
     */
    public function applied_coupon($coupon_code)
    {

        // Check if cart has been loaded
        if ($this->cart_loaded_from_session) {

            // Maybe change cart item prices
            $this->maybe_change_cart_item_prices(WC()->cart);
        }
    }

    /**
     * =================================================================================================================
     * GENERAL CART ITEM PRICING HANDLING
     * =================================================================================================================
     */

    /**
     * Maybe change cart item prices
     *
     * @access public
     * @param object $cart
     * @return void
     */
    private function maybe_change_cart_item_prices($cart)
    {

        // Check if cart has been loaded
        if (!$this->cart_loaded_from_session) {
            return;
        }

        // Wait until applied coupon event if coupon is being applied during current request
        if ($this->coupon_is_being_applied()) {
            if (current_action() !== 'woocommerce_applied_coupon' && !did_action('woocommerce_applied_coupon')) {
                return;
            }
        }

        // Cart is empty, nothing to do
        if (!is_array($cart->cart_contents) || empty($cart->cart_contents)) {
            return;
        }

        // Get price changes for cart items
        $price_changes = RightPress_Product_Price_Changes::get_price_changes_for_cart_items($cart->cart_contents);

        // Store price changes in memory
        $this->cart_item_price_changes = $price_changes;

        // Apply price changes
        foreach ($price_changes as $cart_item_key => $cart_item_price_change) {

            // Set price to cart item
            $cart->cart_contents[$cart_item_key]['data']->set_price($cart_item_price_change['price']);

            // Trigger event
            do_action('rightpress_product_price_cart_price_set', $cart_item_price_change['price'], $cart_item_key, $cart, $cart_item_price_change);
        }

        // Trigger no changes event
        if (empty($price_changes)) {
            do_action('rightpress_product_price_cart_no_changes_to_prices', $cart);
        }

        // Maybe force cart totals recalculation
        if (current_filter() !== 'woocommerce_before_calculate_totals') {
            if (RightPress_Help::is_request('ajax') && isset($_REQUEST['wc-ajax']) && in_array($_REQUEST['wc-ajax'], array('get_refreshed_fragments', 'add_to_cart', 'remove_from_cart'), true)) {
                WC()->session->set('cart_totals', null);
            }
        }
    }

    /**
     * Get cart item price changes
     *
     * @access public
     * @param string $cart_item_key
     * @return array
     */
    public static function get_cart_item_price_changes($cart_item_key = null)
    {

        // Get instance
        $instance = RightPress_Product_Price_Cart::get_instance();

        // Return changes for single cart item
        if (isset($cart_item_key)) {
            return isset($instance->cart_item_price_changes[$cart_item_key]) ? $instance->cart_item_price_changes[$cart_item_key] : array();
        }
        // Return all changes
        else {
            return $instance->cart_item_price_changes;
        }
    }

    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Check if coupon is being applied during current request
     *
     * @access public
     * @return bool
     */
    public function coupon_is_being_applied()
    {

        return (!empty($_POST['apply_coupon']) && !empty($_POST['coupon_code']));
    }









}

RightPress_Product_Price_Cart::get_instance();

}
