<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Condition_Time')) {
    require_once('rightpress-condition-time.class.php');
}

/**
 * Condition: Time - Datetime
 *
 * @class RightPress_Condition_Time_Datetime
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Time_Datetime')) {

abstract class RightPress_Condition_Time_Datetime extends RightPress_Condition_Time
{

    protected $key      = 'datetime';
    protected $method   = 'datetime';
    protected $fields   = array(
        'after' => array('datetime'),
    );
    protected $position = 30;

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

        return __('Date & time', 'rightpress');
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

        return RightPress_Help::get_datetime_object();
    }





}
}
