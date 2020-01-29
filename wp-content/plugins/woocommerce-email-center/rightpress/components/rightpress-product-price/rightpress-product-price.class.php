<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price')) {

/**
 * RightPress Shared Product Price Component
 *
 * @class RightPress_Product_Price
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price
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

        // Flag products in cart
        add_filter('woocommerce_add_cart_item', array($this, 'flag_product_in_cart'), 1);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'flag_product_in_cart'), 1);

        // WPML Multi Currency support
        add_filter('wcml_multi_currency_ajax_actions', array($this, 'wcml_multi_currency_ajax_actions'));

        // Continue setup on init
        // Note: All plugins using this component must set up their callbacks during init before position 20
        add_action('init', array($this, 'init'), 20);
    }

    /**
     * Continue setup on init
     *
     * Note: Keep this functionality on init as plugins need time to load settings etc
     *
     * @access public
     * @return void
     */
    public function init()
    {

        // Load classes
        require_once __DIR__ . '/classes/rightpress-product-price-background-refresh.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-breakdown.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-cart.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-changes.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-display.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-exception.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-live-update.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-shop.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-test.class.php';
    }

    /**
     * Flag product in cart
     *
     * @access public
     * @param array $cart_item_data
     * @return array
     */
    public function flag_product_in_cart($cart_item_data)
    {

        $cart_item_data['data']->rightpress_in_cart = true;
        return $cart_item_data;
    }


    /**
     * =================================================================================================================
     * ASSETS
     * =================================================================================================================
     */

    /**
     * Enqueue component assets
     *
     * @access public
     * @return void
     */
    public static function enqueue_assets()
    {

        global $rightpress_version;

        // Enqueue styles
        RightPress_Help::enqueue_or_inject_stylesheet('rightpress-product-price-styles', RIGHTPRESS_LIBRARY_URL . '/components/rightpress-product-price/assets/styles.css', $rightpress_version);
    }


    /**
     * =================================================================================================================
     * LATE HOOK CONTROL
     * =================================================================================================================
     */

    /**
     * Add late action
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @return void
     */
    public static function add_late_action($hook, $callback, $accepted_args = 1)
    {

        add_action($hook, $callback, RightPress_Product_Price::get_late_hook_priority($hook, $callback), $accepted_args);
    }

    /**
     * Add late filter
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @param int $accepted_args
     * @return void
     */
    public static function add_late_filter($hook, $callback, $accepted_args = 1)
    {

        add_filter($hook, $callback, RightPress_Product_Price::get_late_hook_priority($hook, $callback), $accepted_args);
    }

    /**
     * Get late hook priority
     *
     * @access public
     * @param string $hook
     * @param mixed $callback
     * @return int
     */
    public static function get_late_hook_priority($hook, $callback)
    {

        return (int) apply_filters('rightpress_product_price_late_hook_priority', PHP_INT_MAX, $hook, $callback);
    }


    /**
     * =================================================================================================================
     * ROUNDING AND DECIMALS
     * =================================================================================================================
     */

    /**
     * Round product price
     *
     * @access public
     * @param float $price
     * @param int $decimals
     * @param bool $skip_default_rounding
     * @return float
     */
    public static function round($price, $decimals = null, $skip_default_rounding = false)
    {

        // Get decimals
        $decimals = RightPress_Product_Price::get_price_decimals($decimals);

        // Maybe apply default rounding
        $rounded_price = $skip_default_rounding ? $price : round($price, $decimals);

        // Allow developers to do their own rounding
        return apply_filters('rightpress_product_price_rounded_price', $rounded_price, $price, $decimals);
    }

    /**
     * Get product price decimals
     *
     * @access public
     * @param int $decimals
     * @return int
     */
    public static function get_price_decimals($decimals = null)
    {

        // Get decimals
        $decimals = isset($decimals) ? $decimals : wc_get_price_decimals();

        // Allow developers to override
        return apply_filters('rightpress_product_price_decimals', $decimals);
    }

    /**
     * Get product display price decimals
     *
     * @access public
     * @param int $decimals
     * @return int
     */
    public static function get_display_price_decimals($decimals = null)
    {

        return apply_filters('rightpress_product_price_display_decimals', RightPress_Product_Price::get_price_decimals($decimals));
    }


    /**
     * =================================================================================================================
     * PRICE COMPARISON
     * =================================================================================================================
     */

    /**
     * Check if prices differ in a float-safe way
     *
     * @access public
     * @param float $first_price
     * @param float $second_price
     * @return bool
     */
    public static function prices_differ($first_price, $second_price)
    {

        return (abs((float) $first_price - (float) $second_price) > 0.000001);
    }

    /**
     * Check if first price is bigger than second price
     *
     * @access public
     * @param float $first_price
     * @param float $second_price
     * @return bool
     */
    public static function price_is_bigger_than($first_price, $second_price)
    {

        return (((float) $first_price - (float) $second_price) > 0.000001);
    }

    /**
     * Check if first price is smaller than second price
     *
     * @access public
     * @param float $first_price
     * @param float $second_price
     * @return bool
     */
    public static function price_is_smaller_than($first_price, $second_price)
    {

        return (((float) $second_price - (float) $first_price) > 0.000001);
    }

    /**
     * Check if price is zero in a float-safe way
     *
     * @access public
     * @param float $price
     * @return bool
     */
    public static function price_is_zero($price)
    {

        return ((float) $price < 0.000001);
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get price key
     *
     * @access public
     * @param float $price
     * @return string
     */
    public static function get_price_key($price)
    {

        return number_format($price, RightPress_Product_Price::get_price_decimals());
    }

    /**
     * WPML Multi Currency support
     *
     * @access public
     * @param array $hooks
     * @return array
     */
    public function wcml_multi_currency_ajax_actions($hooks)
    {

        // Add our ajax hook
        $hooks[] = 'rightpress_product_price_live_update';

        return $hooks;
    }





}

RightPress_Product_Price::get_instance();

}
