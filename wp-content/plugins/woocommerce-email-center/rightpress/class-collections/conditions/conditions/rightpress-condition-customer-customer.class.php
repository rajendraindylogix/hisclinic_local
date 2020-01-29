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
 * Condition: Customer - Customer
 *
 * @class RightPress_Condition_Customer_Customer
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Customer_Customer')) {

abstract class RightPress_Condition_Customer_Customer extends RightPress_Condition_Customer
{

    protected $key      = 'customer';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('users'),
    );
    protected $position = 10;

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

        return __('Customer', 'rightpress');
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

        return get_current_user_id();
    }





}
}
