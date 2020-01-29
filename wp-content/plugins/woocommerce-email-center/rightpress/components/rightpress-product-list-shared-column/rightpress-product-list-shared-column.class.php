<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Product_List_Shared_Column')) {

/**
 * RightPress Product List Shared Column
 *
 * @class RightPress_Product_List_Shared_Column
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_List_Shared_Column
{

    protected static $column_name = 'rightpress-product-list-shared-column';

    // Singleton instance
    protected static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Add product list custom column
        add_filter('manage_product_posts_columns', array($this, 'add_product_list_custom_column'), 99);

        // Display product list custom column value
        add_action('manage_product_posts_custom_column', array($this, 'print_product_list_custom_column_value'), 99, 2);
    }

    /**
     * Add product list custom column
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function add_product_list_custom_column($columns)
    {
        // Define custom column
        // TBD
        $custom_column = array(self::$column_name => '<span class="rightpress_product_list_shared_column_header">TBD</span>');

        // Insert before date if it is set
        if (isset($columns['date'])) {
            $columns = RightPress_Help::insert_to_array_before_key($columns, 'date', $custom_column);
        }
        // Alternatively insert as the last element
        else {
            $columns = array_merge($columns, $custom_column);
        }

        // Return columns array
        return $columns;
    }

    /**
     * Display product list custom column value
     *
     * @access public
     * @param string $column_name
     * @param int $post_id
     * @return void
     */
    public function print_product_list_custom_column_value($column_name, $post_id)
    {
        // Not our column
        if ($column_name !== self::$column_name) {
            return;
        }

        // Get values to display
        $values = apply_filters('rightpress_product_list_shared_column_values', array(), $post_id);

        // Display values
        echo join('', $values);
    }



}

RightPress_Product_List_Shared_Column::get_instance();

}
