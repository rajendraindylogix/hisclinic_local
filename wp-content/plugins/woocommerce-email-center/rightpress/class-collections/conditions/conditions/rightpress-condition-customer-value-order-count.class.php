<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Customer_Value')) {
    require_once('rightpress-condition-customer-value.class.php');
}

/**
 * Condition: Customer Value - Order Count
 *
 * @class RightPress_Condition_Customer_Value_Order_Count
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Customer_Value_Order_Count')) {

abstract class RightPress_Condition_Customer_Value_Order_Count extends RightPress_Condition_Customer_Value
{

    protected $key          = 'order_count';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('timeframe_span'),
        'after'     => array('number'),
    );
    protected $main_field   = 'number';
    protected $position     = 40;

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

        return __('Order count', 'rightpress');
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

        // Get order ids
        if ($order_ids = $this->get_order_ids_by_timeframe($params['condition']['timeframe_span'])) {

            // Return order count
            return count($order_ids);
        }

        return 0;
    }





}
}
