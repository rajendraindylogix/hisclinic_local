<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Object')) {

/**
 * Generic Object Class
 *
 * @class RightPress_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object
{

    protected $id           = 0;
    protected $data_ready   = false;
    protected $default_data = array();
    protected $changes      = array();
    protected $meta_data    = null;
    protected $controller   = null;
    protected $data_store   = null;

    protected $setting_datetime_property = false;
    protected $getting_datetime_property = false;

    // Properties with default values
    protected $data                 = array();
    protected $datetime_properties  = array();

    // Common properties
    protected $common_data                  = array();
    protected $common_datetime_properties   = array();

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

        // Merge common data
        $this->data                 = array_merge($this->common_data, $this->data);
        $this->datetime_properties  = array_merge($this->common_datetime_properties, $this->datetime_properties);

        // Set default data
        $this->default_data = $this->data;

        // Set data store
        $this->set_data_store($data_store);

        // Set controller
        $this->set_controller($controller);

        // Set id
        if (is_numeric($object) && $object > 0) {
            $this->set_id($object);
        }
        else if (is_a($object, get_class($this))) {
            $this->set_id($object->get_id());
        }
        else {
            $this->set_data_ready(true);
        }

        // Read data
        if ($this->get_id() > 0) {
            $this->data_store->read($this);
        }
    }

    /**
     * Set data store
     *
     * @access protected
     * @param object $data_store
     * @return void
     */
    protected function set_data_store($data_store)
    {
        $this->data_store = $data_store;
    }

    /**
     * Get data store
     *
     * @access public
     * @return object
     */
    public function get_data_store()
    {
        return $this->data_store;
    }

    /**
     * Set controller
     *
     * @access protected
     * @param object $controller
     * @return void
     */
    public function set_controller($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @access public
     * @return object
     */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
     * Set id
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function set_id($id)
    {
        $this->id = absint($id);
    }

    /**
     * Get id
     *
     * @access public
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set data ready
     *
     * @access public
     * @param bool $ready
     * @return void
     */
    public function set_data_ready($ready)
    {
        $this->data_ready = (bool) $ready;
    }

    /**
     * Check if data is ready
     *
     * @access public
     * @return bool
     */
    public function is_data_ready()
    {
        return $this->data_ready;
    }

    /**
     * Get default data
     *
     * @access public
     * @return array
     */
    public function get_default_data()
    {
        return $this->default_data;
    }

    /**
     * Get all data
     *
     * @access public
     * @return array
     */
    public function get_data()
    {
        // Merge properties and return
        return array_merge(array('id' => $this->get_id()), $this->data, array('meta_data' => $this->get_meta_data()));
    }

    /**
     * Get data keys
     *
     * @access public
     * @return array
     */
    public function get_data_keys()
    {
        return array_keys($this->data);
    }

    /**
     * Reset all data
     *
     * @access public
     * @return array
     */
    public function reset_data()
    {
        $this->data = $this->get_default_data();
        $this->changes = array();
        $this->set_data_ready(false);
    }

    /**
     * Get changes
     *
     * @access public
     * @return array
     */
    public function get_changes()
    {
        return $this->changes;
    }

    /**
     * Apply changes
     *
     * @access public
     * @return void
     */
    public function apply_changes()
    {
        // Merge data
        $this->data = array_replace_recursive($this->data, $this->changes);

        // Reset changes array
        $this->changes = array();
    }

    /**
     * Get property
     *
     * Accepted $context values:
     * - view    get value with filters applied
     * - edit    get value without filters applied
     * - store   get value prepared to be stored in database
     *
     * @access public
     * @param string $key
     * @param string $context
     * @param array $args
     * @return mixed
     */
    public function get_property($key, $context = 'view', $args = array())
    {
        // Datetime property handling
        if ($this->is_property_datetime($key) && !$this->getting_datetime_property) {
            return $this->get_datetime_property($key, $context, $args);
        }

        $value = null;

        // Check if requested key exists
        if (array_key_exists($key, $this->data)) {

            // Get value from changes if set, otherwise get from main data array
            $value = array_key_exists($key, $this->changes) ? $this->changes[$key] : $this->data[$key];

            // Allow developers to override
            if ($context === 'view') {
                $value = apply_filters($this->get_controller()->prefix_public_hook('get_property_' . $key), $value, $this);
            }
        }

        return $value;
    }

    /**
     * Set property
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set_property($key, $value)
    {
        // Datetime property handling
        if ($this->is_property_datetime($key) && !$this->setting_datetime_property) {
            $this->set_datetime_property($key, $value);
            return;
        }

        // Set property
        if (array_key_exists($key, $this->data)) {
            if ($this->is_data_ready()) {
                if ($value !== $this->data[$key] || array_key_exists($key, $this->changes)) {
                    $this->changes[$key] = $value;
                }
            }
            else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Get datetime property
     *
     * @access public
     * @param string $key
     * @param string $context
     * @param array $args
     * @return object|string
     */
    public function get_datetime_property($key, $context = 'view', $args = array())
    {
        // Import arguments
        extract(RightPress_Help::filter_by_keys_with_defaults($args, array('is_gmt' => true)));

        // Get value
        $this->getting_datetime_property = true;
        $value = $this->get_property($key, $context, $args);
        $this->getting_datetime_property = false;

        // Maybe prepare value to be stored in database
        if ($context === 'store') {
            $value = $is_gmt ? gmdate('Y-m-d H:i:s', $value->getTimestamp()) : $value->format('Y-m-d H:i:s');
        }

        // Return value
        return $value;
    }

    /**
     * Set datetime property
     *
     * Accepts as value:
     * - RightPress_DateTime object
     * - Unix timestamp
     * - ISO 8601 string which must define timezone offset, e.g. '2018-06-17T23:17:28+01:00'
     * - MySQL datetime which must be in local timezone, e.g. '2018-06-17 23:17:28'
     *
     * @access public
     * @param string $key
     * @param string|int|object $value
     * @return void
     */
    public function set_datetime_property($key, $value)
    {

        try {

            // Get datetime object
            if (!empty($value)) {

                // Value is RightPress_DateTime
                if (is_a($value, 'RightPress_DateTime')) {
                    $datetime = $value;
                }
                // Value is timestamp
                else if (is_numeric($value)) {
                    $datetime = new RightPress_DateTime('@' . $value);
                }
                // Value is ISO 8601 string
                else if (RightPress_Help::is_date($value, DATE_ATOM)) {
                    $datetime = new RightPress_DateTime($value);
                }
                // Value is MySQL datetime (must be in local time zone)
                else if (RightPress_Help::is_date($value, 'Y-m-d H:i:s')) {
                    $datetime = new RightPress_DateTime($value, RightPress_Help::get_time_zone());
                }
                // Value is not supported
                else {
                    // TBD: maybe we should throw error and exit from here? Maybe better to fix this before writing any further wrong data to database?
                    RightPress_Help::doing_it_wrong((get_class($this) . '::' . __FUNCTION__), 'Datetime property value "' . strval($value) . '" is not of supported format.', '1.0');
                    return;
                }

                // Ensure correct time zone
                $datetime->setTimezone(RightPress_Help::get_time_zone());
            }
            else {
                $datetime = null;
            }

            // Set property
            $this->setting_datetime_property = true;
            $this->set_property($key, $datetime);
            $this->setting_datetime_property = false;
        }
        catch (Exception $e) {
            // TBD: handle error somehow (may be our thrown error or something from DateTime handling)
        }
    }

    /**
     * Set multiple properties
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @return bool|array
     */
    public function set_properties($properties)
    {
        // Prepare to store errors
        $errors = new WP_Error();

        // Iterate over properties
        foreach ($properties as $key => $value) {

            try {

                // Value must be set
                // TBD: not sure if this is a good approach? had it here to avoid "setting nothing" but then this prevented from clearing values after user unset some checkbox or so
                // if ($value === null) {
                //     continue;
                // }

                // Unknown property, write to log and continue with other properties
                if (!array_key_exists($key, $this->data)) {
                    RightPress_Help::doing_it_wrong((get_class($this) . '::' . __FUNCTION__), 'Property "' . strval($key) . '" is not defined for objects of class ' . get_class($this) . '.', '1.0');
                    continue;
                }

                // Set property
                $setter = 'set_' . $key;
                $this->{$setter}($value);
            }
            catch (RightPress_Object_Exception $e) {
                $errors->add($e->get_error_code(), $e->getMessage());
            }
        }

        // Return true if all properties were set successfully or list of errors if some were not
        return count($errors->get_error_codes()) ? $errors : true;
    }

    /**
     * Check if property is of datetime type
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function is_property_datetime($key)
    {
        return in_array($key, $this->datetime_properties, true);
    }

    /**
     * Save object
     *
     * @access public
     * @return int
     */
    public function save()
    {
        // Allow developers to modify object
        do_action($this->get_controller()->prefix_public_hook('before_save'), $this);

        // Existing object
        if ($this->get_id() > 0) {
            $this->data_store->update($this);
        }
        // New object
        else {
            $this->data_store->create($this);
        }

        // Return object id
        return $this->get_id();
    }

    /**
     * Delete object
     *
     * @access public
     * @param bool $permanently
     * @return void
     */
    public function delete($permanently = false)
    {
        // Let developers know
        do_action($this->get_controller()->prefix_public_hook('before_delete'), $this);

        // Delete entry
        $this->data_store->delete($this, array('permanently' => $permanently));

        // Reset id
        $this->set_id(0);
    }

    /**
     * Get all meta data
     *
     * @access public
     * @return array
     */
    public function get_meta_data()
    {
        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Return all meta data that have value set
        return array_filter($this->meta_data, function($meta) {
            return $meta->value !== null;
        });
    }

    /**
     * Save all meta data
     *
     * @access public
     * @return void
     */
    public function save_meta_data()
    {
        // Meta not supported
        if (!$this->get_controller()->supports_metadata()) {
            return;
        }

        // Meta not loaded
        if ($this->meta_data === null) {
            return;
        }

        // Iterate over meta
        foreach ($this->meta_data as $index => $meta) {

            // Delete meta entry
            if ($meta->value === null) {

                // Check if meta entry was previously stored
                if ($meta->id !== null) {
                    $this->data_store->delete_meta($this, $meta);
                    unset($this->meta_data[$index]);
                }
            }
            // Add new meta entry
            else if ($meta->id === null) {
                $meta->id = $this->data_store->add_meta($this, $meta);
                $meta->apply_changes();
            }
            // Update existing meta entry
            else if ($meta->get_changes()) {
                $this->data_store->update_meta($this, $meta);
                $meta->apply_changes();
            }
        }
    }

    /**
     * Maybe read meta data
     *
     * @access protected
     * @return void
     */
    protected function maybe_read_meta_data()
    {
        if ($this->meta_data === null) {
            $this->read_meta_data();
        }
    }

    /**
     * Read meta data
     *
     * @access public
     * @return void
     */
    public function read_meta_data()
    {
        $this->meta_data = array();

        // Meta not supported
        if (!$this->get_controller()->supports_metadata()) {
            return;
        }

        // Object is new
        if (!$this->get_id()) {
            return;
        }

        // Read meta data
        $this->meta_data = $this->data_store->read_meta($this);
    }

    /**
     * Add meta
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @return bool $unique
     * @return void
     */
    public function add_meta($key, $value, $unique = false)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Handle unique meta
        if ($unique) {
            $this->delete_meta($key);
        }

        // Add meta
        $this->meta_data[] = new RightPress_Meta(array(
            'key'   => $key,
            'value' => $value,
        ));
    }

    /**
     * Update meta
     *
     * @access public
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public function update_meta($key, $value, $meta_id = null)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Get meta index if meta id was provided
        $index = ($meta_id !== null) ? current(array_keys(wp_list_pluck($this->meta_data, 'id'), $meta_id)) : null;

        // Update single meta entry by index
        if (is_numeric($index)) {
            $meta = $this->meta_data[$index];
            $meta->key = $key;
            $meta->value = $value;
        }
        // Update meta by replacing any existing entries with new one
        else {
            $this->add_meta($key, $value, true);
        }
    }

    /**
     * Get meta
     *
     * @access public
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public function get_meta($key, $single = true, $context = 'view')
    {
        // Default value
        $value = $single ? '' : array();

        // Check metadata support
        if (!$this->check_metadata_support()) {
            return $value;
        }

        // Get all meta data
        $meta_data = $this->get_meta_data();

        // Get meta indexes by key
        $indexes = array_keys(wp_list_pluck($meta_data, 'key'), $key);

        // Check if meta exists
        if (!empty($indexes)) {

            // Get single value
            if ($single) {
                $value = $meta_data[current($indexes)]->value;
            }
            // Get multiple values as array
            else {
                $value = array_intersect_key($meta_data, array_flip($indexes));
            }
        }

        // Allow developers to override and return
        return apply_filters($this->get_controller()->prefix_public_hook('get_meta_' . $key), $value, $single, $context, $this);
    }

    /**
     * Delete meta
     *
     * @access public
     * @param string $key
     * @return void
     */
    public function delete_meta($key)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return;
        }

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Get meta indexes by key
        $indexes = array_keys(wp_list_pluck($this->meta_data, 'key'), $key);

        // Unset meta by indexes
        foreach ($indexes as $index) {
            $this->meta_data[$index]->value = null;
        }
    }

    /**
     * Check if meta exists
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function meta_exists($key)
    {
        // Check metadata support
        if (!$this->check_metadata_support()) {
            return false;
        }

        // Check metadata support
        $this->check_metadata_support();

        // Maybe read meta data
        $this->maybe_read_meta_data();

        // Check if meta key exists
        return in_array($key, wp_list_pluck($this->get_meta_data(), 'key'), true);
    }

    /**
     * Check if object supports metadata
     *
     * Throws exception if metadata is not supported
     *
     * @access public
     * @return bool
     */
    public function check_metadata_support()
    {
        // Check metadata support
        $supports_metadata = $this->get_controller()->supports_metadata();

        // Add warning if metadata is not supported but someone is trying to use it
        if (!$supports_metadata) {
            RightPress_Help::doing_it_wrong((get_class($this) . '::' . __FUNCTION__), 'Metadata is not supported for objects of class ' . get_class($this) . '.', '1.0');
        }

        return $supports_metadata;
    }





}
}
