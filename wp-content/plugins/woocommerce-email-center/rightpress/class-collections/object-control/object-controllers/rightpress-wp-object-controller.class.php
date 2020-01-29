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
if (!class_exists('RightPress_WP_Object_Controller')) {

/**
 * WordPress Object Controller
 *
 * @class RightPress_WP_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Controller extends RightPress_Object_Controller
{

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
    }

    /**
     * Prefix status
     *
     * @access public
     * @param string $status
     * @return string
     */
    public function prefix_status($status)
    {
        return $this->get_status_prefix() . $status;
    }

    /**
     * Remove prefix from status
     *
     * @access public
     * @param string $status
     * @return string
     */
    public function unprefix_status($status)
    {
        $prefix = $this->get_status_prefix();

        if (substr($status, 0, strlen($prefix)) === $prefix) {
            $status = substr($status, strlen($prefix));
        }

        return $status;
    }

    /**
     * Get status prefix
     *
     * @access public
     * @return string
     */
    public function get_status_prefix()
    {
        return str_replace('_', '-', $this->get_plugin_private_prefix());
    }



}
}
