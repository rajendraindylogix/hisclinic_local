<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Settings')) {

/**
 * RightPress Settings Class
 *
 * @class RightPress_Settings
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Settings
{

    // TBD: PREPARE JS FRAMEWORK FOR TWO LEVEL PANELS (WHAT WE USE FOR WCDPD)
    // TBD: UPDATE VALIDATION JS TO VALIDATE PANELS
    // TBD: PREPARE THIS COMPONENT TO HANDLE SETTINGS ON OTHER PAGES, E.G. WC PRODUCT PAGES. ONLY OUR OWN CUSTOM POST TYPES DO NOT GO THROUGH THIS LIB.

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Maybe print settings templates
        add_action('admin_footer', array($this, 'maybe_print_settings_templates'));

    }

    /**
     * Maybe print settings templates in WordPress admin footer
     *
     * @access public
     * @return void
     */
    public function maybe_print_settings_templates()
    {
        // Print settings templates if they are used on current page
        if ($this->page_uses_settings_templates()) {
            $this->print_settings_templates();
        }
    }

    /**
     * Check if page uses settings templates
     *
     * Note: plugins overriding this method must also define method print_settings_templates()
     *
     * @access public
     * @return void
     */
    public function page_uses_settings_templates()
    {
        return false;
    }






}
}
