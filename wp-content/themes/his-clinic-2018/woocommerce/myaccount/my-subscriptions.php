<?php
/**
 * My Subscriptions section on the My Account page
 *
 * @author   Prospress
 * @category WooCommerce Subscriptions/Templates
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce_account_subscriptions">

	<?php if ( WC_Subscriptions::is_woocommerce_pre( '2.6' ) ) : ?>
	<h2><?php esc_html_e( 'My Subscriptions', 'woocommerce-subscriptions' ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $subscriptions ) ) : ?>

		<!---------------------------
		---------- ORDERS HTML------- 
		---------------------------->
		<div class="account-top">
			<h3><?php _e( 'You are currently signed up to a subscription', 'woocommerce' ); ?></h3>
			<p><?php _e( 'Changing your details will impact future orders.', 'woocommerce' ); ?></p>
		</div>

		<div class="accordions accordions-myOrders extend-subscription">
			<div class="accordion">
				<div class="title"><?php _e( 'Subscription Details', 'woocommerce' ); ?></div>

				<?php /** @var WC_Subscription $subscription */ ?>
				<?php foreach ( $subscriptions as $subscription_id => $subscription ) : 
					
					// print_r( $subscription );

					$subscription_products = $subscription->get_items();

					// print_r( $subscription_products );

				?>
				
					<div class="box">

					<?php foreach ( $subscription_products as $key => $product ) : 
						
						// print_r( $product );
						
					?>
						
						<table class="table table-bordered desktop-only">
							<tr>
								<th colspan="2">
									<div class="table--title">
										<span class="top"><?php echo esc_html( $product->get_name() ); ?></span>
										<span class="bottom"><?php _e( 'Your current treatment is', 'woocommerce' ); ?> <?php echo esc_html( $product->get_name() ); ?></span>
									</div>
								</th>
							</tr>
							<tr>
								<td>
									<div class="item">
									<?php _e( 'Subscription Discount', 'woocommerce' ); ?>
									<span>20% off</span>
									</div>
								</td>
								<td rowspan="2" style=" vertical-align: middle; ">
									<div class="item">						
										<?php _e( 'Number of Tablets Per Pack', 'woocommerce' ); ?>
										<div class="yes-no">
											<div class="radio-btn-wrap">
												<div class="radio-btn">
													<input type="radio" name="no-of-tabs" value="4" id="four1">
													<label for="four">4</label>
												</div>
												<div class="radio-btn">
													<input type="radio" name="no-of-tabs" value="12" id="twelve1">
													<label for="twelve">12</label>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="item">
									Shipping
									<span>FREE</span>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="item">
									<?php _e( 'Frequency of order', 'woocommerce' ); ?>
									<span><?php printf( '%1$sly', $subscription->get_billing_period() ); ?></span>
									</div>
								</td>						
								<td>
									<div class="item">
									<?php _e( 'Total Per Order', 'woocommerce' ); ?>
									<span class="black"><?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?></span>
									</div>
								</td>						
							</tr>
						</table>

						<table class="table table-bordered mobile-only">
							<tr>
								<th colspan="2">
									<div class="table--title">
										<span class="top"><?php echo esc_html( $product->get_name() ); ?></span>
										<span class="bottom"><?php _e( 'Your current treatment is', 'woocommerce' ); ?> <?php echo esc_html( $product->get_name() ); ?></span>
									</div>
								</th>
							</tr>
							<tr>
								<td>
									<div class="item">
									Discount
									<span>20% off</span>
									</div>
								</td>
								<td>
									<div class="item">
									Shipping
									<span>FREE</span>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" style=" vertical-align: middle; ">
									<div class="item">						
										<?php _e( 'Number of Tablets Per Pack', 'woocommerce' ); ?>
										<div class="yes-no">
											<div class="radio-btn-wrap">
												<div class="radio-btn">
													<input type="radio" name="no-of-tabs" value="4" id="four">
													<label for="four">4</label>
												</div>
												<div class="radio-btn">
													<input type="radio" name="no-of-tabs" value="12" id="twelve">
													<label for="twelve">12</label>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="item">
									<?php _e( 'Frequency', 'woocommerce' ); ?> 
									<span><?php printf( '%1$sly', $subscription->get_billing_period() ); ?></span>
									</div>
								</td>
								<td>
									<div class="item">
									<?php _e( 'Total', 'woocommerce' ); ?>
									<span class="black"><?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?></span>
									</div>
								</td>	
							</tr>
						</table>

					<?php endforeach; ?>

						<div class="info">
							<p><?php _e( 'If you no longer desire services from His Clinic, you can', 'woocommerce' ); ?> <a href="#"><?php _e( 'cancel your subscription', 'woocommerce' ); ?></a> <?php _e( 'at any time.', 'woocommerce' ); ?></p>
						</div>
					</div>

				<?php endforeach; ?>

			</div>

			<div class="accordion">
				<div class="title">Shipping and Billing Address</div>
					<div class="box">
						<div class="shipping-billing">
						<div class="label-textarea">
							<label for="shipping-textarea">Shipping Address</label>
								<textarea name="" id="shipping-textarea"> Suite 301, Elizabeth Plaza,
								North Sydney
								NSW, 2600
								</textarea>
						</div>
						<div class="label-textarea">
							<label for="billing-textarea">Billing Address</label>
								<textarea name="" id="billing-textarea"> Suite 301, Elizabeth Plaza,
								North Sydney
								NSW, 2600
								</textarea>
						</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php else : ?>

		<div class="account-top no-subscription">
			<h3><?php _e( 'You don’t currently have a subscription', 'woocommerce' ); ?></h3>
			<p><?php _e( 'Sign up to save 20% on future orders!', 'woocommerce' ); ?></p> 
			<p><?php printf( 'By signing up to a subscription, you’ll always be prepared. We offer free express shipping on all orders, and the ability to customise and cancel your subscription whenever your needs change. If you have any questions about our subscription service, please %1$scontact us%2$s for more information.', '<a href="' . home_url( '/contact-us' ) . '">', '</a>' ); ?></p>
		</div>

	<?php endif; ?>

</div>

<?php
