<?php

/* Template Name: Admin batch content page */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

if (!empty($_GET['batch_number'])) {
    $batch_number = $_GET['batch_number'];
    $batch = wp_get_single_post($batch_number);
    echo $batch->post_content;
}
die();

?>
