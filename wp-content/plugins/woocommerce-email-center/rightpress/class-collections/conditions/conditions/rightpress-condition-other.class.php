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
 * Condition Group: Other
 *
 * @class RightPress_Condition_Other
 * @package RightPress
 * @author RightPress
 */
if (!class_exists('RightPress_Condition_Other')) {

abstract class RightPress_Condition_Other extends RightPress_Condition
{

    protected $group_key        = 'other';
    protected $group_position   = 220;

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

        return __('Other', 'rightpress');
    }





}
}
