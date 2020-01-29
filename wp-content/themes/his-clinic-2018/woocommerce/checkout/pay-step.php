<div class="mf-step" style="display:none;">
    <div class="mf-progress">
        <a href="#" class="mf-prev" >< <?php _e( 'Back', 'his-clinic' ); ?></a>
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:100%;"></div>
        </div>
    </div>
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e('Place Your Order', 'his-clinic');?></h2> 
        </div>
        <div class="row"> 
            <div class="col-md-6">
                <?php include('pay-order-description.php') ?>
            </div>
            <div class="col-md-6">
                <div class="order-description-right">
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                </div>                                
            </div>
        </div>
        <div class="text-right text-center-xs">
            <?php 
                $user_id           = get_current_user_id();
                $allow_coupons_use = get_field( 'allow_coupons_use', 'user_'. $user_id );

                if ( $allow_coupons_use ) :
            ?>
                    <div class="coupon-container">
                        <a class="btn-apply-discount-code btn-secondary" data-toggle="collapse" href="#couponCodeToggle" role="button" aria-expanded="false" aria-controls="couponCodeToggle">
                            <?php _e( 'apply discount code', 'woocommerce' ); ?> <img src="<?php echo get_template_directory_uri() ?>/assets/img/discount-plus.svg" alt="" class="svg plus">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/discount-minus.svg" alt="" class="svg less">
                        </a>
                        <div class="collapse" id="couponCodeToggle">
                            <div class="card card-body">
                                <div class="form-group">
                                    <input bind="bindedCouponCode" type="text" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="bindedCouponCode" value="" />
                                    <button id="triggerApplyCoupon" class="btn filled" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endif; ?>
            
            <button class="btn filled place-order-button">Place Order</button>
        </div>

        <?php include('accepted-cards.php') ?>
    </div>
</div>