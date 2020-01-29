<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="woocommerce-order">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

        <?php else : 

            $order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
            $show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
            $show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
            $downloads             = $order->get_downloadable_items();
            $show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
            
            $username = '';

            if ( is_user_logged_in() && $order->get_user_id() ) {
                
                $user     = get_user_by( 'id', $order->get_user_id() );
                $username = $user->user_firstname;
            }
            
            // Successful order
            do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
        ?>
        <div class="success-message">
            <div class="container">
                <div class="text-center">
                    <h2 class="main-heading">
                        <?php 
                            $success_message = function_exists( 'get_field' ) ? get_field( 'success_message', 'option' ) : false;
                            
                            $dynamic_tags = array(
                                '{customer_name}' => $username,
                            );
                            
                            $success_message_text = str_replace( array_keys( $dynamic_tags ), $dynamic_tags, $success_message );
                            
                            echo apply_filters( 'woocommerce_thankyou_order_received_text', $success_message_text, $order );
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <!-- end success-message -->
        <?php 
            $next_steps = function_exists( 'get_field' ) ? get_field( 'next_steps', 'option' ) : array();

            if ( ! empty( $next_steps ) ) :

                $title = isset( $next_steps['title'] ) && ! empty( $next_steps['title'] ) ? $next_steps['title'] : false;
                $steps = isset( $next_steps['steps'] ) && ! empty( $next_steps['steps'] ) ? $next_steps['steps'] : array();
                ?>
                <div class="what-happens">
                    <div class="container">
                        <?php if ( $title ) : ?>
                            <h3 class="heading"><?php echo esc_html( $title ); ?></h3>
                        <?php 
                            endif; 
                            
                            if ( ! empty( $steps ) ) :
                        ?>
                                <div class="slider-happens-next">
                                    
                                    <?php foreach( $steps as $k => $step ) : 
                                        
                                        $icon         = isset( $step['icon'] ) && ! empty( $step['icon'] ) ? $step['icon'] : false;
                                        $step_title   = isset( $step['step_title'] ) && ! empty( $step['step_title'] ) ? $step['step_title'] : false;
                                        $step_content = isset( $step['step_content'] ) && ! empty( $step['step_content'] ) ? $step['step_content'] : false;
                                    ?>
                                        <div class="slider-item">
                                            <div class="inner-box">
                                                <?php if ( $icon ) : ?>
                                                    <div class="icon">
                                                        <img src="<?php echo esc_url( $icon ); ?>">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="description">
                                                <?php if ( $step_title ) : ?>
                                                    <h5><?php echo esc_html( $step_title ); ?></h5>
                                                <?php endif; 
                                                    if ( $step_content ) :
                                                ?>
                                                    <p><?php echo wp_kses_post( $step_content ); ?></p>
                                                <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end slider item -->
                                    <?php endforeach; ?>

                                </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- end slider -->
        <?php endif; ?>
        <div class="confirmation-message">
            <div class="container">
                <div class="row">
                <?php 
                    $confirmation_message = function_exists( 'get_field' ) ? get_field( 'confirmation_message', 'option' ) : false;

                    if ( $confirmation_message ) :
                ?>
                        <div class="col-lg-5">
                            <div class="conform-message-left">
                                <?php echo wp_kses_post( $confirmation_message ); ?>
                            </div>
                            <?php 
                                
                                $link = function_exists( 'get_field' ) ? get_field( 'link', 'option' ) : false;
                                
                                if( $link ): 
                                    
                                    $link_url    = $link['url'];
                                    $link_title  = $link['title'];
                                    $link_target = $link['target'] ? $link['target'] : '_self';
                            ?>
                                    <div class="review-btn">
                                        <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                                    </div>

                            <?php endif; ?>

                        </div>
                    <?php endif; ?>
                    <div class="col-lg-6 col-lg-offset-1 ">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <tbody>
                                    <tr>
                                        <td><?php _e( 'Product', 'woocommerce' ); ?></td>
                                        <td>
                                        <?php 
                                            foreach ( $order_items as $item_id => $item ) {
                                                $product = $item->get_product();
                                                echo esc_html( $product->get_name() );
                                            }
                                        ?>
                                    </td>
                                    </tr>
                                    <!-- end table row -->
                                    <?php 
                                        foreach ( $order_items as $item_id => $item ) {
                                            $product = $item->get_product();
                                            
                                            if( $product->is_type( 'variation' ) ) {
                                                // Get the variation attributes
                                                $variation_attributes = $product->get_variation_attributes();
                                                // Loop through each selected attributes
                                                foreach( $variation_attributes as $attribute_taxonomy => $term_slug ){
                                                    $taxonomy = str_replace('attribute_', '', $attribute_taxonomy );
                                                    // The name of the attribute
                                                    $attribute_name = get_taxonomy( $taxonomy )->labels->singular_name;
                                                    // The term name (or value) for this attribute
                                                    $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo esc_html( $attribute_name ); ?></td>
                                                            <td><?php echo esc_html( $attribute_value ); ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                    ?>
                                    <!-- <tr>
                                        <td>Number of tablets per pack</td>
                                        <td>12</td>
                                    </tr> -->
                                    <!-- end table row -->
                                    <!-- <tr>
                                        <td>Subscription</td>
                                        <td>Yes</td>
                                    </tr> -->
                                    <!-- end table row -->
                                            
                                    <?php 
                                        if ( $order->get_shipping_method() ) { 

                                            $tax_display = '';
                                            $tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );    
                                        ?>

                                        <tr>
                                            <td><?php _e( 'Shipping', 'woocommerce' ); ?></td>
                                            <td><?php echo $order->get_shipping_to_display( $tax_display ) ?></td>
                                        </tr>
                                        <?php
                                        }
                                        endif;    
                                    ?>
                                    <!-- end table row -->

                                    <tr>
                                        <td><?php _e( 'ORDER NUMBER', 'woocommerce' ); ?></td>
                                        <td><?php echo $order->get_order_number(); ?></td>
                                    </tr>
                                    <!-- end table row -->

                                    <tr>
                                        <td><?php _e( 'DATE', 'woocommerce' ); ?></td>
                                        <td><?php echo wc_format_datetime( $order->get_date_created() ); ?></td>
                                    </tr>
                                    <!-- end table row -->
                                    <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                                        <tr>
                                            <td><?php _e( 'EMAIL', 'woocommerce' ); ?></td>
                                            <td><?php echo $order->get_billing_email(); ?></td>
                                        </tr>
                                        <!-- end table row -->
                                    <?php endif;
                                        
                                    if ( $order->get_payment_method_title() ) : ?>
                                        <tr>
                                            <td><?php _e( 'PAYMENT METHOD', 'woocommerce' ); ?></td>
                                            <td><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></td>
                                        </tr>
                                        <!-- end table row -->
                                    <?php endif; ?>

                                    <tr>
                                        <td><?php _e( 'Total', 'woocommerce' ); ?></td>
                                        <td><strong><?php echo $order->get_formatted_order_total(); ?></strong></td>
                                    </tr>
                                    <!-- end table row -->

                                    <tr>
                                        <td><?php _e( 'PAYMENT STATUS', 'woocommerce' ); ?></td>
                                        <td class="red"><strong><?php _e( 'Pending', 'woocommerce' ); ?></strong></td>
                                    </tr>
                                    <!-- end table row -->
                                </tbody>
                            </table>
                        </div>
                        <!-- end responsive table -->

                        <?php 
                        $show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address(); 
                        
                        if ( $show_shipping ) :
                        ?>

                            <div class="table-responsive">
                                <table class="table table-address">
                                    <thead>
                                        <th><?php _e( 'Billing Address', 'woocommerce' ); ?></th>
                                        <th><?php _e( 'Shipping Address', 'woocommerce' ); ?></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th><?php _e( 'Billing Address', 'woocommerce' ); ?></th>
                                            <td class="address">
                                                <?php echo wp_kses_post( $order->get_formatted_billing_address( __( 'N/A', 'woocommerce' ) ) ); ?>
                                            </td>
                                            <th><?php _e( 'Shipping Address', 'woocommerce' ); ?></th>
                                            <td class="address">
                                                <?php echo wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A', 'woocommerce' ) ) ); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end responsive table -->

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
            if ( $show_downloads ) {
                wc_get_template( 'order/order-downloads.php', array( 'downloads' => $downloads, 'show_title' => true ) );
            }
		?>
	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>
