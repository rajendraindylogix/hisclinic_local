<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition methods controller
 *
 * @class RightPress_Controller_Condition_Methods
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Controller_Condition_Methods')) {

abstract class RightPress_Controller_Condition_Methods extends RightPress_Item_Controller
{

    protected $item_key = 'condition_method';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }





}
}
