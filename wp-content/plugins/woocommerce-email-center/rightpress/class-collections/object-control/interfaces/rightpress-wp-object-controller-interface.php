<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_WP_Object_Controller_Interface')) {

/**
 * WordPress Object Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Object_Controller_Interface
{

    /**
     * Get statuses
     *
     * Status arrays must contain elements label and label_count
     *
     * @access public
     * @return array
     */
    public function get_statuses();

    /**
     * Get default status
     *
     * @access public
     * @return string
     */
    public function get_default_status();






}
}
