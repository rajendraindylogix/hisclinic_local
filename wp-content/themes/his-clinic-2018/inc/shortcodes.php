<?php  
/**
 * Hisclinic Custom Shortcodes
 * 
 * @package Hisclinic
 */
add_shortcode( 'hisclinic_order_details', 'hisclinic_add_order_details_shortcode' );
/**
  * [hisclinic_add_order_details_shortcode Adds Shortcode]
  *
  * @return  [type]  [return description]
  */
function hisclinic_add_order_details_shortcode( $atts ) {

    ob_start();

    $user_id = get_current_user_id();

    if ( ! is_user_logged_in() ) {

        ?>
        <div class="container">
            <p><?php _e( 'You must be logged in to order', 'woocommerce' ); ?></p>
                <br/>
            <a href="<?php echo home_url('medical-form') ?>" class="btn"><?php _e( 'Check Eligibility', 'woocommerce' ); ?></a>
        </div>
        <?php
    
    }
    
    else {

        if ( isset( $_GET['prod_id'] ) && ! empty( $_GET['prod_id'] ) ) {
            get_template_part( '/template-parts/order', 'details' );
        } else {
            _e( 'Invalid product ID', 'his-clinic' );
        }
    }

    $data = ob_get_clean();

    return $data;

}

/**
 * Add [add_to_cart_form] shortcode that display a single product add to cart form
 * Supports id and sku attributes [add_to_cart_form id=99] or [add_to_cart_form sku=123ABC]
 * Essentially a duplicate of the [product_page]
 * but replacing wc_get_template_part( 'content', 'single-product' ); with woocommerce_template_single_add_to_cart()
 *
 * @param array $atts Attributes.
 * @return string
 */
function kia_add_to_cart_form_shortcode( $atts ) {
    if ( empty( $atts ) ) {
        return '';
    }

    if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
        return '';
    }

    $args = array(
        'posts_per_page'      => 1,
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => 1,
    );

    if ( isset( $atts['sku'] ) ) {
        $args['meta_query'][] = array(
            'key'     => '_sku',
            'value'   => sanitize_text_field( $atts['sku'] ),
            'compare' => '=',
        );

        $args['post_type'] = array( 'product', 'product_variation' );
    }

    if ( isset( $atts['id'] ) ) {
        $args['p'] = absint( $atts['id'] );
    }

    $single_product = new WP_Query( $args );

    $preselected_id = '0';

    // Check if sku is a variation.
    if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {

        $variation = new WC_Product_Variation( $single_product->post->ID );
        $attributes = $variation->get_attributes();

        // Set preselected id to be used by JS to provide context.
        $preselected_id = $single_product->post->ID;

        // Get the parent product object.
        $args = array(
            'posts_per_page'      => 1,
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
            'p'                   => $single_product->post->post_parent,
        );

        $single_product = new WP_Query( $args );
    ?>
        <script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );

                <?php foreach ( $attributes as $attr => $value ) { ?>
                    $variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
                <?php } ?>
            });
        </script>
    <?php
    }

    // For "is_single" to always make load comments_template() for reviews.
    $single_product->is_single = true;

    ob_start();

    global $wp_query;

    // Backup query object so following loops think this is a product page.
    $previous_wp_query = $wp_query;
    // @codingStandardsIgnoreStart
    $wp_query          = $single_product;
    // @codingStandardsIgnoreEnd

    wp_enqueue_script( 'wc-single-product' );

        $_pf = new WC_Product_Factory();

        while ( $single_product->have_posts() ) {
            $single_product->the_post();
            ?>
            <div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
                <?php
                $user_id = get_current_user_id();

                // if ( is_approved($user_id) ) {
                    do_action( 'woocommerce_before_add_to_cart_form' );
                        woocommerce_template_single_add_to_cart();
                    do_action( 'woocommerce_after_add_to_cart_form' );
                // } else {
                    ?>
                        <!-- <p>Your medical form is currently being review by our doctors. For now, you can view our products, but you won't be able to make a purchase until we confirm that our products are safe for you. If you have any questions, please drop us a message and we'll get back to you as soon as we can!</p>
                        <br/>
                        <a href="<?php echo home_url('medical-form') ?>" class="btn">Check Eligibility</a> -->
                    <?php
                // }
                ?>
            </div>
        <?php
    }
    // Restore $previous_wp_query and reset post data.
    // @codingStandardsIgnoreStart
    $wp_query = $previous_wp_query;
    // @codingStandardsIgnoreEnd
    wp_reset_postdata();

    return '<div class="woocommerce">' . ob_get_clean() . '</div>';
}
add_shortcode( 'add_to_cart_form', 'kia_add_to_cart_form_shortcode' );
