<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Object_Data_Store')) {

/**
 * Object Data Store
 *
 * @class RightPress_Object_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object_Data_Store
{

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

        // Iterate over properties
        foreach ($object->get_data_keys() as $key) {

            // Update property only if it has changed
            if (array_key_exists($key, $changes)) {

                // Get value
                $getter = 'get_' . $key;
                $value = $object->{$getter}('store');

                // Add to data array
                $data[$key] = $value;
            }
        }

        // Set updated property
        if (!empty($data)) {
            $object->set_updated(time());
            $data['updated'] = $object->get_updated('store');
        }

        return $data;
    }





}
}
