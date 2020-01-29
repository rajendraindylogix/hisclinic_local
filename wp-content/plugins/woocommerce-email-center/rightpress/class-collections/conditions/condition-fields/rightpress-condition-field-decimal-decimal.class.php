<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Field_Decimal')) {
    require_once('rightpress-condition-field-decimal.class.php');
}

/**
 * Condition Field: Decimal - Decimal
 *
 * @class RightPress_Condition_Field_Decimal_Decimal
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Field_Decimal_Decimal')) {

abstract class RightPress_Condition_Field_Decimal_Decimal extends RightPress_Condition_Field_Decimal
{

    protected $key = 'decimal';

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
