<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$user_id = null;

if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $user_id = $user->ID;
}
?>
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<h3 id="ship-to-different-address">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> <span><?php _e( 'I have a different delivery address', 'woocommerce' ); ?></span>
			</label>
		</h3>

		<div class="shipping_address">

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );
					$include_fields = [
						'shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2', 
						'shipping_city', 'shipping_state', 'shipping_postcode',
					];

					foreach ($include_fields as $key) {
						if (empty($fields[$key])) {
							continue;
						}

						$field = $fields[$key];
						$field_value = $checkout->get_value($key);

						switch ($key) {
							case 'shipping_first_name':
								if (!$field_value) {
									$field_value = get_first_name($user_id);
								}
		
								break;
		
							case 'shipping_last_name':
								if (!$field_value) {
									$field_value = get_last_name($user_id);
								}
		
								break;
	
							case 'shipping_state':
								$field['type'] = 'text';		
								break;
	
							case 'shipping_address_1':
								$field['custom_attributes']['autocomplete'] = 'off';
								$field['custom_attributes']['maxlength'] = 40;
								break;

							case 'shipping_address_2':
								$field['label'] = false;
								$field['custom_attributes']['maxlength'] = 40;
								break;
							
							default:
								break;
						}

						woocommerce_checkout_form_field( $key, $field, $field_value );
					}
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

		</div>

	<?php endif; ?>
</div>
<!-- <div class="woocommerce-additional-fields">
	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

			<h3><?php _e( 'Additional information', 'woocommerce' ); ?></h3>

		<?php endif; ?>

		<div class="woocommerce-additional-fields__field-wrapper">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?php woocommerce_checkout_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div> -->
