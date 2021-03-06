<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Method')) {
    require_once('rightpress-condition-method.class.php');
}

/**
 * Condition Method: Boolean
 *
 * @class RightPress_Condition_Method_Boolean
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Method_Boolean')) {

abstract class RightPress_Condition_Method_Boolean extends RightPress_Condition_Method
{

    protected $key = 'boolean';

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
     * Get method options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        return array(
            'yes'   => __('yes', 'rightpress'),
            'no'    => __('no', 'rightpress'),
        );
    }

    /**
     * Check against condition method
     *
     * @access public
     * @param string $option_key
     * @param mixed $value
     * @param mixed $condition_value
     * @return bool
     */
    public function check($option_key, $value, $condition_value)
    {

        // Yes
        if ($option_key === 'yes' && $value) {
            return true;
        }
        // No
        else if ($option_key === 'no' && !$value) {
            return true;
        }

        return false;
    }





}
}
