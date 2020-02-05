<?php
/**
* Plugin Name: PC prescribe
* Plugin URI: http://localhost
* Description: PC prescription system
* Version: 2.0
* Author: sudofusion
* Author URI: http://localhost
**/

if (!defined('WPINC')) {
    die;
}

//register_activation_hook(__FILE__, 'prescribe_activate');
//register_deactivation_hook(__FILE__, 'prescribe_deactivate');

function prescribe_deactivate() {
};
function remove_custom_roles() {
    remove_role('doctor');
    remove_role('referrer');
};
function remove_custom_statuses() {
};

add_action('init', 'prescribe_activate');
function prescribe_activate() {
    add_custom_roles();
    // add custom order statuses
    register_pc_order_statuses();
    add_filter('wc_order_statuses', 'add_pc_order_statuses');
    register_batch_post_type();
};
/*creating doctor role and adding capabilities*/
function add_custom_roles() {
    // give administrator custom capabilities too
    $administrator = get_role('administrator');
    $administrator->add_cap('prescribe_capability');
    $administrator->add_cap('retrieve_scripts');
    add_role('doctor', 'Doctor', array(
        'read'                   => true,
        'read_private_pages'     => true,
        'read_private_posts'     => true,
        'edit_posts'             => true,
        'edit_pages'             => true,
        'edit_published_posts'   => true,
        'edit_published_pages'   => true,
        'edit_private_pages'     => true,
        'edit_private_posts'     => true,
        'edit_others_posts'      => true,
        'edit_others_pages'      => true,
        'publish_posts'          => true,
        'publish_pages'          => true,
        'upload_files'           => true,
        'export'                 => true,
        'import'                 => true,
        'list_users'             => true,
        'prescribe_capability'   => true
    ));
    //todo limit referrer capabilities
    add_role('referrer', 'Referrer', array(
        'read'                   => true,
        'read_private_pages'     => true,
        'read_private_posts'     => true,
        'edit_posts'             => true,
        'edit_pages'             => true,
        'edit_published_posts'   => true,
        'edit_published_pages'   => true,
        'edit_private_pages'     => true,
        'edit_private_posts'     => true,
        'edit_others_posts'      => true,
        'edit_others_pages'      => true,
        'publish_posts'          => true,
        'publish_pages'          => true,
        'upload_files'           => true,
        'export'                 => true,
        'import'                 => true,
        'list_users'             => true,
    ));
    //todo limit pharmacist capabilities
    add_role('pharmacist', 'Pharmacist', array(
        'read'                   => true,
        'read_private_pages'     => true,
        'read_private_posts'     => true,
        'edit_posts'             => true,
        'edit_pages'             => true,
        'edit_published_posts'   => true,
        'edit_published_pages'   => true,
        'edit_private_pages'     => true,
        'edit_private_posts'     => true,
        'edit_others_posts'      => true,
        'edit_others_pages'      => true,
        'publish_posts'          => true,
        'publish_pages'          => true,
        'upload_files'           => true,
        'export'                 => true,
        'import'                 => true,
        'list_users'             => true,
        'retrieve_scripts'       => true
    ));
};
add_action('admin_menu', 'custom_admin_menu');
function custom_admin_menu() {
    remove_menu_page('edit.php?post_type=script_batch');
};

function register_pc_order_statuses() {
    register_post_status('wc-waiting', array(
        'label'                         => 'Awaiting scripting',
        'public'                        => false,
        'show_in_admin_status_list'     => true,
        'show_in_admin_all_list'        => true,
        'exclude_from_search'           => false,
        'label_count'                   => _n_noop('Awaiting scripting <span class="count">(%s)</span>', 'Awaiting scripting <span class="count">(%s)</span>'))
    );
    register_post_status('wc-doctorquestions', array(
        'label'                         => 'Doctor questions',
        'public'                        => false,
        'show_in_admin_status_list'     => true,
        'show_in_admin_all_list'        => true,
        'exclude_from_search'           => false,
        'label_count'                   => _n_noop('Doctor questions <span class="count">(%s)</span>', 'Doctor questions <span class="count">(%s)</span>'))
    );
    register_post_status('wc-scripted', array(
        'label'                         => 'Scripted',
        'public'                        => false,
        'show_in_admin_status_list'     => true,
        'show_in_admin_all_list'        => true,
        'exclude_from_search'           => false,
        'label_count'                   => _n_noop('Scripted <span class="count">(%s)</span>', 'Scripted <span class="count">(%s)</span>'))
    );
    register_post_status('wc-emailed', array(
        'label'                         => 'Emailed',
        'public'                        => true,
        'show_in_admin_status_list'     => true,
        'show_in_admin_all_list'        => true,
        'exclude_from_search'           => false,
        'label_count'                   => _n_noop('Emailed <span class="count">(%s)</span>', 'Emailed <span class="count">(%s)</span>'))
    );
    register_post_status('wc-shipped', array(
        'label'                         => 'Shipped',
        'public'                        => true,
        'show_in_admin_status_list'     => true,
        'show_in_admin_all_list'        => true,
        'exclude_from_search'           => false,
        'label_count'                   => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped Arrival <span class="count">(%s)</span>'))
    );
};
function add_pc_order_statuses($order_statuses) {
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-waiting'] = 'Awaiting scripting';
            $new_order_statuses['wc-doctorquestions'] = 'Doctor questions';
            $new_order_statuses['wc-scripted'] = 'Scripted';
            $new_order_statuses['wc-emailed'] = 'Emailed';
            $new_order_statuses['wc-shipped'] = 'Shipped';
        }
    }
    return $new_order_statuses;
};

add_filter('bulk_actions-edit-shop_order', 'add_custom_bulk_actions');
function add_custom_bulk_actions($bulk_actions) {
    $bulk_actions['mark_as_waiting'] = 'Mark as awaiting scripting';
    return $bulk_actions;
};
add_action('admin_action_mark_as_waiting', 'mark_as_waiting');
function mark_as_waiting() {
    if (!isset($_REQUEST['post']) && !is_array($_REQUEST['post'])) {
        return;
    }

    $current_user = wp_get_current_user();
    foreach ($_REQUEST['post'] as $order_id) {
        $order = new WC_Order($order_id);
        $order->update_status('waiting', 'Bulk action marked as awaiting scripting by '.$current_user->display_name.' - ', true);
    }
};

function register_batch_post_type() {
    register_post_type('script_batch',
        array(
            'labels' => array(
                'name' => 'Batches',
                'singular_name' => 'Batch'),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'batch'))
    );
};

//todo add custom admin notices
/*
add_action('admin_notices', 'custom_admin_notices');
function misha_custom_order_status_notices() {
    global $pagenow, $typenow;

    if ($typenow == 'shop_order'
        && $pagenow == 'edit.php'
        && isset( $_REQUEST['marked_awaiting_shipment'] )
        && $_REQUEST['marked_awaiting_shipment'] == 1
        && isset( $_REQUEST['changed'] ) ) {

		$message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $_REQUEST['changed'] ), number_format_i18n( $_REQUEST['changed'] ) );
		echo "<div class=\"updated\"><p>{$message}</p></div>";
	}
};
*/


add_action('admin_menu', 'add_prescribe_admin_page');
function add_prescribe_admin_page() {
    add_menu_page('Prescriptions', 'Prescribe', 'manage_options', 'prescribe_page.php', 'render_prescribe_page', 'dashicons-edit', 4);
};
function render_prescribe_page() {
    include_once('prescribe_page.php');
};


add_action('admin_menu', 'add_prescribe_admin_page_new');
function add_prescribe_admin_page_new() {
    add_submenu_page('prescribe_page.php', 'Prescriptions', 'Prescribe 2.0', 'manage_options', 'prescribe_page_new.php', 'render_prescribe_page_new', 'dashicons-edit', 4);
};
function render_prescribe_page_new() {
    include_once('prescribe_page_new.php');
};

add_action('admin_menu', 'add_pdf_admin_page');
function add_pdf_admin_page() {
    add_menu_page('Scripted orders', 'Script PDF', 'manage_options', 'script-pdf.php', 'render_pdf_page', 'dashicons-media-document', 4);
};
function render_pdf_page() {
    include_once('script-pdf.php');
};

add_action('admin_menu', 'add_csv_admin_page');
function add_csv_admin_page() {
    add_menu_page('Order batches', 'Order batches', 'manage_options', 'script-csv.php', 'render_csv_page', 'dashicons-media-document', 4);
};
function render_csv_page() {
    include_once('script-csv.php');
};

// auto prescribing function (processing orders => scripted orders)
//add_action('woocommerce_order_status_processing', 'script_order', 10, 1);
// auto prescribing function (waiting orders => scripted orders)
//add_action('woocommerce_order_status_waiting', 'script_order', 10, 1);
function script_order($order_id) {
    $order = wc_get_order($order_id);

	foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item['product_id'];
        $dosage_instructions = get_field('dosage_instructions', $product_id);
        $requires_prescription = get_field('requires_prescription', $product_id);
        wc_update_order_item_meta($item_id, 'dosage_instructions', $dosage_instructions);
        wc_update_order_item_meta($item_id, 'requires_prescription', $requires_prescription);
	}
	error_log($order_id);
	$prescribing_doctor = get_user_by('id', 5611); // drneha user id
	$scripted_time = current_time('Y-m-d H:i:s', 0);
	update_field('field_5c91e1398bf00', 5611, $order_id); // drneha user id
	update_field('field_5c91e1518bf01', $scripted_time, $order_id);
	//update_post_meta($order_id, 'prescribing_doctor', $prescribing_doctor->ID);
	update_post_meta($order_id, 'prescribing_doctor', 5611); // drneha user id
	update_post_meta($order_id, 'scripted_time', $scripted_time);
	error_log(get_field('field_5c91e1398bf00', $order_id, false));
	error_log(get_field('field_5c91e1518bf01', $order_id, false));

    $order->update_status('wc-scripted', 'Order was scripted by '.$prescribing_doctor->display_name);
};
