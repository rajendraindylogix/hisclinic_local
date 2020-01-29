<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Object_Controller')) {
    require_once('rightpress-object-controller.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WC_Product_Controller')) {

/**
 * WooCommerce Product Controller
 *
 * @class RightPress_WC_Product_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product_Controller extends RightPress_Object_Controller
{

    // TBD: maybe add some hidden input with product settings version number so that we reject submits of pages that were opened before update (we changed field names for existing fields)

    protected $is_editable          = true;
    protected $supports_metadata    = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Construct parent
        parent::__construct();

        // Print checkboxes
        add_filter('product_type_options', array($this, 'add_product_checkbox'));
        add_action('woocommerce_variation_options', array($this, 'print_variation_checkbox'), 10, 3);

        // Print settings
        add_action('woocommerce_product_options_general_product_data', array($this, 'print_product_settings'));
        add_action('woocommerce_product_after_variable_attributes', array($this, 'print_variation_settings'), 10, 3);

        // Process submitted settings
        add_action('woocommerce_admin_process_product_object', array($this, 'process_product_settings'));
        add_action('woocommerce_save_product_variation', array($this, 'process_product_variation_settings'), 10, 2);

        // Add product list shared column value
        RightPress_Loader::load_component('rightpress-product-list-shared-column');
        add_filter('rightpress_product_list_shared_column_values', array($this, 'add_product_list_shared_column_value'), 10, 2);

        // Load shared product pricing component
        RightPress_Loader::load_component('rightpress-product-price');
    }

    /**
     * Add product checkbox
     *
     * @access public
     * @param array $checkboxes
     * @return array
     */
    public function add_product_checkbox($checkboxes)
    {
        // Add checkbox
        $checkboxes[$this->get_object_key()] = array(
            'id'            => $this->get_object_key('_'),
            'wrapper_class' => 'show_if_simple',
            'label'         => $this->get_checkbox_label(),
            'description'   => $this->get_checkbox_description(),
            'default'       => 'no',
        );

        // Return checkboxes array
        return $checkboxes;
    }

    /**
     * Print variation checkbox
     *
     * WC31: Products will no longer be posts ($variation->ID)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function print_variation_checkbox($loop, $variation_data, $variation)
    {
        // Check if functionality is enabled for this variation
        $is_enabled = $this->is_enabled_for_product($variation->ID);

        // TBD: html validation errors are not visible when variation panel is collapsed

        // Format and print checkbox
        $input = '<input type="checkbox" class="checkbox ' . $this->get_object_key('_') . '_variable" name="' . $this->get_object_key('_') . '[' . $loop . ']" ' . checked($is_enabled, true, false) . ' />';
        // TBD: missing label "for" attribute???
        echo '<label class="tips" data-tip="' . $this->get_checkbox_description() . '"> ' . $this->get_checkbox_label() . ' ' . $input . '</label>';
    }

    /**
     * Process product settings
     *
     * Product object is passed by reference and object data is saved by
     * WooCommerce so we only need to set meta data
     *
     * @access public
     * @param object $product
     * @param bool $enabled
     * @param array $posted
     * @return void
     */
    public function process_product_settings($product, $enabled = null, $posted = null)
    {
        // Get posted data
        if (!isset($enabled) && !$product->is_type('variable')) {

            // Get checkbox name and settings key
            $checkbox_name      = $this->get_object_key('_');
            $settings_prefix    = $checkbox_name . '_settings';

            // Functionality was enabled
            if (!empty($_POST[$checkbox_name]) && !empty($_POST[$settings_prefix])) {

                $posted = $_POST[$settings_prefix];
                $enabled = true;
            }
            // Functionality was disabled
            else {

                $posted = null;
                $enabled = false;
            }
        }

        // Load object
        if ($object = $this->get_object($product)) {

            // Functionality is enabled
            if ($enabled) {

                // Merge product settings
                $settings = array_merge(array($this->get_object_name() => true), $posted);

                // Validate and sanitize settings
                $result = $this->sanitize_product_settings($settings, $object);

                // Set properties if validation was successful
                // TBD: How do we handle when validation is not successful? Should we still save those fields that were valid? Should populate field in the frontend with bad value to be fixed?
                if (!is_wp_error($result)) {
                    $result = $object->set_properties($result);
                }

                // Display potential errors
                if (is_wp_error($result)) {
                    foreach ($result->get_error_messages() as $message) {
                        WC_Admin_Meta_Boxes::add_error($message);
                    }
                }

                // Save updated configuration
                $object->save();
            }
            // Functionality was disabled
            else {

                // Data cleanup
                $object->delete();
            }
        }
    }

    /**
     * Process product variation settings
     *
     * @access public
     * @param int $variation_id
     * @param int $i
     * @return void
     */
    public function process_product_variation_settings($variation_id, $i)
    {
        // Load variation object
        if ($product_variation = wc_get_product($variation_id)) {

            // Get checkbox name and settings key
            $checkbox_name      = $this->get_object_key('_');
            $settings_prefix    = $checkbox_name . '_settings';

            // Functionality was enabled
            if (!empty($_POST[$checkbox_name][$i]) && !empty($_POST[$settings_prefix][$i])) {

                $posted = $_POST[$settings_prefix][$i];
                $enabled = true;
            }
            // Functionality was disabled
            else {

                $posted = null;
                $enabled = false;
            }

            // Process settings
            $this->process_product_settings($product_variation, $enabled, $posted);

            // Save variation object
            $product_variation->save();
        }

        // TBD: Need to mark variable product as having functionality enabled when at least one of its variations has it enabled
    }

    /**
     * Add product list shared column value
     *
     * @access public
     * @param array $values
     * @param int $post_id
     * @return array
     */
    public function add_product_list_shared_column_value($values, $post_id)
    {
        // Check if functionality is enabled
        if ($this->is_enabled_for_product($post_id)) {

            // Add column value
            $values[] = $this->get_product_list_shared_column_value($post_id);
        }

        // Return values
        return $values;
    }


/**
 * Insert custom column into product list view
 *
 * @access public
 * @param array $columns
 * @return array
 */
// TBD: REMOVE THIS
/*public function wc_hook_manage_product_posts_custom_column($column)
{
    global $post, $woocommerce, $the_product;

    if (empty($the_product) || RightPress_WC_Legacy::product_get_id($the_product) != $post->ID) {
        $the_product = get_product($post);
    }

    if ($column == 'subscriptio') {
        if (self::is_subscription($the_product)) {
            $tip = RightPress_WC_Legacy::product_get_type($the_product) == 'simple' ? __('This product is a subscription', 'subscriptio') : __('Contains at least one subscription', 'subscriptio');
            echo '<i class="fa fa-repeat subscriptio_product_list_icon tips" data-tip="' . $tip . '"></i>';
        }
    }
}*/










/**

TBD: WILL NEED TO MIGRATE THESE PRODUCT META ENTRIES TO NEW FORMAT FOR SUBSCRIPTIO
_subscriptio <-- this is also set to parent products when at least one variation is a subscription
_subscriptio_price_time_value
_subscriptio_price_time_unit
_subscriptio_free_trial_time_value
_subscriptio_free_trial_time_unit
_subscriptio_max_length_time_value
_subscriptio_max_length_time_unit
_subscriptio_signup_fee

*/












}
}
