<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Data_Export')) {

/**
 * RightPress Data Export Component
 *
 * @class RightPress_Data_Export
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Data_Export
{

    // TBD

    // Singleton instance
    protected static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

}

RightPress_Data_Export::get_instance();

}
