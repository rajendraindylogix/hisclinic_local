<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_WP_Object_Controller_Post_Interface')) {

/**
 * WordPress Post Object Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Object_Controller_Post_Interface
{

    /**
     * Get main post type
     *
     * @access public
     * @return string
     */
    public function get_main_post_type();

    /**
     * Get menu priority
     *
     * @access public
     * @return string
     */
    public function get_menu_priority();







}
}
