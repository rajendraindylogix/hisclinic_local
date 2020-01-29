<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Object_Data_Store')) {
    require_once('rightpress-object-data-store.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WP_Object_Data_Store')) {

/**
 * WordPress Object Data Store
 *
 * @class RightPress_WP_Object_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Data_Store extends RightPress_Object_Data_Store
{

    /**
     * Add object meta data to the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function add_meta(&$object, $meta, $args = array())
    {
        // Sanitize value
        $value = is_string($meta->value) ? addslashes($meta->value) : $meta->value;

        // Get meta type
        $meta_type = $this->get_meta_type($object);

        // Add meta data
        add_metadata($meta_type, $object->get_id(), $this->prefix_meta_key($meta->key), $value, false);
    }

    /**
     * Read object meta data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return mixed
     */
    public function read_meta(&$object, $args = array())
    {
        global $wpdb;

        $sanitized = array();

        // Get database properties
        $meta_table_name = $this->get_meta_table_name($object);
        $object_id_field_name = $this->get_object_id_field_name($object);

        // Get meta from database
        $sql = $wpdb->prepare("SELECT meta_id, meta_key, meta_value FROM {$meta_table_name} WHERE {$object_id_field_name} = %d ORDER BY meta_id", $object->get_id());
        $results = $wpdb->get_results($sql);

        // Sanitize results
        if (is_array($results) && !empty($results)) {
            foreach ($results as $meta) {

                // Check if entry is true meta
                if ($this->is_true_meta_key($meta->meta_key)) {

                    // Sanitize meta value
                    $sanitized[] = new RightPress_Meta(array(
                        'id'    => absint($meta->meta_id),
                        'key'   => $this->unprefix_meta_key($meta->meta_key),
                        'value' => maybe_unserialize($meta->meta_value),
                    ));
                }
            }
        }

        // Return sanitized results
        return $sanitized;
    }

    /**
     * Update object meta data in the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function update_meta(&$object, $meta, $args = array())
    {
        // Get meta type
        $meta_type = $this->get_meta_type($object);

        // Update meta
        update_metadata_by_mid($meta_type, $meta->id, $meta->value, $this->prefix_meta_key($meta->key));
    }

    /**
     * Delete object meta data from the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function delete_meta(&$object, $meta, $args = array())
    {
        // Get meta type
        $meta_type = $this->get_meta_type($object);

        // Delete meta
        delete_metadata_by_mid($meta_type, $meta->id);
    }

    /**
     * Prefix meta key
     *
     * All custom meta is prefixed with _x_ to ensure it does not clash with
     * regular object properties that can be saved as WordPress post meta
     *
     * @access protected
     * @param string $key
     * @return string
     */
    protected function prefix_meta_key($key)
    {
        return '_x_' . $key;
    }

    /**
     * Remove prefix from meta key
     *
     * @access protected
     * @param string $key
     * @return string
     */
    protected function unprefix_meta_key($key)
    {
        if ($this->is_true_meta_key($key)) {
            $key = substr($key, 3);
        }

        return $key;
    }

    /**
     * Check if key is true meta key
     *
     * @access protected
     * @param string $key
     * @return bool
     */
    protected function is_true_meta_key($key)
    {
        return RightPress_Help::string_begins_with_substring($key, '_x_');
    }

    /**
     * Get table name
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_table_name(&$object)
    {
        global $wpdb;
        return $wpdb->prefix . $this->get_meta_type($object) . 's';
    }

    /**
     * Get meta table name
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_meta_table_name(&$object)
    {
        global $wpdb;
        return $wpdb->prefix . $this->get_meta_type($object) . 'meta';
    }




}
}
