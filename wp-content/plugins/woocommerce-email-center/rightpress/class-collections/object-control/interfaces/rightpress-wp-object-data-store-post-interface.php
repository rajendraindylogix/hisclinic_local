<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_WP_Object_Data_Store_Post_Interface')) {

/**
 * WordPress Post Object Data Store Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Object_Data_Store_Post_Interface
{

    /**
     * Get post type
     *
     * @access public
     * @return string
     */
    public function get_post_type();






}
}
