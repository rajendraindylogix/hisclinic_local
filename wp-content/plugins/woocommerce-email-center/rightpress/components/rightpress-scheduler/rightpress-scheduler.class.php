<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Scheduler')) {

/**
 * RightPress Scheduler Component
 *
 * @class RightPress_Scheduler
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Scheduler
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

RightPress_Scheduler::get_instance();

}
