<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Field_Text')) {
    require_once('rightpress-condition-field-text.class.php');
}

/**
 * Condition Field: Text - Postcode
 *
 * @class RightPress_Condition_Field_Text_Postcode
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Field_Text_Postcode')) {

abstract class RightPress_Condition_Field_Text_Postcode extends RightPress_Condition_Field_Text
{

    protected $key = 'postcode';

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
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return __('e.g. 90210, 902**, 90200-90299, SW1A 1AA, NSW 2001', 'rightpress');
    }





}
}
