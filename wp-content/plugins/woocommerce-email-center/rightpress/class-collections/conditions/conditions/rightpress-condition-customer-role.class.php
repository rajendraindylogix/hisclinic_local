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
 * Condition: Customer - Role
 *
 * @class RightPress_Condition_Customer_Role
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Customer_Role')) {

abstract class RightPress_Condition_Customer_Role extends RightPress_Condition_Customer
{

    protected $key      = 'role';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('roles'),
    );
    protected $position = 20;

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

        return __('User role', 'rightpress');
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

        return RightPress_Help::current_user_roles();
    }





}
}
