<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_DateTime')) {

/**
 * DateTime Class
 *
 * @class RightPress_DateTime
 * @package RightPress
 * @author RightPress
 */
class RightPress_DateTime extends DateTime
{

    /**
     * Convert DateTime object to ISO 8601 string
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->format(DATE_ATOM);
    }







}
}
