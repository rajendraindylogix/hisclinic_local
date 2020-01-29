<?php

/**
 * Plugin Name: WooCommerce Custom Emails
 * Plugin URI: http://www.rightpress.net/woocommerce-email-center
 * Description: Send highly targeted WooCommerce emails. Formerly "WooCommerce Email Center".
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 *
 * Text Domain: rp_wcec
 * Domain Path: /languages
 *
 * Version: 1.4.3
 *
 * Requires at least: 4.0
 * Tested up to: 5.2
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.7
 *
 * @package WooCommerce Email Center
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('RP_WCEC_PLUGIN_KEY', 'woocommerce-email-center');
define('RP_WCEC_PLUGIN_PUBLIC_PREFIX', 'rp_wcec_');
define('RP_WCEC_PLUGIN_PRIVATE_PREFIX', 'rp_wcec_');
define('RP_WCEC_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RP_WCEC_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('RP_WCEC_VERSION', '1.4.3');
define('RP_WCEC_OPTIONS_VERSION', '1');
define('RP_WCEC_SUPPORT_PHP', '5.6');
define('RP_WCEC_SUPPORT_WP', '4.0');
define('RP_WCEC_SUPPORT_WC', '3.0');

// Load main plugin class
require_once 'rp-wcec.class.php';

// Initialize automatic updates
require_once(plugin_dir_path(__FILE__) . 'rightpress-updates/rightpress-updates.class.php');
RightPress_Updates_13907681::init(__FILE__, RP_WCEC_VERSION);
