<?php

/**
 * Email order items template
 * Based on WooCommerce 2.4
 * Adapted to work with WooCommerce 2.2+
 * Tested up to WooCommerce 2.5.5
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// $email variable fix
$email = isset($email) ? $email : null;

?>

<?php foreach ($items as $item_id => $item): ?>

<?php
    $_product   = apply_filters('woocommerce_order_item_product', $item->get_product(), $item);
?>

<?php if (apply_filters('woocommerce_order_item_visible', true, $item)): ?>
    <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
        <td class="td rp_wcec_item">
            <?php

                // Show title/image etc
                if ($show_image) {
                    echo apply_filters('woocommerce_order_item_thumbnail', '<div class="rp_wcec_item_thumbnail"><img src="' . ($_product->get_image_id() ? current(wp_get_attachment_image_src($_product->get_image_id(), 'thumbnail')) : wc_placeholder_img_src()) .'" alt="' . esc_attr__('Product Image', 'woocommerce') . '" height="' . esc_attr($image_size[1]) . '" width="' . esc_attr($image_size[0]) . '" /></div>', $item);
                }

                // Product name
                echo apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false);

                // SKU
                if ($show_sku && is_object($_product) && $_product->get_sku()) {
                    echo ' (#' . $_product->get_sku() . ')';
                }

                // Allow other plugins to add additional product information here
                do_action('woocommerce_order_item_meta_start', $item_id, $item, $order);

                // Variation
                if ($item_meta = RP_WCEC_Helper::get_wc_display_item_meta($item, true, array('separator' => "\n"))) {
                    echo '<br/><small>' . nl2br($item_meta) . '</small>';
                }

                // File URLs
                if ($show_download_links) {
                    wc_display_item_downloads($item);
                }

                // Allow other plugins to add additional product information here
                do_action('woocommerce_order_item_meta_end', $item_id, $item, $order);
            ?>
        </td>
        <td class="td rp_wcec_item"><?php echo apply_filters('woocommerce_email_order_item_quantity', $item->get_quantity(), $item); ?></td>
        <td class="td rp_wcec_item"><?php echo $order->get_formatted_line_subtotal($item); ?></td>
    </tr>
<?php endif; ?>

<?php if ($show_purchase_note && is_object($_product) && ($purchase_note = $_product->get_purchase_note())): ?>
    <tr>
        <td class="rp_wcec_item" colspan="3"><?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); ?></td>
    </tr>
<?php endif; ?>

<?php endforeach; ?>
