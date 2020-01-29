<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Object_Controller')) {

/**
 * Object Controller
 *
 * @class RightPress_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object_Controller
{

    // Data store reference
    protected $data_store = null;

    // Properties with default values
    protected $is_chronologic       = false;
    protected $is_editable          = false;
    protected $supports_comments    = false;
    protected $supports_metadata    = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Load data store
        $data_store_class = $this->get_data_store_class();
        $this->data_store = new $data_store_class;
    }

    /**
     * Prefix public hook
     *
     * @access public
     * @param string $hook
     * @return string
     */
    public function prefix_public_hook($hook)
    {
        return $this->get_plugin_public_prefix() . $this->get_object_name() . '_' . $hook;
    }

    /**
     * Prefix private hook
     *
     * @access public
     * @param string $hook
     * @return string
     */
    public function prefix_private_hook($hook)
    {
        return $this->get_object_key() . '_' . $hook;
    }

    /**
     * Get object key
     *
     * @access public
     * @param string $prefix
     * @return string
     */
    public function get_object_key($prefix = '')
    {
        return $prefix . $this->get_plugin_private_prefix() . $this->get_object_name();
    }

    /**
     * Check if object is chronologic
     *
     * @access public
     * @return bool
     */
    public function is_chronologic()
    {
        return $this->is_chronologic;
    }

    /**
     * Check if object is editable
     *
     * @access public
     * @return bool
     */
    public function is_editable()
    {
        return $this->is_editable;
    }

    /**
     * Check if object supports comments
     *
     * @access public
     * @return bool
     */
    public function supports_comments()
    {
        return $this->supports_comments;
    }

    /**
     * Check if object supports meta data
     *
     * @access public
     * @return bool
     */
    public function supports_metadata()
    {
        return $this->supports_metadata;
    }

    /**
     * Get object
     *
     * @access public
     * @param int|object $object
     * @return object|bool
     */
    public function get_object($object)
    {

        // Attempt to load object
        try {

            // Get object class
            $object_class = $this->get_object_class();

            // Load object
            return new $object_class($object, $this->data_store, $this);
        }
        // Unable to load object
        catch (Exception $e) {

            error_log($e->getMessage());
            return false;
        }
    }




}
}
