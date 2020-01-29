<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
$user = get_currentuserinfo();
?>

<div class="account-content">
    <div class="graphic">&nbsp;</div>
    <div class="intro">
        <!-- <h1 class="h1">Welcome to your account<span class="pink">.</span></h1> -->
        
        <div class="person dt">
            <div class="dtc shorter">
                <div class="image">
                    <div class="inner">
                        <img src="<?php echo get_user_avatar_url(get_user_avatar(get_current_user_id())) ?>" alt="Avatar">
                        <div class="overlay">&nbsp;</div>
                    </div>
                </div>
            </div>
            <div class="dtc">
                <h2 class="name"style="word-break: break-word;">Hi, <?php echo $user->user_firstname ?></h2>
                <!-- <p class="signout"><a href="<?php echo wp_logout_url( home_url() ); ?>">Sign Out</a></p> -->
                <?php
                    $cur_usr_id        = get_current_user_id();
                    $suggested_product = get_user_meta( $cur_usr_id, 'suggested_product', true );
                    $default_prod_id   = function_exists( 'get_field' ) ? get_field( 'default_product', 'option' ) : '';

                    if ( ! empty( $suggested_product ) ) {

                        $shop_url = $suggested_product;

                    } else {
                        $shop_url = get_permalink( $default_prod_id );
                    }
                ?>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-filled btn--reorder"><?php echo 0 < wc_get_customer_order_count( get_current_user_id() ) ? __( 'Want to Reorder?', 'woocommerce' ) : __( 'Start your order', 'woocommerce' ); ?></a>
            </div>
        </div>

        <ul class="navigation">
            <li class="<?php echo wc_get_account_menu_item_classes('dashboard') ?>">
                <a href="<?php echo home_url('my-account') ?>">My Details</a>
                <a href="<?php echo home_url('my-account/orders') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('medical-details') ?>">
                <a href="<?php echo home_url('my-account/subscriptions') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/medical-details') ?>">Medical Details</a>
                <a href="<?php echo home_url('my-account/request-treatment-change') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('orders') ?>">
                <a href="<?php echo home_url('my-account') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/orders') ?>">My Orders</a>
                <a href="<?php echo home_url('my-account/subscriptions') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('subscriptions') ?>">
                <a href="<?php echo home_url('my-account/orders') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/subscriptions') ?>">My Subscriptions</a>
                <a href="<?php echo home_url('my-account/help') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('help') ?>">
                <a href="<?php echo home_url('my-account/request-treatment-change') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/help') ?>">Speak to a Doctor</a>
                <a href="<?php echo home_url('my-account/wc-user-logout') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('request-treatment-change') ?>">
                <a href="<?php echo home_url('my-account/medical-details') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/request-treatment-change') ?>">Request Treatment Change</a>
                <a href="<?php echo home_url('my-account/my-account/help') ?>" class="after">&nbsp;</a>
            </li>
            <li class="<?php echo wc_get_account_menu_item_classes('wc-user-logout') ?>">
                <!-- <a href="<?php echo wp_logout_url( home_url() ); ?>"></a> -->
                <a href="<?php echo home_url('my-account/help') ?>" class="before">&nbsp;</a>
                <a href="<?php echo home_url('my-account/wc-user-logout') ?>">Sign Out</a>
            </li>
        </ul>
    </div>
	<?php
        woocommerce_output_all_notices();
		do_action( 'woocommerce_account_content' );
	?>
</div>
