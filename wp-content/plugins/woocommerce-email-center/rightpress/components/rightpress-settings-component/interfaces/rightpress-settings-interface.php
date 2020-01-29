<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if interface has already been loaded
if (!interface_exists('RightPress_Plugin_Settings_Interface')) {

/**
 * Plugin Settings Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_Plugin_Settings_Interface
{

    /**
     * Define structure
     *
     * @access public
     * @return array
     */
    public function define_structure();

    /**
     * Migrate settings
     *
     * @access public
     * @param array $stored
     * @return array
     */
    public function migrate_settings($stored);

    /**
     * Get plugin private prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_private_prefix();

    /**
     * Get plugin path
     *
     * @access public
     * @return string
     */
    public function get_plugin_path();

    /**
     * Get settings capability
     *
     * @access public
     * @return string
     */
    public function get_capability();





}
}
