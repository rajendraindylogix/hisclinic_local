<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>
	<!---------------------------
	---------- ORDERS HTML------- 
	---------------------------->
	<div class="accordions accordions-myOrders">
		<?php
			// Order details values.
			foreach ( $customer_orders->orders as $order ):
                $order            = wc_get_order( $order );
                $items            = $order->get_items();
				$item_count       = $order->get_item_count();
				$payment_method   = $order->get_payment_method_title();
				$is_sub           = wcs_order_contains_subscription( $order );
				$billing_address  = $order->get_formatted_billing_address();
				$shipping_address = $order->get_formatted_shipping_address();
		?>
		<div class="accordion">
			<div class="title"> <?php echo $order->get_date_created()->format('d F Y') ?> - <?php echo wc_get_order_status_name($order->get_status()) ?> </div>
			<?php foreach ( $items as $item_key => $item ) :

				$product_id   = $item->get_product_id();
				$pack         = wc_get_order_item_meta( $item_key, 'pa_pack-size' );
				$subscription = wc_get_order_item_meta( $item_key, 'pa_frequency' );

				$dec_text = function_exists( 'get_field' ) ? get_field( 'order_information', $product_id ) : '';
			?>
			<div class="box">
			<table class="table table-bordered desktop-only">
					<tr>
						<th colspan="2">
							<div class="table--title">
								<span class="top"><?php echo esc_html( $item->get_name() ); ?></span>
								<span class="bottom"><?php echo wp_kses_post( $dec_text ); ?></span>
							</div>
						</th>
					</tr>
					<tr>
						<td>
							<div class="item">
								<?php _e( 'Number of tablets per pack', 'woocommerce' ); ?>
								<span><?php echo esc_html( $pack ); ?></span>
							</div>
						</td>
						<td>
							<div class="item">
							<?php _e( 'Payment Method', 'woocommerce' ); ?> <span><?php echo esc_html( $payment_method ); ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="item">						
								<?php _e( 'Subscription Service', 'woocommerce' ); ?> 
								<span><?php echo $is_sub ? __( 'Yes', 'woocommerce' ) : __( 'No', 'woocommerce' ); ?></span>
							</div>
						</td>
						<td>
							<div class="item">
							<?php _e( 'Payment Status', 'woocommerce' ); ?> <span class="red">Pending</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="item">
							<?php _e( 'Order Number', 'woocommerce' ); ?><span><?php echo $item->get_order_id() ?></span>
							</div>
						</td>
						<td>
							<div class="item">
							<?php _e( 'Total Price', 'woocommerce' ); ?>
							<span class="black"><?php echo wc_price($item->get_total()) ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="item">
								<?php _e( 'Shipping Address', 'woocommerce' ); ?>
								<div class="address">
									<?php echo $shipping_address; ?>
								</div>
							</div>
						</td>
						<td>
							<div class="item">
								<?php _e( 'Billing Address', 'woocommerce' ); ?>
								<div class="address">
									<?php echo $billing_address; ?>
								</div>
							</div>
						</td>
					</tr>
				</table>

				<table class="table table-bordered mobile-only">
					<tr>
						<th colspan="2">
							<div class="table--title">
								<span class="top"><?php echo esc_html( $item->get_name() ); ?></span>
								<span class="bottom"><?php echo wp_kses_post( $dec_text ); ?></span>
							</div>
						</th>
					</tr>
					<tr>
						<td>
							<div class="item">
								<?php _e( 'tablets per pack', 'woocommerce' ); ?>
								<span><?php echo esc_html( $pack ); ?></span>
							</div>
						</td>
						<td>
							<div class="item">						
								<?php _e( 'Subscription', 'woocommerce' ) ?> 
								<span><?php echo $is_sub ? __( 'Yes', 'woocommerce' ) : __( 'No', 'woocommerce' ); ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="item">
							<?php _e( 'Payment Method', 'woocommerce' ); ?> <span><?php echo esc_html( $payment_method ); ?></span>
							</div>
						</td>
						<td>
							<div class="item">
							<?php _e( 'Payment Status', 'woocommerce' ); ?> <span class="red">Pending</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="item">
								<?php _e( 'Order Number', 'woocommerce' ); ?><span><?php echo $item->get_order_id() ?></span>
							</div>
						</td>
						<td>
							<div class="item">
							<?php _e( 'Total', 'woocommerce' ); ?>
							<span class="black"><?php echo wc_price($item->get_total()) ?></span>
							</div>
						</td>
					</tr>
					<tr class="billing">
						<td colspan="2">
							<div class="item">
								<?php _e( 'Billing Address', 'woocommerce' ); ?>
								<div class="address">
									<?php echo $billing_address; ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="item">
								<?php _e( 'Shipping Address', 'woocommerce' ); ?>
								<div class="address">
									<?php echo $shipping_address; ?>
								</div>
							</div>
						</td>
					</tr>
				</table>

				<div class="info">
					<p><?php echo sprintf( __( 'Something wrong? Please %1$scontact us%2$s and we’ll look into it for you.', 'woocommerce' ), '<a href="'. get_permalink( hisclinic_get_page_id_by_page_name( 'contact-us' ) ) .'">', '</a>' ); ?></p>
					<p><?php echo sprintf( __( 'If you have a subscription service but you’d like to change your details for future orders, you can do so using %1$sSubscription Details%2$s and %3$sRequest Treatment Change%4$s above.', 'woocommerce' ), '<a href="'. home_url('my-account/subscriptions') .'">', '</a>', '<a href="'. home_url('my-account/request-treatment-change') .'">', '</a>' ); ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

		<?php endforeach; ?>
	
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>
	<div class="container no-order">
		<p><?php _e( 'No order has been made yet.', 'woocommerce' ); ?></p>
	
		<!-- <a class="btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php _e( 'Go shop', 'woocommerce' ) ?>
		</a> -->
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders );
