<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Settings_Component')) {

/**
 * RightPress Settings Component
 *
 * @class RightPress_Settings_Component
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Settings_Component
{

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

        // Load classes
        require_once __DIR__ . '/classes/rightpress-plugin-settings.class.php';
        require_once __DIR__ . '/classes/rightpress-settings-exception.class.php';

        // Load interfaces
        require_once __DIR__ . '/interfaces/rightpress-settings-interface.php';
    }



}

RightPress_Settings_Component::get_instance();

}
