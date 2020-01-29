<?php
/**
 * Single-Product Subscription Options Template.
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/product-subscription-options.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wcsatt-options-wrapper" <?php echo count( $options ) === 1 ? 'style="display:none;"' : '' ?>><?php

	//Removed Prompt for subscription
	/*if ( $prompt ) {
		echo $prompt;
	} else {
		?><h3><?php
			_e( 'Choose a subscription plan:', 'woocommerce-subscribe-all-the-things' );
		?></h3><?php
	}*/

	?><ul class="wcsatt-options-product"><?php
		$i = 1;
		foreach ( $options as $option ) {

			$discount_per = isset( $option['data']['subscription_scheme']['discount'] ) && ! empty( $option['data']['subscription_scheme']['discount'] ) ? $option['data']['subscription_scheme']['discount'] : false;

			$option_id = $option['value'] . $i;

			$discount_html = '';

			if ( $discount_per )
				$discount_html = sprintf( __( ' ( Save %1$s ', 'hippeieco' ), $discount_per  ) . '% )<span data-template="' . esc_attr( $option_id ) . '" class="tippy-popup"> - see details</span>';

			?>
			<li class="<?php echo esc_attr( $option[ 'class' ] ); ?>">
				<label>
					<input type="radio" name="convert_to_sub_<?php echo absint( $product_id ); ?>" data-custom_data="<?php echo esc_attr( json_encode( $option[ 'data' ] ) ); ?>" value="<?php echo esc_attr( $option[ 'value' ] ); ?>" <?php checked( $option[ 'selected' ], true, true ); ?> />
					<?php echo '<span class="' . esc_attr( $option[ 'class' ] ) . '-details">' . $option[ 'description' ] . $discount_html . '</span>'; ?>
				</label>
			</li><?php
			$i++;
		}
	?></ul>
</div>
