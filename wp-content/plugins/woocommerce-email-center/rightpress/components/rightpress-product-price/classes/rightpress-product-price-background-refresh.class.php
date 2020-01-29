<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_Price_Background_Refresh')) {

/**
 * RightPress Shared Product Price Background Refresh
 *
 * @class RightPress_Product_Price_Background_Refresh
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Background_Refresh
{

    // TBD: price caching could be updated by background process as soon as at least one price is determined to be outdated (for that user prices are updated real time but for others it will potentially be faster)

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

RightPress_Product_Price_Background_Refresh::get_instance();

}
