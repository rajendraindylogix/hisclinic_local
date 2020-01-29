<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Field_Number')) {
    require_once('rightpress-condition-field-number.class.php');
}

/**
 * Condition Field: Number - Number
 *
 * @class RightPress_Condition_Field_Number_Number
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Field_Number_Number')) {

abstract class RightPress_Condition_Field_Number_Number extends RightPress_Condition_Field_Number
{

    protected $key = 'number';

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





}
}
