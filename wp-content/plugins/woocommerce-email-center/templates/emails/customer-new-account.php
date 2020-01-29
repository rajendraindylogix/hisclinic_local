<?php

/**
 * Customer new account email
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

<?php do_action('woocommerce_email_header', $email_heading, $email); ?>

<p><?php printf(__('Thanks for creating an account on %s. Your username is <strong>%s</strong>.', 'woocommerce'), esc_html($blogname), esc_html($user_login)); ?></p>

<?php if (get_option('woocommerce_registration_generate_password') == 'yes' && $password_generated): ?>
    <p><?php printf(__('Your password has been automatically generated: <strong>%s</strong>', 'woocommerce'), esc_html($user_pass)); ?></p>
<?php endif; ?>

<p><?php printf(__('You can access your account area to view your orders and change your password here: %s.', 'woocommerce'), wc_get_page_permalink('myaccount')); ?></p>

<?php do_action('woocommerce_email_footer', $email); ?>
