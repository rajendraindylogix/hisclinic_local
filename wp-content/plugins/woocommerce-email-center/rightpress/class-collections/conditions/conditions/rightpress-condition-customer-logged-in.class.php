<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Customer')) {
    require_once('rightpress-condition-customer.class.php');
}

/**
 * Condition: Customer - Logged In
 *
 * @class RightPress_Condition_Customer_Logged_In
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Customer_Logged_In')) {

abstract class RightPress_Condition_Customer_Logged_In extends RightPress_Condition_Customer
{

    protected $key      = 'logged_in';
    protected $method   = 'boolean';
    protected $position = 50;

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
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return __('Is logged in', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        return is_user_logged_in();
    }





}
}
