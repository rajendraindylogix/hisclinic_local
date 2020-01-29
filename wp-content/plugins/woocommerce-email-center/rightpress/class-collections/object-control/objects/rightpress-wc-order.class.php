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
if (!class_exists('RightPress_WC_Order')) {

/**
 * WooCommerce Order "Wrapper" Class
 *
 * Used to build our own object (e.g. subscription order) around WooCommerce order object
 *
 * @class RightPress_WC_Order
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Order extends RightPress_Object
{

    protected $wc_order = null;

    // Common properties
    protected $common_data = array(
        'updated'           => null,
        'plugin_version'    => null,
    );

    // Common datetime properties
    protected $common_datetime_properties = array(
        'updated',
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
        // Object of class WC_Order was provided
        if (is_a($object, 'WC_Order')) {

            $this->set_wc_order($object);
            $object = $object->get_id();
        }
        // Object of class RightPress_WC_Order was provided
        else if (is_a($object, 'RightPress_WC_Order')) {

            $this->set_wc_order(wc_get_order($object->get_id()));
        }
        // Numeric order identifier was provided
        else if (is_numeric($object) && $object > 0) {

            $this->set_wc_order(wc_get_order($object));
        }
        // Identifier is undefined
        else {

            throw new Exception('RightPress: Identifier for object of class RightPress_WC_Order must be either numeric identifier or object of class WC_Order or RightPress_WC_Order.');
        }

        // Check if order is defined
        if (!is_a($this->get_wc_order(), 'WC_Order')) {
            throw new Exception('RightPress: WooCommerce order is undefined in RightPress_WC_Order constructor.');
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
     * Get WooCommerce order
     *
     * @access public
     * @return object
     */
    public function get_wc_order()
    {
        return $this->wc_order;
    }

    /**
     * Get id
     *
     * @access public
     * @return int
     */
    public function get_id()
    {
        return $this->get_wc_order()->get_id();
    }

    /**
     * =================================================================================================================
     * SETTERS
     * =================================================================================================================
     */

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
     * Set WooCommerce order
     *
     * @access public
     * @param object $value
     * @return void
     */
    public function set_wc_order($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_wc_order($value);

        // Set property
        $this->wc_order = $value;
    }

    /**
     * =================================================================================================================
     * SANITIZERS - VALIDATORS
     * =================================================================================================================
     */

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
        // TBD: do any validation
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
        // TBD: do any validation
        // throw new RightPress_Object_Exception();

        // Cast value to string
        $value = (string) $value;

        // Return sanitized value
        return $value;
    }

    /**
     * Sanitize and validate WooCommerce order
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param object $value
     * @return object
     */
    public function sanitize_wc_order($value)
    {
        // Validate value
        // TBD: do any validation
        // throw new RightPress_Object_Exception();

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
