<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// Show Coupoun addition form
echo woocommerce_checkout_coupon_form();

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>
<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
    
    <div id="mf-app">
        <div class="container">
            <div id="prod-order-details">
                <div class="mf-step mf-step__rform">
                    <div class="mf-progress">
                        <a href="<?php echo esc_url( $_SERVER['HTTP_REFERER'] ); ?>" class="mf-stop" >< <?php _e( 'Back', 'his-clinic' ); ?></a>
                        <div class="mf-progress__bar">
                            <div class="mf-progress__fill" style="width:50%;"></div>
                        </div>
                    </div>
                    <div class="mf-step__item">
                        <div class="text-center">
                            <h2><?php _e('Your Address', 'his-clinic');?></h2> 
                        </div>
                        <div class="row">
                        <?php if ( $checkout->get_checkout_fields() ) : ?>

                            <div class="col-md-12">
                                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                            </div>

                            <div class="col-md-12">
                                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                            </div>

                        <?php endif; ?>
                        <div class="col-md-12 text-center">
                            <button onclick="event.preventDefault();" class="btn filled validate-checkout"><?php _e( 'Continue', 'woocommerce' ); ?></button>
                        </div>
                        </div>
                    </div>
                </div>

                <?php 
                    // Geolocate
                    $location      = WC_Geolocation::geolocate_ip();
                    $user_location = isset( $location['state'] ) && ! empty( $location['state'] ) ? $location['state'] : $location['country'];
                    
                    $country = WC()->countries->countries[ $location['country'] ];

                // if( $location['country'] === 'AU' ) :
                ?>

                    <div class="mf-step" style="display:none;">
                        <div class="mf-progress">
                            <a href="#" class="mf-prev" >< <?php _e( 'Back', 'his-clinic' ); ?></a>
                            <div class="mf-progress__bar">
                                <div class="mf-progress__fill" style="width:75%;"></div>
                            </div>
                        </div>
                        <div class="mf-step__item">
                            <div class="text-center">
                                <h2><?php _e('Delivery Options', 'his-clinic');?></h2> 
                            </div>
                            <div class="row">
                                <div class="col-md-12"></div>
                                <div class="shipping-image-icon text-center">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/shipping-big.svg">

                                </div>
                                <div class="shipping-info text-center">
                                    <p><?php _e( 'You qualify for', 'woocommerce' ); ?> <strong>
                                        <?php _e( 'free express shipping!', 'woocommerce' ); ?>
                                    </strong> </p>
                                    <p><?php echo __( 'You live in ', 'woocommerce' ); ?> <span id="shipping-locale"> <?php echo $country; ?></span><?php echo __( ', so you should expect your purchase within', 'woocommerce' ); ?> <strong><?php _e( '1 - 3 working days', 'woocommerce' ); ?></strong>.</p>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button onclick="event.preventDefault();" class="btn filled mf-next"><?php _e( 'Continue', 'woocommerce' ); ?></button>
                            </div>
                        </div>
                    </div>

                <?php // endif; ?>

                <!-- Payment Step -->
                <?php include('pay-step.php') ?>
            </div>
        </div>
    </div>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
