<div class="res-menu">
    <div class="inner">
        <div class="dt">
            <div class="dtc">
                <?php wp_nav_menu(['menu' => 'top-menu']) ?>

                <?php if (is_user_logged_in()): 
                        
                        $cur_usr_id        = get_current_user_id();
                        $suggested_product = get_user_meta( $cur_usr_id, 'suggested_product', true );
                        $default_prod_id   = function_exists( 'get_field' ) ? get_field( 'default_product', 'option' ) : '';
                    
                        if ( ! empty( $suggested_product ) ) {

                            $shop_url = $suggested_product;

                        } else {
                            $shop_url = get_permalink( $default_prod_id );
                        }

                        if ( is_singular( 'product' ) ) :

							global $post;
							$post_id  = $post->ID;

							$shop_url = home_url( '/order-details' );
							$shop_url = add_query_arg( 'prod_id', $post_id, $shop_url );

						endif;
						
                    
                ?>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="btn filled">
                        <span class="text"><?php _e( 'Start Your Order', 'woocommerce' ); ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo home_url('medical-form') ?>" class="btn filled check-eligibility">
                        <span class="text">Check Eligibility</span>
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<div class="res-seo-menu">
    <div id="sticky">
        <?php wp_nav_menu( array( 'menu' => 'SEO Sidebar Menu' ) ); ?>
        <!-- <?php if (is_user_logged_in()): ?>
            <a href="<?php echo home_url('shop') ?>" class="btn filled">
                <span class="text">See Treatments</span>
            </a>
        <?php else: ?>
            <a href="<?php echo home_url('medical-form') ?>" class="btn filled check-eligibility">
                <span class="text">Check Eligibility</span>
            </a>
        <?php endif ?> -->
    </div>
</div>