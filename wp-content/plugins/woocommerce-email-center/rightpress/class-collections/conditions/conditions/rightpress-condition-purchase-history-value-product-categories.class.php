<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Purchase_History_Value')) {
    require_once('rightpress-condition-purchase-history-value.class.php');
}

/**
 * Condition: Purchase History Value - Product Categories
 *
 * @class RightPress_Condition_Purchase_History_Value_Product_Categories
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Purchase_History_Value_Product_Categories')) {

abstract class RightPress_Condition_Purchase_History_Value_Product_Categories extends RightPress_Condition_Purchase_History_Value
{

    protected $key          = 'product_categories';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('product_categories'),
        'after'     => array('decimal'),
    );
    protected $main_field   = 'decimal';
    protected $position     = 30;

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

        return __('Value purchased - Categories', 'rightpress');
    }





}
}
