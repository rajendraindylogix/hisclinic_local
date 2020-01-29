<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Field_Multiselect')) {
    require_once('rightpress-condition-field-multiselect.class.php');
}

/**
 * Condition Field: Multiselect - Shipping Classes
 *
 * @class RightPress_Condition_Field_Multiselect_Shipping_Classes
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Field_Multiselect_Shipping_Classes')) {

abstract class RightPress_Condition_Field_Multiselect_Shipping_Classes extends RightPress_Condition_Field_Multiselect
{

    protected $key = 'shipping_classes';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {

        return RightPress_Conditions::get_all_shipping_classes($ids, $query);
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {

        return __('Select shipping classes', 'rightpress');
    }





}
}
