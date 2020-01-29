<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price_Display')) {

/**
 * RightPress Shared Product Price Display
 *
 * @class RightPress_Product_Price_Display
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Display
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

        // Shop product
        if (has_filter('rightpress_product_price_display_shop')) {

            // TBD: This needs to be prepared for use by subscriptions, bookings and deposits
        }

        // Variable product display price fixes for all plugins that change product prices in shop (even when specific display formatting is not used)
        if (has_filter('rightpress_product_price_shop_cache_hash_data')) {

            // TBD: This needs to be prepared for use by subscriptions, bookings and deposits

            // Variable product display price fix
            // Note: We use very early position so that 3rd party plugins can still make their changes to display price
            add_filter('woocommerce_variable_price_html', array($this, 'maybe_change_variable_product_display_price'), 1, 2);

            // Fix product sale flag for variable products
            RightPress_Product_Price::add_late_filter('woocommerce_product_is_on_sale', array($this, 'maybe_fix_variable_product_sale_flag'), 2);
        }

        // Cart item display price
        RightPress_Product_Price::add_late_filter('woocommerce_cart_item_price', array($this, 'cart_item_display_price'), 3);
    }

    /**
     * Cart item display price
     *
     * @access public
     * @param string $display_price
     * @param array $cart_item
     * @param string $cart_item_key
     * @return string
     */
    public function cart_item_display_price($display_price, $cart_item, $cart_item_key)
    {

        // Functionality is not enabled
        if (!apply_filters('rightpress_product_price_cart_item_display_price_enabled', false)) {
            return $display_price;
        }

        // Get cart item price changes
        $price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes($cart_item_key);

        // Check if any price changes were made to this cart item
        if (!empty($price_changes['all_changes'])) {

            // Get new display price
            $display_price = RightPress_Product_Price_Display::get_cart_item_product_display_price($cart_item['data'], $price_changes, $cart_item);
        }

        // Return display price
        return $display_price;
    }

    /**
     * Get cart item product display price
     *
     * Note: This is also used for product price live update functionality where we simulate add to cart in which case $cart_item is not provided
     *
     * @access public
     * @param object $product
     * @param array $price_data
     * @param array $cart_item
     * @param bool $display_subtotal
     * @param bool $always_display_quantity
     * @return string
     */
    public static function get_cart_item_product_display_price($product, $price_data, $cart_item = null, $display_subtotal = false, $always_display_quantity = false)
    {

        $is_cart = isset($cart_item);

        // Get price breakdown for display
        $price_breakdown = RightPress_Product_Price_Breakdown::get_price_breakdown_for_display($price_data);

        // Prepare and format prices for display
        foreach ($price_breakdown as $price_key => $price_breakdown_entry) {

            // Get price from price breakdown entry
            $price = $price_breakdown_entry['price'];

            // Prepare price for display
            $price = (float) RightPress_Product_Price_Display::prepare_product_price_for_display($product, $price, $is_cart);

            // Prepare full price for display
            $full_price = (float) RightPress_Product_Price_Display::prepare_product_price_for_display($product, $price_breakdown_entry['full_price'], $is_cart);

            // Format price
            $formatted_price = wc_price($price);

            // Maybe prepend initial price
            if ($is_cart && RightPress_Product_Price_Display::display_price_was_discounted($price, $full_price) && apply_filters('rightpress_product_price_display_full_price', false, $full_price, $price, $cart_item)) {
                $formatted_price = '<del>' . wc_price($full_price) . '</del> <ins>' . $formatted_price . '</ins>';
            }

            // Set prices
            $price_breakdown[$price_key]['price'] = $price;
            $price_breakdown[$price_key]['formatted_price'] = apply_filters('rightpress_product_price_cart_item_product_display_price', $formatted_price, $price_breakdown_entry, $full_price);
        }

        // Display price breakdown
        if ((count($price_breakdown) > 1) || $always_display_quantity) {

            $rows = array();

            // Get display context
            $context = $is_cart ? 'cart' : 'shop';

            // Format rows
            foreach ($price_breakdown as $price_key => $price_breakdown_entry) {
                $rows[] = '<div class="rightpress_product_price_breakdown_row"><div class="rightpress_product_price_breakdown_price rightpress_product_price_breakdown_' . $context . '_price">' . $price_breakdown_entry['formatted_price'] . '</div><div class="rightpress_product_price_breakdown_quantity rightpress_product_price_breakdown_' . $context . '_quantity">&times; ' . $price_breakdown_entry['quantity'] . '</div></div>';
            }

            // Format table
            $table_html = '<div class="rightpress_product_price_breakdown rightpress_product_price_breakdown_' . $context . '">' . join('', $rows) . '</div>';

            // Maybe add subtotal
            if ($display_subtotal) {

                // Calculate subtotal
                $subtotal = 0.0;

                // Format rows
                foreach ($price_breakdown as $price_key => $price_breakdown_entry) {
                    $subtotal += (RightPress_Product_Price::round($price_breakdown_entry['price']) * $price_breakdown_entry['quantity']);
                }

                // Append to table
                $table_html .= '<div class="rightpress_product_price_breakdown_subtotal rightpress_product_price_breakdown_' . $context . '_subtotal">' . wc_price($subtotal) . '</div>';
            }

            // Set new display price
            $display_price = apply_filters('rightpress_product_price_cart_item_product_display_price_breakdown_table', $table_html, $price_breakdown);
        }
        // Display single price
        else {

            // Get price data
            $price_breakdown_entry = array_pop($price_breakdown);

            // Set new display price
            $display_price = $price_breakdown_entry['formatted_price'];
        }

        // Return display price
        return $display_price;
    }

    /**
     * Prepare product price for display
     *
     * @access public
     * @param object $product
     * @param float $custom_price
     * @param bool $is_cart
     * @param bool $suppress_currency_conversion
     * @return float
     */
    public static function prepare_product_price_for_display($product, $custom_price = null, $is_cart = false, $suppress_currency_conversion = false)
    {

        // Get product price
        $price = ($custom_price !== null ? $custom_price : $product->get_price());

        // Get tax display option
        $tax_display = $is_cart ? get_option('woocommerce_tax_display_cart') : get_option('woocommerce_tax_display_shop');

        // Include or exclude tax
        if ($tax_display === 'excl') {
            $price = wc_get_price_excluding_tax($product, array('qty' => 1, 'price' => $price));
        }
        else {
            $price = wc_get_price_including_tax($product, array('qty' => 1, 'price' => $price));
        }

        // If custom price was provided, apply realmag777 currency conversion
        if ($custom_price === null && !$suppress_currency_conversion) {
            $price = apply_filters('woocs_convert_price', $price, false);
        }

        // Return price for display
        return (float) $price;
    }

    /**
     * Check if product display price was discounted
     *
     * Note: This method can only be used for display purposes since it rounds prices to decimal places,
     * actual price calculations may have more decimals than used for display
     *
     * @access public
     * @param float $new_price
     * @param float $old_price
     * @return bool
     */
    public static function display_price_was_discounted($new_price, $old_price)
    {

        // Get display price decimals
        $decimals = RightPress_Product_Price::get_display_price_decimals();

        // Compare prices rounded to decimal places
        return (string) round($new_price, $decimals) < (string) round($old_price, $decimals);
    }

    /**
     * Check if display prices differ
     *
     * Note: This method can only be used for display purposes since it rounds prices to decimal places,
     * actual price calculations may have more decimals than used for display
     *
     * @access public
     * @param float $new_price
     * @param float $old_price
     * @return bool
     */
    public static function display_prices_differ($new_price, $old_price)
    {

        // Get display price decimals
        $decimals = RightPress_Product_Price::get_display_price_decimals();

        // Compare prices rounded to decimal places
        return (string) round($new_price, $decimals) !== (string) round($old_price, $decimals);
    }

    /**
     * Variable product display price fix
     *
     * Based on WC_Product_Variable::get_price_html, WC version 3.6.4
     *
     * @access public
     * @param string $price_html
     * @param object $product
     * @return string
     */
    public function maybe_change_variable_product_display_price($price_html, $product)
    {

        // Process variable product prices only
        if (!$product->is_type('variable')) {
            return $price_html;
        }

        // Skip if cart was has not been loaded
        if (!did_action('woocommerce_cart_loaded_from_session')) {
            return $price_html;
        }

        // Skip products in cart
        if (!empty($product->rightpress_in_cart)) {
            return $price_html;
        }

        // Skip backend requests
        if (!RightPress_Help::is_request('frontend')) {
            return $price_html;
        }

        // Product price live update in progress
        if (RightPress_Product_Price_Live_Update::is_processing_live_update_request()) {
            return $price_html;
        }

        // Get visible variations prices
        $prices = RightPress_Product_Price_Shop::get_visible_variations_prices($product, true);

        // No prices set
        if (empty($prices['price'])) {

            $price_html = apply_filters('woocommerce_variable_empty_price_html', '', $product);
        }
        // Prices set
        else {

            // Get min and max prices
            // Note: We depend on correct price sorting in RightPress_Product_Price_Shop::get_visible_variations_prices
            $min_price      = current($prices['price']);
            $max_price      = end($prices['price']);
            $min_reg_price  = current($prices['regular_price']);
            $max_reg_price  = end($prices['regular_price']);

            // Prices do not match - display range
            if (RightPress_Product_Price::prices_differ($min_price, $max_price)) {
                $price_html = wc_format_price_range($min_price, $max_price);
            }
            // Display single price on sale
            else if ($product->is_on_sale() && !RightPress_Product_Price::prices_differ($min_reg_price, $max_reg_price)) {
                $price_html = wc_format_sale_price(wc_price($max_reg_price), wc_price($min_price));
            }
            // Display single price
            else {
                $price_html = wc_price($min_price);
            }
        }

        return $price_html;
    }

    /**
     * Fix product sale flag for variable products
     *
     * This overrides any current sale flag value whether it's true or false
     *
     * @access public
     * @param bool $is_on_sale
     * @param object $product
     * @return bool
     */
    public function maybe_fix_variable_product_sale_flag($is_on_sale, $product)
    {

        // Process variable product prices only
        if (!$product->is_type('variable')) {
            return $is_on_sale;
        }

        // Skip if cart was has not been loaded
        if (!did_action('woocommerce_cart_loaded_from_session')) {
            return $is_on_sale;
        }

        // Skip backend requests
        if (!RightPress_Help::is_request('frontend')) {
            return $is_on_sale;
        }

        // Get visible variations prices
        $prices = RightPress_Product_Price_Shop::get_visible_variations_prices($product, true);

        // Check if product is on sale
        // Note: We compare arrays of prices here, not actual prices!
        $is_on_sale = $prices['regular_price'] !== $prices['sale_price'] && $prices['sale_price'] === $prices['price'];

        // Return updated value
        return $is_on_sale;
    }












// TBD: FILTER NAMES:
// rightpress_product_price_display_shop
// rightpress_product_price_display_cart


/**
 * Display price setup
 *
 * @access private
 * @return void
 */
public function display_price_setup()
{
    // Define hooks
    $hooks = array(
        'woocommerce_empty_price_html',
        'woocommerce_get_price_html',
        'woocommerce_grouped_free_price_html',
        'woocommerce_grouped_price_html',
        'woocommerce_grouped_empty_price_html',
        'woocommerce_variable_empty_price_html',
        'woocommerce_variable_price_html',
    );

    // Set up hooks
    foreach ($hooks as $hook) {
        RightPress_Product_Price::add_late_filter($hook, array('RightPress_Product_Price', 'display_price_handler'), 2);
    }
}

/**
 * Display price handler
 *
 * @access public
 * @param string $display_price
 * @param object $product
 * @return string
 */
public static function display_price_handler($display_price, $product)
{
    return apply_filters('rightpress_product_price_display_price', $display_price, $product, current_filter());
}


// TBD: maybe use these hooks:
// woocommerce_format_sale_price
// woocommerce_format_price_range
// woocommerce_get_price_suffix
// woocommerce_price_format
// formatted_woocommerce_price
// wc_price


}

RightPress_Product_Price_Display::get_instance();

}
