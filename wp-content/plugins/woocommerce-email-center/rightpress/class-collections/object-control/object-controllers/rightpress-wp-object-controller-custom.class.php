<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_WP_Object_Controller')) {
    require_once('rightpress-wp-object-controller.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WP_Object_Controller_Custom')) {

/**
 * WordPress Custom Object Controller
 *
 * @class RightPress_WP_Object_Controller_Custom
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Controller_Custom extends RightPress_WP_Object_Controller
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

        // Register meta table
        if ($this->supports_metadata()) {
            add_action('init', array($this, 'register_meta_table'), 0);
            add_action('switch_blog', array($this, 'register_meta_table'), 0);
        }
    }

    /**
     * Get meta table name
     *
     * @access public
     * @return string
     */
    public function get_meta_table_name()
    {
        return $this->get_object_key() . 'meta';
    }

    /**
     * Register meta table
     *
     * @access public
     * @return void
     */
    public function register_meta_table()
    {
        global $wpdb;

        // Get meta table name
        $table_name = $this->get_meta_table_name();

        // Register meta table
        $wpdb->$table_name  = $wpdb->prefix . $table_name;
        $wpdb->tables[]     = $table_name;
    }



}
}
