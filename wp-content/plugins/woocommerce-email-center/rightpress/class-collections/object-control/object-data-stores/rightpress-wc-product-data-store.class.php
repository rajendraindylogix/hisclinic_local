<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Object_Data_Store')) {
    require_once('rightpress-object-data-store.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WC_Product_Data_Store')) {

/**
 * WooCommerce Product Data Store
 *
 * @class RightPress_WC_Product_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product_Data_Store extends RightPress_Object_Data_Store
{

    /**
     * Create object data in the database
     *
     * Note: This method does nothing since WooCommerce product is always present
     * when saving data, i.e. object id is always set when saving object
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array()) {}

    /**
     * Read object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function read(&$object, $args = array())
    {
        // Reference WooCommerce product object
        $wc_product = $object->get_wc_product();

        // Set properties
        foreach ($object->get_data_keys() as $key) {

            // Prefix key
            $prefixed_key = $this->prefix_key($key, $object);

            // Check if meta exists
            if (RightPress_WC::product_meta_exists($wc_product, $prefixed_key)) {

                // Get meta value
                $value = RightPress_WC::product_get_meta($wc_product, $prefixed_key, true, 'edit');

                // Set property
                $method = 'set_' . $key;
                $object->{$method}($value);
            }
        }

        // Read meta data
        $object->read_meta_data();

        // Object data is ready
        $object->set_data_ready(true);
    }

    /**
     * Update object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function update(&$object, $args = array())
    {
        // TBD: maybe we should store and update plugin_version property here as well?

        // Reference WooCommerce product object
        $wc_product = $object->get_wc_product();

        // Reset plugin version
        $object->reset_plugin_version();

        // Get changes
        $changes = $object->get_changes();

        // Get data for database
        if ($data = $this->get_data_for_database($object, $changes)) {

// TBD: what do we do with null values ?

            // Iterate over data entries
            foreach ($data as $key => $value) {

                // Prefix key
                $prefixed_key = $this->prefix_key($key, $object);

                // Update value in WooCommerce product meta
                RightPress_WC::product_update_meta_data($wc_product, $prefixed_key, $value);
            }

            // Apply changes
            $object->apply_changes();
        }

        // Save meta data
        // TBD
        $object->save_meta_data();
    }

    /**
     * Delete object data from the database
     *
     * Note: this does not actually delete any object since object is based
     * on WooCommerce product; we only clear all our data from product meta
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array())
    {
        // Reference WooCommerce product object
        $wc_product = $object->get_wc_product();

        // Iterate over all data keys
        foreach ($object->get_data_keys() as $key) {

            // Prefix key
            $prefixed_key = $this->prefix_key($key, $object);

            // Delete from WooCommerce product meta
            RightPress_WC::product_delete_meta_data($wc_product, $prefixed_key);
        }
    }

    /**
     * Add object meta data to the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function add_meta(&$object, $meta, $args = array())
    {
        // TBD
    }

    /**
     * Read object meta data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return mixed
     */
    public function read_meta(&$object, $args = array())
    {
        // TBD
    }

    /**
     * Update object meta data in the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function update_meta(&$object, $meta, $args = array())
    {
        // TBD
    }

    /**
     * Delete object meta data from the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function delete_meta(&$object, $meta, $args = array())
    {
        // TBD
    }

    /**
     * Get product meta prefix
     *
     * @access public
     * @param string $key
     * @param object $object
     * @return string
     */
    public function prefix_key($key, &$object)
    {
        return '_' . $object->get_controller()->get_plugin_private_prefix() . $key;
    }





}
}
