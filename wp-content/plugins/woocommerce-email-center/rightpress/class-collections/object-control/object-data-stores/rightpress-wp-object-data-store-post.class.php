<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_WP_Object_Data_Store')) {
    require_once('rightpress-wp-object-data-store.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WP_Object_Data_Store_Post')) {

/**
 * WordPress Post Type Data Store
 *
 * @class RightPress_WP_Object_Data_Store_Post
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Data_Store_Post extends RightPress_WP_Object_Data_Store
{

    /**
     * Properties with default values
     */

    protected $post_property_keys = array();

    /**
     * Common properties
     */

    protected $common_post_property_keys = array(
        'status'    => 'post_status',
        'created'   => 'post_date',
        'updated'   => 'post_modified',
    );

    /**
     * Get meta type
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_meta_type(&$object)
    {
        return 'post';
    }

    /**
     * Get object id field name
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_object_id_field_name(&$object)
    {
        return 'post_id';
    }

    /**
     * Create object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array())
    {
        // Reset plugin version
        $object->reset_plugin_version();

        // Set created datetime
        $object->set_created(time());

        // Run status getter so that default status is set if not set manually
        $object->get_status();

        // Get changes
        $changes = $object->get_changes();

        // Get data for database
        $data = $this->get_data_for_database($object, $changes);

        // Set main post args
        $post_args = array_merge($data, array(
            'post_author'       => $this->get_new_post_author($object),
            'post_type'         => $this->get_post_type(),
            'comment_status'    => ($object->get_controller()->supports_comments() ? 'open' : 'closed'),
            'ping_status'       => 'closed',
            'post_password'     => $this->get_post_type() . '_' . RightPress_Help::get_hash(true),
            'post_parent'       => 0,
        ));

        // Insert post
        $id = wp_insert_post(apply_filters($object->get_controller()->prefix_public_hook('post_args'), $post_args), true);

        // Check if post was inserted
        if ($id && !is_wp_error($id)) {

            // Set object id
            $object->set_id($id);

            // Save properties
            $this->update_properties($object, $changes);

            // Save meta data
            $object->save_meta_data();

            // Apply changes
            $object->apply_changes();
        }
    }

    /**
     * Read object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function read(&$object, $args = array())
    {
        // Reset any current data
        $object->reset_data();

        // Get object id
        $id = $object->get_id();

        // Check post and load it
        if (!$id || !RightPress_Help::post_type_is($id, $this->get_post_type()) || !($post = get_post($id))) {
            throw new Exception('RightPress: Data for object ' . get_class($object) . ' #' . $object->get_id() . ' could not be read from database.');
        }

        // Read properties
        $this->read_properties($object, $post);

        // Read meta data
        $object->read_meta_data();

        // Object data is ready
        $object->set_data_ready(true);
    }

    /**
     * Update object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function update(&$object, $args = array())
    {
        global $wpdb;

        // Get object id
        $id = $object->get_id();

        // Reset plugin version
        $object->reset_plugin_version();

        // Get changes
        $changes = $object->get_changes();

        // Update properties
        if ($this->update_properties($object, $changes)) {

            // Reset updated time
            $object->set_updated(time());
            $changes['updated'] = $object->get_updated('edit');
        }

        // Get data for database
        $data = $this->get_data_for_database($object, $changes);

        // Check if any post properties need to be updated
        if (!empty($data)) {

            // Update by directly calling database if post is currently being saved by another process
            if (doing_action('save_post')) {
                $wpdb->update($wpdb->posts, $data, array('ID' => $id));
                clean_post_cache($id);
            }
            // Update in the regular way to fire post updated hooks
            else {
                wp_update_post(array_merge($data, array('ID' => $id)));
            }

            // Reload meta data in case developers changed via post updated hook
            $object->read_meta_data();
        }

        // Save meta data
        $object->save_meta_data();

        // Apply changes
        $object->apply_changes();
    }

    /**
     * Delete object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array())
    {
        // Import arguments
        extract(RightPress_Help::filter_by_keys_with_defaults($args, array('permanently' => false)));

        // Get object id
        $id = $object->get_id();

        // Object must exist in the databse to be deleted
        if (!$id) {
            return;
        }

        // Delete permanently
        if ($permanently) {
            wp_delete_post($id);
            $object->set_id(0);
            do_action($object->get_controller()->prefix_public_hook('deleted'), $object, $id);
        }
        // Move to trash
        else {
            wp_trash_post($id);
            $object->set_status('trash');
            do_action($object->get_controller()->prefix_public_hook('trashed'), $object);
        }
    }

    /**
     * Read object properties from post or post meta
     *
     * @access protected
     * @param object $object
     * @param object $post
     * @return void
     */
    protected function read_properties(&$object, $post)
    {
        // Get object id
        $id = $object->get_id();

        // Iterate over properties
        foreach ($object->get_data_keys() as $key) {

            $value = null;

            // Get value from post
            if ($post_property_key = $this->get_post_property_key($key)) {
                if (isset($post->$post_property_key)) {
                    $value = $post->$post_property_key;
                }
            }
            // Get value from meta
            else if (metadata_exists('post', $id, $key)) {
                $value = get_post_meta($id, $key, true);
            }

            // Get setter method name
            $method = 'set_' . $key;

            // Check if value is set and setter method exists
            if ($value !== null && method_exists($object, $method)) {
                $object->{$method}($value);
            }
        }
    }

    /**
     * Update changed object properties in the database
     *
     * Only updates properties saved as post meta - properties saved as post
     * object properties are updated in create() and update() methods
     *
     * Returns a number of properties updated
     *
     * @access protected
     * @param object $object
     * @param array $changes
     * @return int
     */
    protected function update_properties(&$object, $changes = array())
    {
        $updated = 0;

        // Get object id
        $id = $object->get_id();

        // Iterate over properties
        foreach ($object->get_data_keys() as $key) {

            // Skip post properties
            if ($this->get_post_property_key($key)) {
                continue;
            }

            // Update property if it has changed or does not exist in database
            if (array_key_exists($key, $changes) || !metadata_exists('post', $id, $key)) {

                // Get value
                $getter = 'get_' . $key;
                $value = $object->{$getter}('store');

                // Update
                update_post_meta($id, $key, $value);

                // Increment
                $updated++;
            }
        }

        // Return a number of properties updated
        return $updated;
    }

    /**
     * Get all post property keys
     *
     * @access protected
     * @return array
     */
    protected function get_post_property_keys()
    {
        return array_merge($this->common_post_property_keys, $this->post_property_keys);
    }

    /**
     * Get post property key if object property value is stored as post property
     * as opposed to post meta data
     *
     * @access protected
     * @param string $key
     * @return string|bool
     */
    protected function get_post_property_key($key)
    {
        // Get post property keys
        $post_property_keys = $this->get_post_property_keys();

        // Return property key or false if provided key does not have a corresponding post property key
        return isset($post_property_keys[$key]) ? $post_property_keys[$key] : false;
    }

    /**
     * Get data for database
     *
     * @access protected
     * @param object $object
     * @param array $changes
     * @return array
     */
    public function get_data_for_database(&$object, $changes)
    {
        $data = array();

        // Get changed post properties
        if ($changed_post_properties = array_intersect(array_keys($this->get_post_property_keys()), array_keys($changes))) {

            // Set updated datetime
            $object->set_updated(time());
            $changed_post_properties[] = 'updated';

            // Iterate over changed post properties
            foreach ($changed_post_properties as $key) {

                // Get post property key
                $post_property_key = $this->get_post_property_key($key);

                // Get value
                $getter = 'get_' . $key;
                $value = $object->{$getter}('store');

                // Additional datetime property handling
                if ($object->is_property_datetime($key)) {

                    // Result from getter is in GMT
                    $value_gmt = $value;

                    // Get non GMT value for default property
                    $value = $object->{$getter}('store', array('is_gmt' => false));

                    // Get property key for GMT value
                    $post_property_key_gmt = $post_property_key . '_gmt';

                    // Set GMT value
                    $data[$post_property_key_gmt] = $value_gmt;
                }

                // Set property value
                $data[$post_property_key] = $value;
            }
        }

        return $data;
    }

    /**
     * Get new post author user id
     *
     * @access public
     * @param object $object
     * @return int
     */
    public function get_new_post_author(&$object)
    {
        return 1;
    }














}
}
