<?php
/**
 * Woocommerce Checkout billing information form to show
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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

/** @global WC_Checkout $checkout */

$user_id = null;

if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $user_id = $user->ID;
}
?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
            $fields = $checkout->get_checkout_fields( 'billing' );
            $include_fields = [
                'billing_first_name', 'billing_last_name', 'billing_phone', 'billing_address_1', 'billing_address_2', 
                'billing_city', 'billing_state', 'billing_postcode',
            ];

            foreach ($include_fields as $key) {
                if (empty($fields[$key])) {
                    continue;
                }
                
                $field = $fields[$key];
                $field_value = $checkout->get_value($key);

                switch ($key) {
                    case 'billing_first_name':
                        if (!$field_value) {
                            $field_value = get_first_name($user_id);
                        }

                        break;

                    case 'billing_last_name':
                        if (!$field_value) {
                            $field_value = get_last_name($user_id);
                        }

                        break;
                        
                    case 'billing_phone':
                        $field['label'] = __( 'Phone Number', 'woocommerce' );
                        break;
                        
                    case 'billing_address_1':
                        $field['custom_attributes']['autocomplete'] = 'off';
                        $field['custom_attributes']['maxlength'] = 40;
                        break;

                    case 'billing_address_2':
                        $field['label'] = false;
                        $field['custom_attributes']['maxlength'] = 40;
                        break;
                    
                    case 'billing_city':
                        $field['label'] = __( 'Suburb', 'woocommerce' );
                        break;
                                            
                    case 'billing_state':
                        $field['label'] = __( 'State', 'woocommerce' );
                        $field['type'] = 'text';
                        break;
                                        
                    case 'billing_postcode':
                        $field['label'] = __( 'Postcode', 'woocommerce' );
                        break;
                    
                    default:
                        break;
                }
                
                woocommerce_checkout_form_field($key, $field, $field_value);
            }
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<!-- <?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ) ?> type="checkbox" name="createaccount" value="1" /> <span><?php _e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?> -->

<?php // Chrome not respecting autocomplete and changes automatically to something else. Forcing autocomplete off ?>
<script>
    setTimeout(() => {
        $('#billing_city').prop('autocomplete', 'none');
        $('#shipping_city').prop('autocomplete', 'none');
    }, 750);
</script>