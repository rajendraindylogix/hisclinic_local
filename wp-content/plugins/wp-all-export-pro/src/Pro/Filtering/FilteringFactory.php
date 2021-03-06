<?php

namespace Wpae\Pro\Filtering;
use Wpae\App\Service\Addons\AddonNotFoundException;
use Wpae\App\Service\Addons\AddonService;

/**
 * Class FilteringFactory
 * @package Wpae\Pro\Filtering
 */
class FilteringFactory
{
    public static function getFilterEngine()
    {
        $addonService = new AddonService();

        if (\XmlExportEngine::$is_comment_export) {
            return new FilteringComments();
        }
        if (\XmlExportEngine::$is_user_export && $addonService->isUserAddonActive()){
            if (! empty(\XmlExportEngine::$post_types) and @in_array("shop_customer", \XmlExportEngine::$post_types)){
                return new \FilteringCustomers();
            }
            return new \FilteringUsers();
        } else if(\XmlExportEngine::$is_user_export && !$addonService->isUserAddonActive()) {

            throw new AddonNotFoundException(\__('The User Export Add-On Pro is required to run this export. You can download the add-on here: <a href="http://www.wpallimport.com/portal/" target="_blank">http://www.wpallimport.com/portal/</a>', \PMXE_Plugin::LANGUAGE_DOMAIN));

        }
        if (\XmlExportEngine::$is_taxonomy_export){
            return new FilteringTaxonomies();
        }
        // WooCommerce Post Types
        if ( ! empty(\XmlExportEngine::$post_types) and class_exists('WooCommerce')){
            if (@in_array("product", \XmlExportEngine::$post_types)){
                return new FilteringProducts();
            }
            if (@in_array("shop_order", \XmlExportEngine::$post_types)){
                return new FilteringOrders();
            }
        }
        return new FilteringCPT();
    }

    public static function render_filtering_block( $engine, $isWizard, $post, $is_on_template_screen = false )
    {

        if ( $isWizard or $post['export_type'] != 'specific' ) return;

        ?>
        <div class="wpallexport-collapsed wpallexport-section closed">
            <div class="wpallexport-content-section wpallexport-filtering-section" <?php if ($is_on_template_screen):?>style="margin-bottom: 10px;"<?php endif; ?>>
                <div class="wpallexport-collapsed-header" style="padding-left: 25px;">
                    <h3><?php _e('Filtering Options','wp_all_export_plugin');?></h3>
                </div>
                <div class="wpallexport-collapsed-content" style="padding: 0;">
                    <div class="wpallexport-collapsed-content-inner">
                        <?php include_once PMXE_ROOT_DIR . '/views/admin/export/blocks/filters.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}