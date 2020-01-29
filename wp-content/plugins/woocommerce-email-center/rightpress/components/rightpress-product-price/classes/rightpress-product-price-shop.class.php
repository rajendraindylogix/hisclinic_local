<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cached pre-calculated prices are stored in wp_options in the following format.
 * Note: Prices can be cached raw or prepared for display (tax adjusted) in which case they simply get a distinct {price_hash}.
 *
 * Generic format:
 *
 *      'rightpress_prices_{product_id}' = array(
 *          {system_hash} => array(
 *              {price_type} => array(
 *                  {price_hash} => array(
 *                      'p' => {price},
 *                      't' => {timestamp},
 *                  ),
 *              ),
 *          )
 *      )
 *
 * Variable products:
 *
 *      'rightpress_prices_{product_id}' = array(
 *          {system_hash} => array(
 *              {price_type} => array(
 *                  {variation_id} => array(
 *                      {price_hash|variable_product_prices_hash} => array(
 *                          'p' => {price},
 *                          't' => {timestamp},
 *                      ),
 *                  ),
 *              ),
 *          )
 *      )
 */

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price_Shop')) {

/**
 * RightPress Shared Product Price Shop
 *
 * @class RightPress_Product_Price_Shop
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Shop
{

    // TBD: Maybe we need to do this in a more controlled fashion - when any price type is requested, we do all three in one call?

    private $cache  = array();
    private $store  = false;

    private $observing      = null;
    private $calculating    = false;

    private $system_hash = null;

    private $visible_variations_prices = array();

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
        if (!has_filter('rightpress_product_price_shop_cache_hash_data')) {
            return;
        }

        // Set up price hooks
        RightPress_Product_Price::add_late_filter('woocommerce_product_get_price', array($this, 'maybe_change_product_price'), 2);
        RightPress_Product_Price::add_late_filter('woocommerce_product_get_sale_price', array($this, 'maybe_change_product_price'), 2);
        RightPress_Product_Price::add_late_filter('woocommerce_product_get_regular_price', array($this, 'maybe_change_product_price'), 2);
        RightPress_Product_Price::add_late_filter('woocommerce_product_variation_get_price', array($this, 'maybe_change_product_price'),  2);
        RightPress_Product_Price::add_late_filter('woocommerce_product_variation_get_sale_price', array($this, 'maybe_change_product_price'), 2);
        RightPress_Product_Price::add_late_filter('woocommerce_product_variation_get_regular_price', array($this, 'maybe_change_product_price'), 2);

        // Clear price cache when WooCommerce does the same
        add_action('woocommerce_delete_product_transients', array('RightPress_Product_Price_Shop', 'clear_cache_for_product'));
    }

    /**
     * =================================================================================================================
     * PRICE HOOK CALLBACKS
     * =================================================================================================================
     */

    /**
     * Maybe change product price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float
     */
    public function maybe_change_product_price($price, $product)
    {

        return $this->maybe_change_price($price, $product);
    }

    /**
     * Maybe change product or variation price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float
     */
    public function maybe_change_price($price, $product)
    {

        // Get price type
        $price_type = $this->get_current_price_type();

        // Observe-only request
        if ($this->observe($price, $price_type, $product->get_id())) {
            return $price;
        }

        // Skip variable products (this does not affect individual variations)
        if ($product->is_type('variable')) {
            return $price;
        }

        // Skip products with no price set - they can't be purchased
        if ($price_type === 'price' && $price === '') {
            return $price;
        }

        // Skip if cart has not been loaded
        if (!did_action('woocommerce_cart_loaded_from_session') && !apply_filters('rightpress_product_price_shop_change_prices_before_cart_is_loaded', false)) {
            return $price;
        }

        // Skip products in cart
        if (!empty($product->rightpress_in_cart)) {
            return $price;
        }

        // Skip backend requests
        if (!RightPress_Help::is_request('frontend') && !apply_filters('rightpress_product_price_shop_change_prices_in_backend', false, $price, $price_type, $product)) {
            return $price;
        }

        // Product price live update in progress
        if (RightPress_Product_Price_Live_Update::is_processing_live_update_request()) {
            return $price;
        }

        // Maybe skip cache to calculate fresh prices for current request
        if ($this->skip_cache($product)) {
            return $this->calculate_price($price, $price_type, $product);
        }

        // Get price hash
        $price_hash = $this->get_price_hash($product, $price, $price_type);

        // Get cached price
        $cached_price = $this->get_cached_price($price_type, $product, $price_hash);

        // Price not in cache
        if ($cached_price === false) {

            // Calculate price
            $cached_price = $this->calculate_price($price, $price_type, $product);

            // Store price in cache
            $this->cache_price($cached_price, $price_type, $product, $price_hash);
        }

        // Return cached price
        return $cached_price;
    }

    /**
     * Maybe skip cache to calculate fresh prices for current request
     *
     * If true, prices won't be read from cache and calculated prices won't be written to cache
     *
     * @access public
     * @param object $product
     * @return bool
     */
    public function skip_cache($product)
    {

        // Allow plugins and 3rd party developers to skip caching
        return apply_filters('rightpress_product_price_shop_skip_cache', false, $product);
    }


    /**
     * =================================================================================================================
     * VARIABLE PRODUCT HANDLING
     * =================================================================================================================
     */

    /**
     * Get prices of visible variations for variable product
     *
     * Returned array has three arrays for each price type which in turn contains prices of a given type for all visible variations
     *
     * Prices are sorted from cheapest to most expensive
     *
     * @access public
     * @param object $variable_product
     * @param bool $for_display
     * @return array
     */
    public static function get_visible_variations_prices($variable_product, $for_display = false)
    {

        $prices         = array();
        $update_cache   = false;

        // Get instance
        $instance = RightPress_Product_Price_Shop::get_instance();

        // Get variable product id
        $product_id = $variable_product->get_id();

        // Get context
        $context = $for_display ? 'display' : 'raw';

        // Not yet in memory
        if (!isset($instance->visible_variations_prices[$product_id][$context])) {

            // Read cached prices from database
            $instance->read_cached_prices($product_id);

            // Check if cache should be skipped
            $skip_cache = $instance->skip_cache($variable_product);

            // Get variable product prices hash
            $variable_product_prices_hash = $instance->get_variable_product_prices_hash($variable_product, $for_display);

            // Iterate over visible variations ids
            foreach ($variable_product->get_visible_children() as $variation_id) {

                // Iterate over different price types
                foreach (array('price', 'sale_price', 'regular_price') as $price_type) {

                    // Skip cache completely
                    if ($skip_cache) {

                        // Load variation
                        if ($variation = wc_get_product($variation_id)) {

                            // Get current variation price
                            // TBD: Are we including adjustments by third parties?
                            $method = 'get_' . $price_type;
                            $price = $variation->$method('edit');

                            // Calculate price
                            $cached_price = $instance->calculate_price($price, $price_type, $variation, $for_display);
                        }
                        else {

                            $cached_price = '';
                        }
                    }
                    // Try to get price by variable product prices hash
                    else if (isset($instance->cache[$product_id][$instance->get_system_hash()][$price_type][$variation_id][$variable_product_prices_hash])) {

                        // Set price from cache by variable product prices hash
                        $cached_price = $instance->cache[$product_id][$instance->get_system_hash()][$price_type][$variation_id][$variable_product_prices_hash]['p'];
                    }
                    // Get price from regular cache
                    else {

                        // Load variation
                        if ($variation = wc_get_product($variation_id)) {

                            // Get current variation price
                            // TBD: Are we including adjustments by third parties?
                            $method = 'get_' . $price_type;
                            $price = $variation->$method('edit');

                            // Get price hash
                            $price_hash = $instance->get_price_hash($variation, $price, $price_type, $for_display);

                            // Get cached price
                            $cached_price = $instance->get_cached_price($price_type, $variation, $price_hash);

                            // Price not in cache
                            if ($cached_price === false) {

                                // Calculate price
                                $cached_price = $instance->calculate_price($price, $price_type, $variation, $for_display);

                                // Store price in cache
                                $instance->cache_price($cached_price, $price_type, $variation, $price_hash);
                            }

                            // Store price in cache
                            // Note: We store variation price in two locations - one for direct access using regular price hash
                            // (when we have variation object but don't have variable product object) and one for access by
                            // variable product prices hash (when we have variable product object and don't have variation object)
                            $instance->cache_price($cached_price, $price_type, $variation, $variable_product_prices_hash);
                        }
                        else {

                            $cached_price = '';
                        }
                    }

                    // Add price of current type of current variation to array
                    $prices[$price_type][$variation_id] = ($cached_price !== '' ? (float) $cached_price : '');
                }
            }

            // Sort prices
            foreach (array('price', 'sale_price', 'regular_price') as $price_type) {
                if (!empty($prices[$price_type])) {
                    asort($prices[$price_type]);
                }
            }

            // Store in memory
            $instance->visible_variations_prices[$product_id][$context] = $prices;
        }

        // Return cached values from memory
        return $instance->visible_variations_prices[$product_id][$context];
    }


    /**
     * =================================================================================================================
     * PRICE CALCULATION
     * =================================================================================================================
     */

    /**
     * Calculate price
     *
     * @access public
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @param bool $for_display         Used for variations only
     * @return float
     */
    public function calculate_price($price, $price_type, $product, $for_display = false)
    {

        // Allow plugins to skip calculation of product price
        if (apply_filters('rightpress_product_price_shop_skip_calculation', false, $price, $price_type, $product)) {
            return $price;
        }

        // Set flag
        $this->calculating = true;

        // Calculate final price by price test if at least one plugin requests this approach
        if ($price_type === 'price' && apply_filters('rightpress_product_price_shop_calculate_by_price_test', false, $price, $price_type, $product)) {

            $price = $this->calculate_price_by_price_test($price, $price_type, $product);
        }
        // Calculate price of any type statically
        else {

            $price = $this->calculate_price_statically($price, $price_type, $product);
        }

        // Maybe prepare price for display
        if ($for_display && $price !== '') {

            $price = RightPress_Product_Price_Display::prepare_product_price_for_display($product, $price, false, true);
        }

        // Unset flag
        $this->calculating = true;

        // Return price
        return $price;
    }

    /**
     * Calculate price by price test
     *
     * @access private
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return float
     */
    private function calculate_price_by_price_test($price, $price_type, $product)
    {

        // Get variation attributes
        $variation_attributes = $product->is_type('variation') ? $product->get_variation_attributes() : array();

        // Run product price test
        $test_price = RightPress_Product_Price_Test::run($product, 1, $variation_attributes, false, false, array(), $price);

        // Check if price was actually adjusted
        if ($test_price !== false && $test_price !== null && RightPress_Product_Price::prices_differ($test_price, $price)) {

            // Set test price
            $price = $test_price;
        }

        // Return calculated price
        return $price;
    }

    /**
     * Calculate price statically
     *
     * @access private
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return float
     */
    private function calculate_price_statically($price, $price_type, $product)
    {

        try {

            // Get empty calculation data array
            $calculation_data = $this->get_calculation_data($price, $price_type, $product);

            // Get price calculation callbacks
            $callbacks = apply_filters('rightpress_product_price_shop_calculation_callbacks', array());

            // Get price changes from callbacks with alternatives
            foreach ($callbacks as $callback) {
                $calculation_data = call_user_func($callback, $calculation_data, $price_type, $product);
            }

            // Finalize base price selection and incorporate selected alternative
            $calculation_data = RightPress_Product_Price_Shop::finalize_base_price_selection($calculation_data);

            // Check if price was actually adjusted
            if (RightPress_Product_Price::prices_differ($calculation_data['price'], $calculation_data['base_price'])) {

                // Set adjusted price
                $price = $calculation_data['price'];
            }
        }
        catch (RightPress_Product_Price_Exception $e) {

            // Empty sale price
            if ($e->get_error_code() === 'empty_price') {

                $price = '';
            }
            // Propagate other exceptions
            else {

                throw $e;
            }
        }

        // Return calculated price
        return $price;
    }

    /**
     * Get empty calculation data array
     *
     * @access private
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return array
     */
    public function get_calculation_data($price, $price_type, $product)
    {

        // Cast price to float
        $price = (float) $price;

        // Format calculation data array
        $calculation_data = array(

         // Commented items are added to the main array after base price selection is finalized
         // 'price'         => $price       // Calculated product price
         // 'base_price'    => $price,      // Price that calculations were based on

            'changes'       => array(),     // Changes that were applicable to product price, empty changes array means that price was not adjusted, each plugin must add its own array of plugin-specific changes
            'alternatives'  => array(),     // Base price candidates and corresponding pricing data
        );

        // Get all potential base prices
        $base_price_key         = RightPress_Product_Price::get_price_key($price);
        $base_price_candidates  = apply_filters('rightpress_product_price_shop_base_price_candidates', array($base_price_key => $price), $price, $price_type, $product);

        // Add base price candidates to calculation data
        foreach ($base_price_candidates as $base_price_candidate_key => $base_price_candidate) {
            $calculation_data['alternatives'][$base_price_candidate_key] = array(
                'price'         => $base_price_candidate,
                'base_price'    => $base_price_candidate,
            );
        }

        // Return calculation data
        return $calculation_data;
    }

    /**
     * Finalize base price selection and incorporate selected alternative
     *
     * @access private
     * @param array $calculation_data
     * @return array
     */
    private static function finalize_base_price_selection($calculation_data)
    {

        // Alternatives not yet incorporated
        if (isset($calculation_data['alternatives'])) {

            // Get default base price key
            reset($calculation_data['alternatives']);
            $default_base_price_key = key($calculation_data['alternatives']);

            // Allow plugins to change selected base price key
            $base_price_key = apply_filters('rightpress_product_price_selected_shop_base_price_key', $default_base_price_key, $calculation_data);

            // Move selected alternative data to the main array and unset alternatives array
            $calculation_data = array_merge($calculation_data['alternatives'][$base_price_key], $calculation_data);
            unset($calculation_data['alternatives']);
        }

        // Return calculation data with incorporated selected alternative
        return $calculation_data;
    }


    /**
     * =================================================================================================================
     * PRICE CACHING
     * =================================================================================================================
     */

    /**
     * Read cached prices from database
     *
     * @access public
     * @param int $product_id
     * @return void
     */
    public function read_cached_prices($product_id)
    {

        // Prices for this product not yet in memory
        if (!isset($this->cache[$product_id])) {

            // Get cached prices from database and store in memory
            $this->cache[$product_id] = array_filter((array) json_decode(strval(get_transient($this->get_transient_name($product_id))), true));
        }
    }

    /**
     * Get valid cached price
     *
     * @access public
     * @param string $price_type
     * @param object $product
     * @param string $price_hash
     * @return float|bool
     */
    public function get_cached_price($price_type, $product, $price_hash)
    {

        $cached_price = false;

        // Get product id
        // Note: We get parent variable product id in case of product variation since price cache for variations is stored under parent id
        $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

        // Read cached prices from database
        $this->read_cached_prices($product_id);

        // Product is variation
        if ($product->is_type('variation')) {

            // Get variation id
            $variation_id = $product->get_id();

            // Check if price exists in cache
            if (isset($this->cache[$product_id][$this->get_system_hash()][$price_type][$variation_id][$price_hash]['p'])) {

                // Get price from cache
                $cached_price = $this->cache[$product_id][$this->get_system_hash()][$price_type][$variation_id][$price_hash]['p'];
            }
        }
        // Product is not variation
        else {

            // Check if price exists in cache
            if (isset($this->cache[$product_id][$this->get_system_hash()][$price_type][$price_hash]['p'])) {

                // Get price from cache
                $cached_price = $this->cache[$product_id][$this->get_system_hash()][$price_type][$price_hash]['p'];
            }
        }

        // Return cached price
        return $cached_price;
    }

    /**
     * Cache price
     *
     * @access public
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @param string $price_hash
     * @return void
     */
    public function cache_price($price, $price_type, $product, $price_hash)
    {

        // Wrap price
        $price_data = array(
            'p' => $price,
            't' => time(),
        );

        // Product is variation
        if ($product->is_type('variation')) {

            // Get product ids
            $product_id     = $product->get_parent_id();
            $variation_id   = $product->get_id();

            // Set to cache array
            $this->cache[$product_id][$this->get_system_hash()][$price_type][$variation_id][$price_hash] = $price_data;
        }
        // Product is not variation
        else {

            // Get product id
            $product_id = $product->get_id();

            // Set to cache array
            $this->cache[$product_id][$this->get_system_hash()][$price_type][$price_hash] = $price_data;
        }

        // Set flag
        $this->cache[$product_id]['_update'] = true;

        // Store cached prices in product meta on shutdown
        if ($this->store === false) {
            register_shutdown_function(array($this, 'store_cached_prices'));
            $this->store = true;
        }
    }

    /**
     * Store cached prices in product meta
     *
     * @access public
     * @return void
     */
    public function store_cached_prices()
    {

        // Iterate over cache entries
        foreach ($this->cache as $product_id => $values) {

            // Store updated entries only
            if (!empty($values['_update'])) {

                // Remove flag
                unset($values['_update']);

                // Cleanup
                $values = $this->cache_cleanup($values);

                // Update transient
                set_transient($this->get_transient_name($product_id), wp_json_encode($values), DAY_IN_SECONDS * 30);
            }
        }
    }

    /**
     * Product prices cache periodic cleanup
     *
     * Drop individual prices older than 30 days
     * This is generic cleanup in case dynamic nature of some plugins generate a lot of different price hashes
     *
     * @access public
     * @param array $values
     * @return array
     */
    public function cache_cleanup($values)
    {

        // Get cutoff timestamp
        $cutoff_timestamp = time() - (DAY_IN_SECONDS * 30);

        foreach ($values as $system_hash => $level_1) {
            foreach ($level_1 as $price_type => $level_2) {
                foreach ($level_2 as $key => $level_3) {

                    // Not product variation
                    if (isset($level_3['p'])) {

                        // Check if record is older than 30 days
                        if ($level_3['t'] < $cutoff_timestamp) {

                            // Unset record
                            unset($values[$system_hash][$price_type][$key]);
                        }
                    }
                    // Product variation
                    else {

                        foreach ($level_3 as $price_hash => $level_4) {

                            // Check if record is older than 30 days
                            if ($level_4['t'] < $cutoff_timestamp) {

                                // Unset record
                                unset($values[$system_hash][$price_type][$key][$price_hash]);
                            }
                        }

                        if (empty($values[$system_hash][$price_type][$key])) {
                            unset($values[$system_hash][$price_type][$key]);
                        }
                    }
                }

                if (empty($values[$system_hash][$price_type])) {
                    unset($values[$system_hash][$price_type]);
                }
            }

            if (empty($values[$system_hash])) {
                unset($values[$system_hash]);
            }
        }

        return $values;
    }

    /**
     * Clear cache for product
     *
     * @access public
     * @param object|int $product
     * @return void
     */
    public static function clear_cache_for_product($product)
    {

        // Get instance
        $instance = RightPress_Product_Price_Shop::get_instance();

        // Get product id
        $product_id = is_a($product, 'WC_Product') ? $product->get_id() : $product;

        // Delete transient
        delete_transient($instance->get_transient_name($product_id));
    }

    /**
     * Get system hash
     *
     * @access public
     * @return string
     */
    public function get_system_hash()
    {

        // System hash not yet defined
        if ($this->system_hash === null) {

            // Format hash data
            $hash_data = array(
                $this->get_wc_product_transient_version(),
            );

            // Allow plugins to add their own system data
            // Note: Keep null as argument for cross-version compatibility when more than one plugin is running
            $hash_data = apply_filters('rightpress_product_price_shop_settings_hash_data', $hash_data, null);

            // Get hash and set
            $this->system_hash = RightPress_Help::get_hash(false, $hash_data);
        }

        // Return system hash from memory
        return $this->system_hash;
    }

    /**
     * Get price hash
     *
     * @access public
     * @param object $product
     * @param float $price
     * @param string $price_type
     * @param bool $for_display
     * @return string
     */
    public function get_price_hash($product, $price, $price_type, $for_display = false)
    {

        // Format price hash data
        $hash_data = array(
            'rightpress' => array(
                $price_type,
                (float) $price,
                (float) $product->get_price('edit'),
                (float) $product->get_regular_price('edit'),
                (float) $product->get_sale_price('edit'),
                (($for_display && wc_tax_enabled()) ? array(wc_prices_include_tax(), get_option('woocommerce_tax_display_shop', 'excl'), WC_Tax::get_rates(), $product->get_tax_class()) : array(false)),
            )
        );

        // Allow plugins to add their own data
        $hash_data = apply_filters('rightpress_product_price_shop_cache_hash_data', $hash_data, $price, $price_type, $product);

        // Get hash
        $hash = RightPress_Help::get_hash(false, $hash_data);

        return $hash;
    }

    /**
     * Get variable product prices hash
     *
     * Adapted from WooCommerce 3.0 method WC_Product_Variable_Data_Store_CPT::get_price_hash()
     *
     * @access public
     * @param object $product
     * @param bool $for_display
     * @return string
     */
    public function get_variable_product_prices_hash($product, $for_display = false)
    {

        global $wp_filter;

        $hash_data      = ($for_display && wc_tax_enabled()) ? array(get_option('woocommerce_tax_display_shop', 'excl'), WC_Tax::get_rates()) : array(false);
        $filter_names   = array('woocommerce_variation_prices_price', 'woocommerce_variation_prices_regular_price', 'woocommerce_variation_prices_sale_price');

        foreach ($filter_names as $filter_name) {
            if (!empty($wp_filter[$filter_name])) {

                $hash_data[$filter_name] = array();

                foreach ($wp_filter[$filter_name] as $priority => $callbacks) {
                    $hash_data[$filter_name][] = array_values(wp_list_pluck($callbacks, 'function'));
                }
            }
        }

        $hash_data = apply_filters('woocommerce_get_variation_prices_hash', $hash_data, $product, $for_display);

        // In addition we also check if prices include tax
        $hash_data[] = ($for_display && wc_tax_enabled()) ? wc_prices_include_tax() : false;

        // Allow plugins to add their own data
        $hash_data = apply_filters('rightpress_product_price_shop_variable_product_prices_hash_data', $hash_data, $product);

        // Get hash
        $hash = RightPress_Help::get_hash(false, $hash_data);

        return $hash;
    }

    /**
     * Get WooCommerce product transient version
     *
     * @access public
     * @return string
     */
    public function get_wc_product_transient_version()
    {

        // Get WooCommerce product transient version
        if (class_exists('WC_Cache_Helper') && method_exists('WC_Cache_Helper', 'get_transient_version') && is_callable(array('WC_Cache_Helper', 'get_transient_version'))) {

            // Get WooCommerce product transient version
            $product_transient_version = (string) WC_Cache_Helper::get_transient_version('product');
        }
        // WooCommerce get_transient_version() method no longer callable
        else {

            // Write warning to error log
            RightPress_Help::doing_it_wrong(__METHOD__, 'Method WC_Cache_Helper::get_transient_version no longer callable.', '1.0');

            // Get random string (cache will be reset on each page load)
            $product_transient_version = RightPress_Help::get_hash();
        }

        return $product_transient_version;
    }

    /**
     * Get transient name
     *
     * @access public
     * @param int $product_id
     * @return string
     */
    public function get_transient_name($product_id)
    {

        return 'rightpress_prices_' . $product_id;
    }


    /**
     * =================================================================================================================
     * PRICE OBSERVATION
     * =================================================================================================================
     */

    /**
     * Start price observation
     *
     * @access public
     * @return void
     */
    public static function start_observation()
    {

        // Get instance
        $instance = RightPress_Product_Price_Shop::get_instance();

        // Preset property
        $instance->observing = array();
    }

    /**
     * Get observed prices and clear property
     *
     * @access public
     * @return array
     */
    public static function get_observed()
    {

        // Get instance
        $instance = RightPress_Product_Price_Shop::get_instance();

        // Reference observed prices
        $observed = $instance->observing;

        // Reset property
        $instance->observing = null;

        // Return observed prices
        return $observed;
    }

    /**
     * Observe price
     *
     * @access public
     * @param float $price
     * @param string $price_type
     * @param int $product_id
     * @return void
     */
    public function observe($price, $price_type, $product_id)
    {

        if (is_array($this->observing)) {
            $this->observing[$product_id][$price_type] = $price;
            return true;
        }

        return false;
    }

    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get current price type by filter hook
     *
     * @access public
     * @return string
     */
    public function get_current_price_type()
    {

        // Get current filter
        $current_filter = current_filter();

        // Get price type
        if (strstr($current_filter, 'regular')) {
            return 'regular_price';
        }
        else if (strstr($current_filter, 'sale')) {
            return 'sale_price';
        }
        else {
            return 'price';
        }
    }

    /**
     * Check if system is calculating shop product price
     *
     * @access public
     * @return bool
     */
    public static function is_calculating()
    {

        // Get instance
        $instance = RightPress_Product_Price_Shop::get_instance();

        // Return result
        return $instance->calculating;
    }




}

RightPress_Product_Price_Shop::get_instance();

}
