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
if (!class_exists('RightPress_WC_Order_Data_Store')) {

/**
 * WooCommerce Order Data Store
 *
 * @class RightPress_WC_Order_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Order_Data_Store extends RightPress_Object_Data_Store
{

    /**
     * Create object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array())
    {
        // TBD
    }

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
        // TBD
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
        // TBD
    }

    /**
     * Delete object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array())
    {
        // TBD
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
     * Get order meta prefix
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
