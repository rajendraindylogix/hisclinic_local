<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Field_Select_Timeframe')) {
    require_once('rightpress-condition-field-select-timeframe.class.php');
}

/**
 * Condition Field: Select - Timeframe Span
 *
 * @class RightPress_Condition_Field_Select_Timeframe_Span
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Field_Select_Timeframe_Span')) {

abstract class RightPress_Condition_Field_Select_Timeframe_Span extends RightPress_Condition_Field_Select_Timeframe
{

    protected $key = 'timeframe_span';

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
