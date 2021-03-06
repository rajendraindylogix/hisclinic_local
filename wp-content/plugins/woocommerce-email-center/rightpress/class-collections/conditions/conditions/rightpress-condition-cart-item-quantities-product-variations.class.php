<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Cart_Item_Quantities')) {
    require_once('rightpress-condition-cart-item-quantities.class.php');
}

/**
 * Condition: Cart Item Quantities - Product Variations
 *
 * @class RightPress_Condition_Cart_Item_Quantities_Product_Variations
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Cart_Item_Quantities_Product_Variations')) {

abstract class RightPress_Condition_Cart_Item_Quantities_Product_Variations extends RightPress_Condition_Cart_Item_Quantities
{

    protected $key          = 'product_variations';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('product_variations'),
        'after'     => array('number'),
    );
    protected $main_field   = 'number';
    protected $position     = 20;

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

        return __('Cart item quantity - Variations', 'rightpress');
    }





}
}
