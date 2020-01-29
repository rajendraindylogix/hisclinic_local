<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price_Changes')) {

/**
 * RightPress Shared Product Price Changes
 *
 * @class RightPress_Product_Price_Changes
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Changes
{

    // Flags
    private $getting_price_changes_for_cart_items           = false;
    private $cart_item_price_changes_first_stage_processed  = false;
    private $cart_item_price_changes_third_stage_processed  = false;

    // Store some data in memory
    private $second_stage_reference_prices  = array();
    private $cart_item_sorting_prices       = array();
    private $cart_item_sorting_prices_test  = array();

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

    }


    /**
     * =================================================================================================================
     * MAIN PRICE CHANGES FLOW
     * =================================================================================================================
     */

    /**
     * Get price changes for cart items
     *
     * This method can be called multiple times during the same request for the same cart - plugins need to make sure
     * they don't apply the same pricing adjustment more than once when this is undesirable.
     *
     * This method can be called for both real cart ($cart is set) and simulated cart during price tests (in which case $test_cart_items is not empty)
     *
     * If cart item key is not set in a resulting array then that cart item has no changes to prices
     *
     * @access public
     * @param array $cart_items
     * @param array $test_cart_items
     * @param bool $return_empty_changes
     * @param float $custom_price
     * @return array
     */
    public static function get_price_changes_for_cart_items($cart_items, $test_cart_items = array(), $return_empty_changes = false, $custom_price = null)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Set flag
        $instance->getting_price_changes_for_cart_items = true;

        // Check if current call is price test
        $is_test = !empty($test_cart_items);

        // Check if this is the first real cart call
        $is_first_call = !$instance->cart_item_price_changes_first_stage_processed;

        // Prepare price changes array for processing
        $price_changes = RightPress_Product_Price_Changes::prepare_price_changes_array_for_processing($cart_items, $is_test, $custom_price);

        /**
         * FIRST STAGE: PART ONE
         *
         * - Runs once on first call for real cart only
         * - Runs on every call during price tests
         * - Works with multiple alternative price changes sets until final base price selection
         * - Currently only one alternative price by WCDPD is expected and alternative price is selected if WCDPD has adjustments to apply for a particular cart item
         */

        // Make sure first stage has not been processed yet
        if (!$instance->cart_item_price_changes_first_stage_processed || $is_test) {

            // Get cart item price changes first stage callbacks
            $callbacks = apply_filters('rightpress_product_price_cart_item_price_changes_first_stage_callbacks', array());

            // Get price changes from callbacks with alternatives
            foreach ($callbacks as $callback) {
                $price_changes = RightPress_Product_Price_Changes::get_price_changes_from_callback_with_alternatives($price_changes, $callback, $cart_items, $test_cart_items);
            }

            // Update sorting prices
            RightPress_Product_Price_Changes::update_sorting_prices($price_changes, $is_test);

            // Set flag
            $instance->cart_item_price_changes_first_stage_processed = true;
        }

        // Allow second stage members to prepare second stage price changes for cart items in advance
        if (!$instance->cart_item_price_changes_third_stage_processed || $is_test) {

            // Prepare reference prices for second stage price changes calculation
            RightPress_Product_Price_Changes::prepare_second_stage_reference_prices($price_changes, $cart_items);

            // Trigger action
            do_action('rightpress_product_price_prepare_second_stage_cart_item_price_changes', $cart_items);
        }

        // Finalize base price selection and incorporate selected alternative
        $price_changes = RightPress_Product_Price_Changes::finalize_base_price_selection($price_changes);

        /**
         * SECOND STAGE
         *
         * - Runs on every call for real cart but only before third stage
         * - Runs on every call during price tests
         */

        // Make sure third stage has not been processed yet
        if (!$instance->cart_item_price_changes_third_stage_processed || $is_test) {

            // Get second stage callbacks
            $callbacks = apply_filters('rightpress_product_price_cart_item_price_changes_second_stage_callbacks', array());

            // Get price changes from callbacks
            foreach ($callbacks as $callback) {
                $price_changes = RightPress_Product_Price_Changes::get_price_changes_from_callback($price_changes, $callback, $cart_items, $test_cart_items);
            }
        }

        /**
         * THIRD STAGE
         *
         * - Runs once as late as possible for real cart to allow time for potential subsequent second stage runs
         * - Runs on every call during price tests
         */

        // Make sure third stage has not been processed yet and this is not the first call
        if ((!$is_first_call && !$instance->cart_item_price_changes_third_stage_processed) || $is_test) {

            // Get third stage callbacks
            $callbacks = apply_filters('rightpress_product_price_cart_item_price_change_third_stage_callbacks', array());

            // Get price changes from callbacks
            foreach ($callbacks as $callback) {
                $price_changes = RightPress_Product_Price_Changes::get_price_changes_from_callback($price_changes, $callback, $cart_items, $test_cart_items);
            }

            // Set flag
            $instance->cart_item_price_changes_third_stage_processed = true;
        }

        /**
         * POST PROCESSING
         *
         * - Performs price test related array transformations
         * - Aggregates change data from price ranges
         * - Cleans up price changes array
         */

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {

            // Price test in progress
            if ($is_test) {

                // Unset changes to other cart items
                if (!isset($test_cart_items[$cart_item_key])) {
                    unset($price_changes[$cart_item_key]);
                    continue;
                }

                // Unset changes to non-test quantity units
                $price_changes[$cart_item_key]['prices'] = RightPress_Product_Price_Breakdown::filter_prices_array_for_test_quantity($price_changes[$cart_item_key]['prices'], $test_cart_items[$cart_item_key]);
            }

            // Incorporate new changes for cart item
            RightPress_Product_Price_Changes::incorporate_new_changes_for_cart_item($price_changes[$cart_item_key]['prices'], $price_changes[$cart_item_key]);

            // Remove cart item from array if it does not have any changes at all
            if (empty($price_changes[$cart_item_key]['all_changes']) && !$return_empty_changes) {
                unset($price_changes[$cart_item_key]);
                continue;
            }

            // Get price to set from prices array
            $price_to_set = RightPress_Product_Price_Breakdown::get_price_from_prices_array($price_changes[$cart_item_key]['prices'], $price_changes[$cart_item_key]['original_price'], $cart_items[$cart_item_key]['data'], $cart_items[$cart_item_key]);

            // Allow developers to override and set to price changes array
            $price_changes[$cart_item_key]['price'] = apply_filters('rightpress_product_price_changes_price_to_set', $price_to_set, $cart_item_key, $price_changes, $cart_items);
        }

        // Unset flag
        $instance->getting_price_changes_for_cart_items = false;

        // Return price changes
        return $price_changes;
    }

    /**
     * Incorporate new changes for cart item
     *
     * Note: This method is used in this class but it may also be used by individual plugins
     * when calculating their own changes for their custom purposes (in which case $cart_item_changes is null)
     *
     * @access public
     * @param $cart_item_changes
     * @return void
     */
    public static function incorporate_new_changes_for_cart_item(&$prices, &$cart_item_changes = null)
    {

        // Iterate over price ranges of current cart item
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Filter out empty plugin changes arrays
            $price_range_new_changes = array_filter($price_range['new_changes']);

            // Incorporate new price range changes
            foreach ($price_range_new_changes as $plugin_key => $plugin_changes) {

                // Add to price range all changes array
                RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($prices['ranges'][$price_range_index]['all_changes'], $plugin_key, $plugin_changes);

                // Check if cart item changes is defined
                if ($cart_item_changes !== null) {

                    // Add to price changes new changes array
                    RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($cart_item_changes['new_changes'], $plugin_key, $plugin_changes);

                    // Add to price changes all changes array
                    RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($cart_item_changes['all_changes'], $plugin_key, $plugin_changes);
                }
            }
        }
    }

    /**
     * Incorporate new price range plugin changes
     *
     * @access public
     * @param array $destination_array
     * @param string $plugin_key
     * @param array $plugin_changes
     * @return void
     */
    public static function incorporate_new_price_range_plugin_changes(&$destination_array, $plugin_key, $plugin_changes)
    {
        // Plugin changes array does not exist in destination array yet
        if (!isset($destination_array[$plugin_key])) {
            $destination_array[$plugin_key] = array();
        }

        // Merge new plugin changes with existing changes in destination array
        $destination_array[$plugin_key] = array_merge($destination_array[$plugin_key], $plugin_changes);
    }

    /**
     * Prepare price changes array for processing
     *
     * @access private
     * @param array $cart_items
     * @param bool $is_test
     * @param float $custom_price
     * @return array
     */
    private static function prepare_price_changes_array_for_processing($cart_items, $is_test, $custom_price = null)
    {

        // Prepare main price changes array
        $price_changes = array();

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Get cart item price changes stored in memory
            $price_changes_in_memory = RightPress_Product_Price_Cart::get_cart_item_price_changes($cart_item_key);

            // Update price changes array that was stored in memory
            if (!empty($price_changes_in_memory)) {

                // Reset new changes array
                $price_changes_in_memory['new_changes'] = array();

                // Reset new changes arrays in price ranges
                foreach ($price_changes_in_memory['prices']['ranges'] as $price_range_index => $price_range) {
                    $price_changes_in_memory['prices']['ranges'][$price_range_index]['new_changes'] = array();
                }

                // Get current cart item product price
                $current_price = (float) ($custom_price !== null ? $custom_price : $cart_item['data']->get_price('edit'));

                // Check if cart item product price has been updated by 3rd parties since last call
                //
                // Note: This is not a desired behaviour, we lose some data and may not be able to display price breakdown
                // correctly etc, however its still better to try to incorporate 3rd party changes instead of dropping them
                //
                // Note: We can't know for sure or reconstruct how 3rd parties manipulated the price (whether it was a fixed
                // price change, percentage discount or so) so we just drop everything that we have and start all over
                if (RightPress_Product_Price::prices_differ($price_changes_in_memory['price'], $current_price)) {

                    // Update price
                    $price_changes_in_memory['price'] = $current_price;

                    // Update base price
                    $price_changes_in_memory['base_price'] = $current_price;

                    // Update original price
                    $price_changes_in_memory['original_price'] = $current_price;

                    // Update prices in price ranges
                    foreach ($price_changes_in_memory['prices']['ranges'] as $price_range_index => $price_range) {

                        // Update price
                        $price_changes_in_memory['prices']['ranges'][$price_range_index]['price'] = $current_price;

                        // Update base price
                        $price_changes_in_memory['prices']['ranges'][$price_range_index]['base_price'] = $current_price;

                        // Maybe update highest price
                        if ($current_price > $price_changes_in_memory['prices']['ranges'][$price_range_index]['highest_price']) {
                            $price_changes_in_memory['prices']['ranges'][$price_range_index]['highest_price'] = $current_price;
                        }
                    }
                }
            }

            // Reuse existing price changes array from previous call
            if (!empty($price_changes_in_memory) && !$is_test) {

                $price_changes[$cart_item_key] = $price_changes_in_memory;
            }
            // Get new price changes array if one does not exist or if price test is in progress
            else {

                $price_changes[$cart_item_key] = RightPress_Product_Price_Changes::get_empty_price_changes_array($cart_item, $cart_item_key, $price_changes_in_memory, $custom_price);
            }
        }

        // Return price changes array for processing
        return $price_changes;
    }

    /**
     * Get empty price changes array for cart item
     *
     * Price changes in memory is only set for price test calls since we need to refer to initial prices
     *
     * Note: This must only be called from RightPress_Product_Price_Changes::get_price_changes_for_cart_items(),
     * otherwise there could be problems with base price selection by plugins
     *
     * WARNING! TBD! If changes are made to format of this array, they must also be made in RightPress_Product_Price_Test::merge_cart_items_price_changes()
     *
     * @access private
     * @param array $cart_item
     * @param string $cart_item_key
     * @param array $price_changes_in_memory
     * @param float $custom_price
     * @return array
     */
    private static function get_empty_price_changes_array($cart_item, $cart_item_key, $price_changes_in_memory = array(), $custom_price = null)
    {

        // Get default price
        $default_price = (float) ($custom_price !== null ? $custom_price : $cart_item['data']->get_price('edit'));

        // Get prices
        $base_price     = !empty($price_changes_in_memory) ? $price_changes_in_memory['base_price']     : $default_price;
        $original_price = !empty($price_changes_in_memory) ? $price_changes_in_memory['original_price'] : $default_price;

        // Format price changes array
        $price_changes = array(

         // Commented items are added to the main array after base price selection is finalized before second stage
         // 'prices'            => $prices,         // See RightPress_Product_Price_Breakdown::generate_prices_array() for details
         // 'price'             => $base_price,     // Single price for all quantity units or average price of prices, only updated at the very end of the process, plugins must work exclusively with the prices array
         // 'base_price'        => $base_price,     // Price that calculations was based on - may be reset to "regular price" (may include 3rd party changes)

            'original_price'    => $original_price, // Price of the cart item's product just like it was on first call (may include 3rd party changes)
            'new_changes'       => array(),         // Changes that were applicable to prices during current call, aggregated from price ranges new changes arrays, cleared at the beginning of each call
            'all_changes'       => array(),         // All changes that were applicable to prices, new changes are merged into all changes array at the end of each call
            'alternatives'      => array(),         // This stores alternative price calculations during the first stage, when base price selection is finalized one alternative is chosen, its contents are merged with the main array and this element is nulled
        );

        // Format base price candidates array
        $base_price_key         = RightPress_Product_Price::get_price_key($base_price);
        $base_price_candidates  = array($base_price_key => $base_price);

        // Allow plugins to add base price candidates on first call for current cart item
        // Note: If $custom_price is provided, only this price is considered
        if (empty($price_changes_in_memory) && $custom_price === null) {
            $base_price_candidates  = apply_filters('rightpress_product_price_cart_item_base_price_candidates', $base_price_candidates, $cart_item['data'], $cart_item_key, $cart_item);
        }

        // Iterate over base price candidates
        foreach ($base_price_candidates as $base_price_candidate_key => $base_price_candidate) {

            // Generate prices array
            $prices = RightPress_Product_Price_Breakdown::generate_prices_array($base_price_candidate, $cart_item['quantity'], $cart_item['data']);

            // Set as alternative
            // Note: Currently it is important to preserve initial order of base price alternatives as the first element of the array is considered to default one
            $price_changes['alternatives'][$base_price_candidate_key] = array(
                'prices'        => $prices,
                'price'         => $base_price_candidate,
                'base_price'    => $base_price_candidate,
            );
        }

        // Return empty price changes array
        return $price_changes;
    }

    /**
     * Finalize base price selection and incorporate selected alternative
     *
     * @access private
     * @param array $price_changes
     * @return array
     */
    private static function finalize_base_price_selection($price_changes)
    {

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {

            // Alternatives not yet incorporated
            if (isset($cart_item_changes['alternatives'])) {

                // Get default base price key for current cart item
                reset($cart_item_changes['alternatives']);
                $default_base_price_key = key($cart_item_changes['alternatives']);

                // Allow plugins to change selected cart item base price key
                $base_price_key = apply_filters('rightpress_product_price_selected_cart_item_base_price_key', $default_base_price_key, array_keys($cart_item_changes['alternatives']), $cart_item_key);

                // Move selected alternative data to the main array and unset alternatives array
                $price_changes[$cart_item_key] = array_merge($cart_item_changes['alternatives'][$base_price_key], $cart_item_changes);
                unset($price_changes[$cart_item_key]['alternatives']);
            }
        }

        // Return price changes array with incorporated selected alternative
        return $price_changes;
    }

    /**
     * Prepare reference prices for second stage price changes calculation
     *
     * Note: We only set reference price for specific cart item once per request as sort order must remain the same,
     * otherwise one of our plugins may apply unexpected price changes when reference prices change dynamically
     *
     * @access private
     * @param array $price_changes
     * @param array $cart_items
     * @return void
     */
    private static function prepare_second_stage_reference_prices($price_changes, $cart_items)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_price_changes) {

            // Skip cart items for which reference prices are already set
            if (isset($instance->second_stage_reference_prices[$cart_item_key])) {
                continue;
            }

            // Alternatives still not merged, use last alternative array in list
            // Note: Currently we only expecte one (default) or two (WCDPD regular price override) alternatives and the
            // last one is always fit for the only plugin (WCDPD) that is currently in second stage
            if (isset($cart_item_price_changes['alternatives'])) {

                $current_changes = array_pop($cart_item_price_changes['alternatives']);
            }
            // Alternatives incorporated, use main array
            else {

                $current_changes = $cart_item_price_changes;
            }

            // Set reference price
            $instance->second_stage_reference_prices[$cart_item_key] = RightPress_Product_Price_Breakdown::get_price_from_prices_array($current_changes['prices'], $cart_item_price_changes['original_price'], $cart_items[$cart_item_key]['data'], $cart_items[$cart_item_key], true);
        }
    }

    /**
     * Get reference price for second stage price changes calculation
     *
     * @access public
     * @param string $cart_item_key
     * @return float|null
     */
    public static function get_second_stage_reference_price($cart_item_key)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Check if price changes are being processed
        if ($instance->getting_price_changes_for_cart_items) {

            // Check if reference price is defined for current cart item
            if (isset($instance->second_stage_reference_prices[$cart_item_key])) {
                return $instance->second_stage_reference_prices[$cart_item_key];
            }
        }

        // Reference price is not defined
        return null;
    }

    /**
     * Get price changes from callback
     *
     * @access private
     * @param array $price_changes
     * @param mixed $callback
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    private static function get_price_changes_from_callback($price_changes, $callback, $cart_items, $test_cart_items = array())
    {

        // Get prices changes from current plugin
        $price_changes = call_user_func($callback, $price_changes, $cart_items, $test_cart_items);

        // Post callback cart item price changes processing
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
             RightPress_Product_Price_Changes::post_callback_cart_item_price_changes_processing($price_changes[$cart_item_key], $cart_items, $cart_item_key);
        }

        // Return price changes
        return $price_changes;
    }

    /**
     * Get price changes from callback with alternatives
     *
     * @access private
     * @param array $price_changes
     * @param mixed $callback
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    private static function get_price_changes_from_callback_with_alternatives($price_changes, $callback, $cart_items, $test_cart_items = array())
    {

        // Get prices changes from current plugin
        $price_changes = call_user_func($callback, $price_changes, $cart_items, $test_cart_items);

        // Post callback cart item price changes processing
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
            foreach ($cart_item_changes['alternatives'] as $base_price_candidate_key => $alternative_changes) {
                RightPress_Product_Price_Changes::post_callback_cart_item_price_changes_processing($price_changes[$cart_item_key]['alternatives'][$base_price_candidate_key], $cart_items, $cart_item_key);
            }
        }

        // Return price changes
        return $price_changes;
    }

    /**
     * Post callback cart item price changes processing
     *
     * Note: $cart_item_changes may actualy be one of the alternative changes, therefore we can only work with
     * data that is set on alternative changes arrays
     *
     * @access private
     * @param array $cart_item_changes
     * @param array $cart_items
     * @param string $cart_item_key
     * @return void
     */
    private static function post_callback_cart_item_price_changes_processing(&$cart_item_changes, $cart_items, $cart_item_key)
    {

        // Reset price ranges sort order
        RightPress_Product_Price_Breakdown::reset_price_ranges_sort_order($cart_item_changes['prices']);

        // Update highest price of each price range
        foreach ($cart_item_changes['prices']['ranges'] as $price_range_key => $price_range) {
            if ($price_range['price'] > $price_range['highest_price']) {
                $cart_item_changes['prices']['ranges'][$price_range_key]['highest_price'] = $price_range['price'];
            }
        }
    }


    /**
     * =================================================================================================================
     * CART ITEM SORTING
     * =================================================================================================================
     */

    /**
     * Sort cart items by price
     *
     * @access public
     * @param array $cart_items
     * @param string $sort_order
     * @param bool $use_sorting_price
     * @return array
     */
    public static function sort_cart_items_by_price($cart_items = null, $sort_order = 'ascending', $use_sorting_price = false)
    {

        // Get cart items if not passed in
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Sort cart items
        $sort_comparison_method = 'sort_cart_items_by_price_' . $sort_order . '_comparison';
        RightPress_Help::stable_uasort($cart_items, array('RightPress_Product_Price_Changes', $sort_comparison_method), array('use_sorting_price' => $use_sorting_price));

        // Return sorted cart items
        return $cart_items;
    }

    /**
     * Sort cart items by price ascending comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_ascending_comparison($a, $b, $params = array())
    {

        return RightPress_Product_Price_Changes::sort_cart_items_by_price_comparison($a, $b, 'ascending', $params);
    }

    /**
     * Sort cart items by price descending comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_descending_comparison($a, $b, $params = array())
    {

        return RightPress_Product_Price_Changes::sort_cart_items_by_price_comparison($a, $b, 'descending', $params);
    }

    /**
     * Sort cart items by price comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param string $sort_order
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_comparison($a, $b, $sort_order, $params = array())
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Select correct sorting prices array
        $sorting_prices = RightPress_Product_Price_Test::is_running() ? $instance->cart_item_sorting_prices_test : $instance->cart_item_sorting_prices;

        // Get cart item prices
        $price_a = (!empty($params['use_sorting_price']) && isset($sorting_prices[$a['key']])) ? $sorting_prices[$a['key']] : (float) $a['data']->get_price();
        $price_b = (!empty($params['use_sorting_price']) && isset($sorting_prices[$b['key']])) ? $sorting_prices[$b['key']] : (float) $b['data']->get_price();

        // Prices are the same
        if (!RightPress_Product_Price::prices_differ($price_a, $price_b)) {
            return 0;
        }

        // Compare prices
        if (($price_a - $price_b) < 0) {
            return ($sort_order === 'ascending' ? -1 : 1);
        }
        else {
            return ($sort_order === 'ascending' ? 1 : -1);
        }
    }

    /**
     * Update sorting prices
     *
     * @access private
     * @param array $price_changes
     * @param bool $is_test
     * @return void
     */
    private static function update_sorting_prices($price_changes, $is_test)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Copy sorting prices for price test
        if ($is_test) {
            $instance->cart_item_sorting_prices_test = $instance->cart_item_sorting_prices;
        }

        // Set sorting price to the last alternative price
        // Note: Sorting price may be used by second stage members to get a correct cart item order for pricing adjustments (WCDPD issue #556)
        // Note: Sorting prices must only be set once per request at the very beginning (the point of having them is that they won't change during request)
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
            foreach ($cart_item_changes['alternatives'] as $base_price_candidate_key => $alternative_changes) {

                // Price test call
                if ($is_test) {
                    $instance->cart_item_sorting_prices_test[$cart_item_key] = $alternative_changes['price'];
                }
                // Real cart call
                else {
                    $instance->cart_item_sorting_prices[$cart_item_key] = $alternative_changes['price'];
                }
            }
        }
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get highest price from cart item price changes
     *
     * Either gets highest price for a single price range or for the whole changes set
     *
     * @access public
     * @param array $price_data
     * @param array $price_range
     * @return float
     */
    public static function get_highest_price_from_cart_item_price_changes($price_data, $price_range = array())
    {

        // Price range is specified
        if (!empty($price_range)) {

            $price_range_alternative = $price_range['highest_price'];
        }
        // Price range is not specified
        else {

            $quantity = 0;

            // Calculate total quantity
            foreach ($price_data['prices']['ranges'] as $price_range) {
                $quantity += RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
            }

            // Get highest prices subtotal from cart item price changes
            $subtotal = RightPress_Product_Price_Changes::get_highest_prices_subtotal_from_cart_item_price_changes($price_data);

            // Calculate average highest price for price range
            $price_range_alternative = ($subtotal / $quantity);
        }

        // Get potential highest prices
        $highest_price_alternatives = array(
            $price_range_alternative,
            $price_data['original_price'],
            $price_data['base_price'],
        );

        // Select highest price and return
        return max($highest_price_alternatives);
    }

    /**
     * Get highest prices subtotal from cart item price changes
     *
     * @access public
     * @param array $price_data
     * @return float
     */
    public static function get_highest_prices_subtotal_from_cart_item_price_changes($price_data)
    {

        $subtotal = 0.0;

        // Iterate over price ranges
        foreach ($price_data['prices']['ranges'] as $price_range) {

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Add price range highest price subtotal
            $subtotal += ($price_range['highest_price'] * $price_range_quantity);
        }

        return $subtotal;
    }








}

RightPress_Product_Price_Changes::get_instance();

}
