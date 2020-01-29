<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_WP_Object_Data_Store_Interface')) {

/**
 * WordPress Object Data Store Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Object_Data_Store_Interface
{

    /**
     * Get meta type
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_meta_type(&$object);

    /**
     * Get object id field name
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_object_id_field_name(&$object);




}
}
