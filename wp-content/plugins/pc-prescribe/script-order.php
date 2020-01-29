<?php
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
    die();
}

global $wpdb;
global $woocommerce;

if (!empty($_GET['order_id']) && !empty($_GET['order_action'])) {
    $prescribing_doctor = wp_get_current_user();
    $scripted_time = current_time('Y-m-d H:i:s', 0);
    $order_id = $_GET['order_id'];
    $order = wc_get_order($order_id);
    $order_action = $_GET['order_action'];
    if ($order_action === 'script') {
        $order->update_status('wc-scripted', 'Order was scripted by '.$prescribing_doctor->display_name);
        update_field('prescribing_doctor', $prescribing_doctor->ID, $order_id);
        update_field('scripted_time', $scripted_time, $order_id);
    } elseif ($order_action === 'hold') {
        $order->update_status('wc-doctorquestions', 'Order was set to Doctor Questions by '.$prescribing_doctor->display_name);
    }
}
?>
