<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Assets_Component')) {

/**
 * RightPress Assets Component
 *
 * @class RightPress_Assets_Component
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Assets_Component
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
        require_once __DIR__ . '/classes/rightpress-asset.class.php';
        require_once __DIR__ . '/classes/rightpress-assets.class.php';
    }





}

RightPress_Assets_Component::get_instance();

}
