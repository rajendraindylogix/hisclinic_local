<?php

/**
 * Order items table plain text email template for custom emails
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, null);

echo strtoupper(sprintf(__('Order number: %s', 'woocommerce'), $order->get_order_number())) . "\n";

echo wc_format_datetime($order->get_date_created()) . "\n";

do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, null);

echo "\n";

foreach ($order->get_items() as $item_id => $item) {

    $_product   = apply_filters('woocommerce_order_item_product', $item->get_product(), $item);

    if (apply_filters('woocommerce_order_item_visible', true, $item)) {

        // Title
        echo apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false);

        // SKU
        if ($order->get_status() !== 'completed' && $_product->get_sku()) {
            echo ' (#' . $_product->get_sku() . ')';
        }

        // Allow other plugins to add additional product information here
        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order);

        // Variation
        if ($item_meta = RP_WCEC_Helper::get_wc_display_item_meta($item, true)) {
            echo "\n" . $item_meta;
        }

        // Quantity
        echo "\n" . sprintf(__('Quantity: %s', 'woocommerce'), apply_filters('woocommerce_email_order_item_quantity', $item->get_quantity(), $item));

        // Cost
        echo "\n" . sprintf(__('Cost: %s', 'woocommerce'), $order->get_formatted_line_subtotal($item));

        // Download URLs
        if (!$sent_to_admin && $order->is_download_permitted() && $_product->exists() && $_product->is_downloadable()) {

            $download_files = $order->get_item_downloads($item);
            $i = 0;

            foreach ($download_files as $download_id => $file) {
                $i++;

                if (count($download_files) > 1) {
                    $prefix = sprintf(__('Download %d', 'woocommerce'), $i);
                }
                elseif ($i == 1) {
                    $prefix = __('Download', 'woocommerce');
                }

                echo "\n" . $prefix . '(' . esc_html($file['name']) . '): ' . esc_url($file['download_url']);
            }
        }

        // Allow other plugins to add additional product information here
        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order);
    }

    // Note
    if (($sent_to_admin ? false : in_array($order->get_status(), array('processing', 'completed'))) && ($purchase_note = $_product->get_purchase_note())) {
        echo "\n" . do_shortcode(wp_kses_post($purchase_note));
    }

    echo "\n\n";
}

echo "==========\n\n";

if ($totals = $order->get_order_item_totals()) {
    foreach ($totals as $total) {
        echo $total['label'] . "\t " . $total['value'] . "\n";
    }
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

// Get after order table content
ob_start();
do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, null);
$action_content = ob_get_clean();

if (!empty($action_content)) {
    echo $action_content;
    echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}
