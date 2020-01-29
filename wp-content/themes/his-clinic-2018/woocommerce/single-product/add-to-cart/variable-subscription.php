<?php
/**
 * Variable subscription product add to cart
 *
 * @author  Prospress
 * @package WooCommerce-Subscriptions/Templates
 * @version 2.2.20
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );
$user_id = get_current_user_id();

if (is_approved($user_id)) {
	do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wcs_json_encode( $available_variations ) ) ?>">
		<?php do_action( 'woocommerce_before_variations_form' ); ?>

		<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
			<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce-subscriptions' ); ?></p>
		<?php else : ?>
			<?php if ( ! $product->is_purchasable() && 0 != $user_id && 'no' != wcs_get_product_limitation( $product ) && wcs_is_product_limited_for_user( $product, $user_id ) ) : ?>
				<?php $resubscribe_link = wcs_get_users_resubscribe_link_for_product( $product->get_id() ); ?>
				<?php if ( ! empty( $resubscribe_link ) && 'any' == wcs_get_product_limitation( $product ) && wcs_user_has_subscription( $user_id, $product->get_id(), wcs_get_product_limitation( $product ) ) && ! wcs_user_has_subscription( $user_id, $product->get_id(), 'active' ) && ! wcs_user_has_subscription( $user_id, $product->get_id(), 'on-hold' ) ) : // customer has an inactive subscription, maybe offer the renewal button ?>
					<a href="<?php echo esc_url( $resubscribe_link ); ?>" class="button product-resubscribe-link"><?php esc_html_e( 'Resubscribe', 'woocommerce-subscriptions' ); ?></a>
				<?php else : ?>
					<p class="limited-subscription-notice notice"><?php esc_html_e( 'You have an active subscription to this product already.', 'woocommerce-subscriptions' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( wp_list_filter( $available_variations, array( 'is_purchasable' => false ) ) ) : ?>
					<p class="limited-subscription-notice notice"><?php esc_html_e( 'You have added a variation of this product to the cart already.', 'woocommerce-subscriptions' ); ?></p>
				<?php endif; ?>
				
				<table class="variations fields" cellspacing="0">
					<tbody>
					<?php foreach ( $attributes as $attribute_name => $options ) : ?>
						<?php
							$terms = wc_get_product_terms($product->get_id(), $attribute_name, [
								'fields' => 'all',
							]);
						?>
						<tr>
							<td class="label">
								<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
									<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>

									<?php if ($note = get_attribute_note($attribute_name)): ?>
										<span class="i">
											i
											<span class="note"><?php echo $note ?></span>
										</span>
										<div class="save-note">(Save <?php the_field('save'); ?>!)</div>
									<?php endif ?>
								</label>
							</td>
							<td class="value">
								<?php
								$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
								
								foreach ($options as $k => $option) {
									$checked = ($selected == $option) ? 'checked' : null;
									$label = $option;

									foreach ($terms as $term) {
										if ($term->slug == $option) {
											$label = $term->name;
											break;
										}
									}

									echo "
										<label class='option field radio'>
											<input type='radio' name='attribute_$attribute_name' value='$option' $checked>
											<span class='box'>$label</span>
										</label>
									";
								}
								
								echo wp_kses( end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce-subscriptions' ) . '</a>' ) : '', array( 'a' => array( 'class' => array(), 'href' => array() ) ) );
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<div class="single_variation_wrap">
					<?php
						do_action( 'woocommerce_before_single_variation' );
						woocommerce_single_variation_add_to_cart_button();
						do_action( 'woocommerce_after_single_variation' );
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_variations_form' ); ?>
	</form>

	<?php
	do_action( 'woocommerce_after_add_to_cart_form' );
} else {
	?>
		<p>Your medical form is currently being review by our doctors. For now, you can view our products, but you won't be able to make a purchase until we confirm that our products are safe for you. If you have any questions, please drop us a message and we'll get back to you as soon as we can!</p>
		<br/>
		<a href="<?php echo home_url('medical-form') ?>" class="btn">Check Eligibility</a>
	<?php
}