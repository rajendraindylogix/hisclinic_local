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
if (!class_exists('RightPress_WC_Product')) {

/**
 * WooCommerce Product "Wrapper" Class
 *
 * Used to build our own object (e.g. subscription product, booking product)
 * around WooCommerce product object
 *
 * @class RightPress_WC_Product
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product extends RightPress_Object
{

    protected $wc_product = null;

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
        // Object of class WC_Product was provided
        if (is_a($object, 'WC_Product')) {

            $this->set_wc_product($object);
            $object = $object->get_id();
        }
        // Object of class RightPress_WC_Product was provided
        else if (is_a($object, 'RightPress_WC_Product')) {

            $this->set_wc_product(wc_get_product($object->get_id()));
        }
        // Numeric product identifier was provided
        else if (is_numeric($object) && $object > 0) {

            $this->set_wc_product(wc_get_product($object));
        }
        // Identifier is undefined
        else {

            throw new Exception('RightPress: Identifier for object of class RightPress_WC_Product must be either numeric identifier or object of class WC_Product or RightPress_WC_Product.');
        }

        // Check if product is defined
        if (!is_a($this->get_wc_product(), 'WC_Product')) {
            throw new Exception('RightPress: WooCommerce product is undefined in RightPress_WC_Product constructor.');
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
     * Get WooCommerce product
     *
     * @access public
     * @return object
     */
    public function get_wc_product()
    {
        return $this->wc_product;
    }

    /**
     * Get id
     *
     * @access public
     * @return int
     */
    public function get_id()
    {
        return $this->get_wc_product()->get_id();
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
     * Set WooCommerce product
     *
     * @access public
     * @param object $value
     * @return void
     */
    public function set_wc_product($value)
    {
        // Sanitize and validate value
        $value = $this->sanitize_wc_product($value);

        // Set property
        $this->wc_product = $value;
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
     * Sanitize and validate WooCommerce product
     * Returns sanitized value, throws RightPress_Object_Exception if invalid
     *
     * @access public
     * @param object $value
     * @return object
     */
    public function sanitize_wc_product($value)
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
