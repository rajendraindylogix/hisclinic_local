<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Object_Controller')) {
    require_once('rightpress-object-controller.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WC_Order_Controller')) {

/**
 * WooCommerce Order Controller
 *
 * @class RightPress_WC_Order_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Order_Controller extends RightPress_Object_Controller
{

    protected $supports_metadata = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Construct parent
        parent::__construct();

    }












}
}
