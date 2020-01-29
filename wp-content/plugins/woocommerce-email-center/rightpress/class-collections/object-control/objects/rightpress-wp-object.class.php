<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Object')) {
    require_once('rightpress-object.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WP_Object')) {

/**
 * WordPress Object Class
 *
 * @class RightPress_WP_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object extends RightPress_Object
{

    // Common properties
    protected $common_data = array(
        'status'            => null,
        'created'           => null,
        'updated'           => null,
        'plugin_version'    => null,
    );

    // Common datetime properties
    protected $common_datetime_properties = array(
        'created', 'updated',
    );

    /**
     * Constructor
     *
     * @access public
     * @param mixed $object
     * @param object $data_store
     * @param object $controller
     * @return void
     */
    public function __construct($object, $data_store, $controller)
    {
        // Get object class
        $object_class = $controller->get_object_class();

        // Identifier not set or not valid
        if (!is_a($object, $object_class) && (!is_numeric($object) || $object <= 0)) {
            throw new Exception('RightPress: Identifier for object of class ' . $object_class . ' is not valid.');
        }

        // Construct parent
        parent::__construct($object, $data_store, $controller);
    }

    /**
     * =================================================================================================================
     * GETTERS
     * =================================================================================================================
     */

    /**
     * Get status
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_status($context = 'view', $args = array())
    {
        // Get status
        $status = $this->get_property('status', $context, $args);

        // Status must never be empty, set default status
        if ($status === null) {

            // Set default status
            $default_status = $this->get_controller()->get_default_status();
            $this->set_status($default_status);

            // Get status again
            $status = $this->get_property('status', $context, $args);
        }

        // Prefix status for storage
        if ($status && $context === 'store') {
            $status = $this->get_controller()->prefix_status($status);
        }

        // Return status
        return $status;
    }

    /**
     * Get created datetime
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_created($context = 'view', $args = array())
    {
        return $this->get_property('created', $context, $args);
    }

    /**
     * Get updated datetime
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_updated($context = 'view', $args = array())
    {
        return $this->get_property('updated', $context, $args);
    }

    /**
     * Get plugin version
     *
     * @access public
     * @param string $context
     * @param array $args
     * @return string
     */
    public function get_plugin_version($context = 'view', $args = array())
    {
        return $this->get_property('plugin_version', $context, $args);
    }

    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */

    /**
     * Set status
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_status($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_status($value);

        // Set property
        $this->set_property('status', $value);
    }

    /**
     * Set created datetime
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_created($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_created($value);

        // Set property
        $this->set_property('created', $value);
    }

    /**
     * Set updated datetime
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_updated($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_updated($value);

        // Set property
        $this->set_property('updated', $value);
    }

    /**
     * Set plugin version
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function set_plugin_version($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_plugin_version($value);

        // Set property
        $this->set_property('plugin_version', $value);
    }

    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */

    /**
     * Sanitize and validate status
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_status($value)
    {
        // Unprefix status string
        $value = $this->get_controller()->unprefix_status((string) $value);

        // Validate value
        if (!in_array($value, array_merge(array_keys($this->get_controller()->get_statuses()), array('trash', 'auto-draft')), true)) {

            // TBD: is this a good handling? Should we throw exception instead?
            RightPress_Help::doing_it_wrong((get_class($this) . '::' . __FUNCTION__), 'Status "' . $value . '" is not valid for object type.', '1.0');
            exit;
        }

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate created datetime
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_created($value)
    {
        // Validate value
        // TBD: do validation
        // throw new RightPress_Object_Exception();

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate updated datetime
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_updated($value)
    {
        // Validate value
        // TBD: do validation
        // throw new RightPress_Object_Exception();

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate plugin version
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param string $value
     * @return string
     */
    public function sanitize_plugin_version($value)
    {
        // Validate value
        // TBD: do validation
        // throw new RightPress_Object_Exception();

        // Cast value to string
        $value = (string) $value;

        // Return sanitized value
        return $value;
    }

    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */
















}
}
