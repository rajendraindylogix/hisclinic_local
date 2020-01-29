<?php
/**
 * Welcome page on activate or updation of the plugin
 */
?>
<style>
    .feature-section .feature-section-item {
        float:left;
        width:48%;
    }
</style>

<div class="wrap about-wrap">
    <?php echo $get_welcome_header; ?>
    <div style="float:left;width: 80%;">
        <p class="about-text" style="margin-right:20px;"><?php
            printf(
                __( "Thank you for activating or updating to the latest version of " . $plugin_name . "! If you're a first time user, welcome! You're well on your way to recovering your lost revenue with multiple abandoned cart capture & recovery mechanisms." )
            );
        ?>
        </p>
    </div>
    
    <div class="ts-badge"><img src="<?php echo $badge_url; ?>" style="width:150px;"/></div>
    
    <p>&nbsp;</p>
    
    <div class="feature-section clearfix introduction">
        <h3><?php esc_html_e( "Get Started with " . $plugin_name, $plugin_context ); ?></h3>
        <div class="video feature-section-item" style="float:left;padding-right:10px;">
            <img src="<?php echo $ts_dir_image_path . 'wcap_settings.png' ?>"
                 alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:600px;">
        </div>

        <div class="content feature-section-item last-feature">
            <h3><?php esc_html_e( 'Enable Abandoned Carts Capturing', $plugin_context ); ?></h3>

            <p><?php esc_html_e( 'To start capturing abandoned carts, which will help you recover lost sales configure the plugin from the settings tab', $plugin_context ); ?></p>
            <a href="admin.php?page=woocommerce_ac_page&action=emailsettings" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Click Here to go to Abandoned Cart Settings page', $plugin_context ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
    </div>

    <!-- /.intro-section -->

    <div class="content">

        <h3><?php esc_html_e( "Some exciting features of " . $plugin_name, $plugin_context ); ?></h3>

         <div class="feature-section clearfix introduction">

            <div class="content feature-section-item">
                <h3><?php esc_html_e( 'Send Facebook Notifications', $plugin_context ); ?></h3>

                <p><?php esc_html_e( 'Now have the flexibility to send reminder Facebook notifications along with emails and SMS. Add new FB notification templates and start sending them right away.', $plugin_context ); ?></p>

                <a href="admin.php?page=woocommerce_ac_page&action=emailsettings&wcap_section=wcap_fb_settings" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Configure Settings', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
                <a href="admin.php?page=woocommerce_ac_page&action=cart_recovery&section=fb_templates" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Configure Templates', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>

            <div class="content feature-section-item">
                <div class="video" style="float:left;padding-right:10px;" >
                <img src="https://www.tychesoftwares.com/wp-content/uploads/2018/07/Facebook_reminder.gif"
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:500px;">
                </div>
            </div>
            
        </div>
        
        <div class="feature-section clearfix introduction">
            <div class="content feature-section-item">
                <img src="<?php echo $ts_dir_image_path . 'wcap_new_template.png'; ?>" 
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:450px;">
            </div>
            <div class="content feature-section-item last-feature">
                <h3><?php esc_html_e( '3 new default email templates', $plugin_context ); ?></h3>

                <p><?php esc_html_e( 'We have added 3 new default email templates which have been built using the new pre-defined templates.', $plugin_context ); ?></p>

                <a href="admin.php?page=woocommerce_ac_page&action=cart_recovery&section=emailtemplates" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Check them out', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>

        <div class="feature-section clearfix">
            <div class="content feature-section-item">
                <h3><?php esc_html_e( 'Abandoned Orders Listing', $plugin_context ); ?></h3>
                <p style="text-align: justify;"><?php esc_html_e( "The ability to view the Abandoned Orders for a particular customer. Customers who have given Facebook Messenger consent from product page will be marked with a messenger icon. Also filter data based on date range from the same screen. Customized manual emails can also be sent to a particular customer from here", $plugin_context ); ?></p>
                <a href="https://www.tychesoftwares.com/docs/docs/abandoned-cart-pro-for-woocommerce/abandoned-orders-listing/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
            <div class="content feature-section-item last-feature">
                <img src="<?php echo $ts_dir_image_path . 'wcap_abandoned_orders.png'; ?>" 
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="max-width:120%;">
            </div>
        </div>


        <div class="feature-section clearfix introduction">
            <div class="video feature-section-item" style="float:left;padding-right:10px;">
                <img src="<?php echo $ts_dir_image_path . 'wcap_coupons.png'; ?>" alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:450px;">
            </div>

            <div class="content feature-section-item last-feature">
                <h3><?php esc_html_e( 'Add discount coupons to recover lost sales', $plugin_context ); ?></h3>

                <p><?php esc_html_e( 'The ability to add unique discount coupon codes along with the campaign emails to recover the lost sales.', $plugin_context ); ?></p>

                <a href="https://www.tychesoftwares.com/docs/docs/abandoned-cart-pro-for-woocommerce/understanding-coupon-codes/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>

        <a href="https://www.tychesoftwares.com/docs/docs/abandoned-cart-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank" class="button-secondary">
            <?php esc_html_e( 'Documentation', $plugin_context ); ?>
            <span class="dashicons dashicons-external"></span>
        </a>

        <a href="https://www.tychesoftwares.com/docs/docs/abandoned-cart-pro-for-woocommerce/changelog/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank" class="button-secondary">
            <?php esc_html_e( 'Changelog', $plugin_context ); ?>
            <span class="dashicons dashicons-external"></span>
        </a>
    </div>

    <div class="feature-section clearfix">
        <div class="content feature-section-item">
            <h3><?php esc_html_e( 'Getting to Know Tyche Softwares', 'woocommerce-ac' ); ?></h3>
            <ul class="ul-disc">
                <li><a href="https://tychesoftwares.com/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank"><?php esc_html_e( 'Visit the Tyche Softwares Website', 'woocommerce-ac' ); ?></a></li>
                <li><a href="https://tychesoftwares.com/premium-woocommerce-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank"><?php esc_html_e( 'View all Premium Plugins', 'woocommerce-ac' ); ?></a>
                <ul class="ul-disc">
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank">Abandoned Cart Pro Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank">Booking & Appointment Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank">Order Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank">Product Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank">Deposits for WooCommerce</a></li>
                </ul>
                </li>
                <li><a href="https://tychesoftwares.com/about/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=AbandonedCartProPlugin" target="_blank"><?php esc_html_e( 'Meet the team', $plugin_context ); ?></a></li>
            </ul>

        </div>
        
        <div class="content feature-section-item">
            <h3><?php esc_html_e( 'Current Offers', $plugin_context ); ?></h3>
            <p>We do not have any offers going on right now</p>
        </div>

    </div>            
    <!-- /.feature-section -->
</div>