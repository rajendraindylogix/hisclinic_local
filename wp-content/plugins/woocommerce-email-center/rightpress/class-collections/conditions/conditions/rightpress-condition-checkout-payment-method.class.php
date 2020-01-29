<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Checkout')) {
    require_once('rightpress-condition-checkout.class.php');
}

/**
 * Condition: Checkout - Payment Method
 *
 * @class RightPress_Condition_Checkout_Payment_Method
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Checkout_Payment_Method')) {

abstract class RightPress_Condition_Checkout_Payment_Method extends RightPress_Condition_Checkout
{

    protected $key      = 'payment_method';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('payment_methods'),
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

        return __('Payment method', 'rightpress');
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

        // Get chosen payment method
        $payment_method = WC()->session->get('chosen_payment_method');

        // Check if payment gateway was chosen
        return (is_string($payment_method) && !empty($payment_method)) ? $payment_method : null;
    }





}
}
