<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper methods
 *
 * @class RP_WCEC_Helper
 * @package WooCommerce Email Center
 * @author RightPress
 */
if (!class_exists('RP_WCEC_Helper')) {

class RP_WCEC_Helper
{

    /**
     * Get WooCommerce item meta for display
     *
     * @access public
     * @param object $item
     * @param bool $flat
     * @param array $args
     * @return string
     */
    public static function get_wc_display_item_meta($item, $flat = false, $args = array())
    {

        // Flat config
        if ($flat) {

            $args = array_merge($args, array(
                'before'    => '',
                'separator' => ', ',
                'after'     => '',
                'autop'     => false,
            ));
        }

        // Set to return
        $args['echo'] = false;

        // Get display meta and strip tags
        return strip_tags(wc_display_item_meta($item, $args));
    }





}
}
