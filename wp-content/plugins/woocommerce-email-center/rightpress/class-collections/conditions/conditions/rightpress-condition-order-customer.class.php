<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition')) {
    require_once('rightpress-condition.class.php');
}

/**
 * Condition Group: Order Customer
 *
 * @class RightPress_Condition_Order_Customer
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Order_Customer')) {

abstract class RightPress_Condition_Order_Customer extends RightPress_Condition
{

    protected $group_key        = 'order_customer';
    protected $group_position   = 135;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {

        return __('Customer', 'rightpress');
    }





}
}
