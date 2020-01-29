<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_WP_Object_Controller')) {
    require_once('rightpress-wp-object-controller.class.php');
}

// Check if class has already been loaded
if (!class_exists('RightPress_WP_Object_Controller_Post')) {

/**
 * WordPress Post Type Controller
 *
 * @class RightPress_WP_Object_Controller_Post
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Controller_Post extends RightPress_WP_Object_Controller
{

    protected $supports_metadata = true;

    /**
     * Properties with default values
     */

    protected $allowed_views        = array('all', 'trash');
    protected $allowed_bulk_actions = array('trash', 'untrash', 'delete');

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Construct parent
        parent::__construct();

        // Get post type
        $post_type = $this->data_store->get_post_type();

        // Register custom post type
        add_action('init', function() {
            register_post_type($this->data_store->get_post_type(), $this->get_post_type_params());
        }, 0);

        // Register custom taxonomies
        add_action('init', function() {
            foreach ($this->get_taxonomies() as $taxonomy_key => $taxonomy_args) {
                register_taxonomy($taxonomy_key, $this->data_store->get_post_type(), $taxonomy_args);
            }
        }, 0);

        // Register custom post statuses
        add_action('init', function() {
            foreach ($this->get_statuses() as $key => $values) {
                register_post_status($this->prefix_status($key), array_merge(array(
                    'public'                    => false,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                ), $values));
            }
        }, 0);

        // Set up admin menu
        if ($post_type === $this->get_main_post_type()) {
            add_action('admin_head', array($this, 'set_up_admin_menu'));
            add_filter('menu_order', array($this, 'fix_admin_menu_order'), $this->get_menu_priority());
        }

        // Remove default post row actions
        add_filter('post_row_actions', function($actions) {
            global $post;
            return RightPress_Help::post_type_is($post, $this->data_store->get_post_type()) ? array() : $actions;
        });

        // Remove date filter if object is not chronologic
        if (!$this->is_chronologic()) {
            add_filter('months_dropdown_results', function($months) {
                global $typenow;
                return $typenow === $this->data_store->get_post_type() ? array() : $months;
            });
        }

        // List view modifications
        add_filter('views_edit-' . $post_type, array($this, 'manage_list_views'));
        add_filter('bulk_actions-edit-' . $post_type, array($this, 'manage_list_bulk_actions'));
        add_action('restrict_manage_posts', array($this, 'add_list_filters'));
        add_filter('manage_' . $post_type . '_posts_columns', array($this, 'manage_list_columns'));
        add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'print_column_value'), 10, 2);

        // Add enctype attribute to form tag to enable file uploads
        add_action('post_edit_form_tag', function($post) {
            echo RightPress_Help::post_type_is($post, $this->data_store->get_post_type()) ? ' enctype="multipart/form-data" ' : '';
        });

        // Meta box setup
        add_action('add_meta_boxes', array($this, 'remove_unsupported_meta_boxes'), 9999, 2);
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 10000, 2);

        // Save post
        add_action('save_post', array($this, 'save_post'), 9, 2);










// TBD: FROM EMAIL CENTER..
return;

// Process status change when changed not from within edit page
add_action('init', array($this, 'process_status_change'), 999);

// Process duplicate action
add_action('init', array($this, 'process_duplicate'), 999);

// Handle list filter query
add_filter('parse_query', array($this, 'handle_list_filter_query'));

// Expand list search context
add_filter('posts_join', array($this, 'expand_list_search_context_join'));
add_filter('posts_where', array($this, 'expand_list_search_context_where'));
add_filter('posts_groupby', array($this, 'expand_list_search_context_group_by'));

// Object post trashed
add_action('trashed_post', array($this, 'post_trashed'));

// Change default post updated notice
add_filter('post_updated_messages', array($this, 'change_post_updated_notice'));
    }

    /**
     * Get custom taxonomies
     *
     * @access public
     * @return array
     */
    public function get_taxonomies()
    {
        return array();
    }

    /**
     * Get meta boxes
     *
     * @access public
     * @return array
     */
    public function get_meta_boxes()
    {
        return array();
    }

    /**
     * Get allowed views
     *
     * @access public
     * @return array
     */
    public function get_allowed_views()
    {
        return $this->allowed_views;
    }

    /**
     * Get allowed bulk actions
     *
     * @access public
     * @return array
     */
    public function get_allowed_bulk_actions()
    {
        return $this->allowed_bulk_actions;
    }

    /**
     * Get list columns
     *
     * @access public
     * @return array
     */
    public function get_list_columns()
    {
        return array();
    }

    /**
     * Get post actions
     *
     * @access public
     * @param object $object
     * @return array
     */
    public function get_post_actions($object = null)
    {
        return array();
    }

    /**
     * Set up admin menu
     *
     * Developers: this method removes all 3rd party menu links, please add them
     * later than action 'admin_head' position 10 so that they are preserved
     *
     * @access public
     * @return void
     */
    public function set_up_admin_menu()
    {
        global $submenu;

        // Get menu items
        $menu_items = $this->get_menu_items();

        // Get parent item
        $parent = reset($menu_items);

        // Check if parent item can be found
        if (isset($submenu[$parent])) {

            $admin_menu = array();

            // Set all items that are present in our menu items array
            foreach ($menu_items as $submenu_key) {
                foreach ($submenu[$parent] as $item_key => $item) {
                    if ($item[2] === $submenu_key) {
                        $admin_menu[$item_key] = $submenu[$parent][$item_key];
                        break;
                    }
                }
            }

            $submenu[$parent] = $admin_menu;
        }
    }

    /**
     * Fix admin menu order
     *
     * @access public
     * @param array $menu_order
     * @return array
     */
    public function fix_admin_menu_order($menu_order)
    {
        $anchor = null;

        // Find anchor
        foreach ($menu_order as $index => $item) {

            if ($item === 'woocommerce') {
                $anchor = $index;
            }
            else if ($item !== null && $item === 'edit.php?post_type=product') {
                $anchor = $index;
            }
        }

        // No anchor found
        if ($anchor === null) {
            return $menu_order;
        }

        // Define custom order
        $custom_order = array();

        // Format own item
        $own_item = 'edit.php?post_type=' . $this->data_store->get_post_type();

        // Iterate over menu items
        foreach ($menu_order as $index => $item) {

            // Add our item immediately after our anchor item
            if ($index === $anchor) {
                $custom_order[] = $item;
                $custom_order[] = $own_item;
            }
            // Add all other items except our own
            else if ($item !== $own_item) {
                $custom_order[] = $item;
            }
        }

        return $custom_order;
    }

    /**
     * Manage list views
     *
     * @access public
     * @param array $views
     * @return array
     */
    public function manage_list_views($views)
    {
        $new_views = array();

        $allowed_views = $this->get_allowed_views();

        foreach ($views as $view_key => $view) {
            if (in_array($view_key, $allowed_views)) {
                $new_views[$view_key] = $view;
            }
        }

        return $new_views;
    }

    /**
     * Manage list bulk actions
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function manage_list_bulk_actions($actions)
    {
        $new_actions = array();

        $allowed_bulk_actions = $this->get_allowed_bulk_actions();

        foreach ($actions as $action_key => $action) {
            if (in_array($action_key, $allowed_bulk_actions)) {
                $new_actions[$action_key] = $action;
            }
        }

        return $new_actions;
    }

    /**
     * Add filtering capabilities
     *
     * @access public
     * @return void
     */
    public function add_list_filters()
    {
// TBD
return;

/*
        global $typenow;
        global $wp_query;

        if ($typenow !== $this->data_store->get_post_type()) {
            return;
        }

        // Iterate over taxonomies
        foreach ($this->get_taxonomies() as $key => $labels) {

            $taxonomy_key = $this->data_store->get_post_type() . '_' . $key;

            // Extract selected filter options
            $selected = array();

            if (!empty($wp_query->query[$taxonomy_key]) && is_numeric($wp_query->query[$taxonomy_key])) {
                $selected[$taxonomy_key] = $wp_query->query[$taxonomy_key];
            }
            else if (!empty($wp_query->query[$taxonomy_key])) {
                $term = get_term_by('slug', $wp_query->query[$taxonomy_key], $taxonomy_key);
                $selected[$taxonomy_key] = $term ? $term->term_id : 0;
            }
            else {
                $selected[$taxonomy_key] = 0;
            }

            // Add options
            wp_dropdown_categories(array(
                'show_option_all'   =>  $labels['all'],
                'taxonomy'          =>  $taxonomy_key,
                'name'              =>  $taxonomy_key,
                'selected'          =>  $selected[$taxonomy_key],
                'show_count'        =>  true,
                'hide_empty'        =>  false,
            ));
        }
*/
    }

    /**
     * Manage list columns
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function manage_list_columns($columns)
    {
        global $typenow;

        $new_columns = array();

        // Leave only allowed default columns
        foreach ($columns as $key => $label) {

            $allowed_keys = array();

            if ($this->is_editable()) {
                $allowed_keys[] = 'cb';
            }

            if (in_array($key, $allowed_keys)) {
                $new_columns[$key] = $label;
            }
        }

        // Add custom columns
        foreach ($this->get_list_columns() as $key => $label) {
            $new_columns[$key] = $label;
        }

        return $new_columns;
    }

    /**
     * Print column value
     *
     * @access public
     * @param array $column
     * @param int $post_id
     * @return void
     */
    public function print_column_value($column, $post_id)
    {
        // Load object
        if ($object = $this->get_object($post_id)) {

            // Get value
            // TBD
            if ($column === 'id') {
                echo '<a href="' . get_edit_post_link($object->get_id()) . '">' . $object->get_id() . '</a>';
            }
        }
    }

    /**
     * Remove unsupported meta boxes
     *
     * @access public
     * @param string $post_type
     * @param object $post
     * @return void
     */
    public function remove_unsupported_meta_boxes($post_type, $post)
    {
        global $wp_meta_boxes;

        // Check post type
        if ($post_type === $this->data_store->get_post_type()) {

            // Get meta boxes for current view
            $screen = get_current_screen();
            $meta_boxes = isset($wp_meta_boxes[$screen->id]) ? $wp_meta_boxes[$screen->id] : array();

            // Get meta boxes whitelist
            $whitelist = apply_filters($this->prefix_public_hook('meta_boxes_whitelist'), array());

            // Iterate over meta boxes
            foreach ($meta_boxes as $context => $by_context) {
                foreach ($by_context as $subcontext => $by_subcontext) {
                    foreach ($by_subcontext as $meta_box_id => $meta_box) {
                        if (!in_array($meta_box_id, $whitelist)) {
                            remove_meta_box($meta_box_id, $post_type, $context);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add meta boxes
     *
     * @access public
     * @param string $post_type
     * @param object $post
     * @return void
     */
    public function add_meta_boxes($post_type, $post)
    {
        if ($post_type === $this->data_store->get_post_type()) {

            foreach ($this->get_meta_boxes() as $key => $args) {

                add_meta_box(
                    $post_type . '_' . $key,
                    $args['title'],
                    array($this, ('print_meta_box_' . $key)),
                    $post_type,
                    $args['context'],
                    $args['priority']
                );
            }
        }
    }

    /**
     * Save post
     *
     * @access public
     * @param int $post_id
     * @param object $post
     * @param array $posted
     * @return void
     */
    public function save_post($post_id, $post, $posted = array())
    {
        // Check if required properties were passed in
        if (empty($post_id) || !is_a($post, 'WP_Post')) {
            return;
        }

        // Get post type
        $post_type = $this->data_store->get_post_type();

        // Check post type
        if ($post->post_type !== $post_type) {
            return;
        }

        // Make sure it is not a draft save action
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || is_int(wp_is_post_autosave($post)) || is_int(wp_is_post_revision($post))) {
            return;
        }

        // Get posted values
        $posted = !empty($posted) ? $posted : $_POST;

        // Make sure the correct post ID was passed from form
        if (empty($posted['post_ID']) || $posted['post_ID'] != $post_id) {
            return;
        }

        // Validate nonce
        if (empty($posted[$post_type . '_save_nonce']) || !wp_verify_nonce($posted[$post_type . '_save_nonce'], ($post_type . '_save'))) {
            return;
        }

        // Make sure user has permission to save data
        // TBD: are we sure we won't be using custom capabilities here? Or do PLUGIN::is_admin() ?
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if object is editable
        if (!$this->is_editable()) {
            return;
        }

        // Get method name
        if (!empty($posted[$post_type . '_button']) && $posted[$post_type . '_button'] === 'actions' && !empty($posted[$post_type . '_actions'])) {
            $method = 'handle_action_' . $posted[$post_type . '_actions'];
        }
        else {
            $method = 'handle_action_save';
        }

        // Get data for this specific post type
        $data = isset($posted[$post_type]) ? $posted[$post_type] : array();

        // Remove hook to prevent infinite loop
        remove_action('save_post', array($this, 'save_post'), 9, 2);

        // Handle action
        $this->$method($post_id, $data, $posted);

        // Restore hook
        add_action('save_post', array($this, 'save_post'), 9, 2);
    }

    /**
     * Register post type controller class
     *
     * @access public
     * @param array $classes
     * @return array
     */
    public function register_post_type_controller_class($classes)
    {
        $post_type = $this->data_store->get_post_type();
        $classes[$post_type] = get_class($this);
        return $classes;
    }



}
}
