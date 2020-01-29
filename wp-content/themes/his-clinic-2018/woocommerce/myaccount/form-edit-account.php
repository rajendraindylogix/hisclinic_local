<?php
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );

$avatars = [
	'default.png',
	'avatar-1.png',
	'avatar-2.png',
	'avatar-3.png',
	'avatar-4.png',
	'avatar-5.png',
	'avatar-6.png',
	'avatar-7.png',
	'avatar-8.png',
	'avatar-9.png',
	'avatar-10.png',
	'avatar-11.png',
	'avatar-12.png',
	'avatar-13.png',
	'avatar-14.png',
	'avatar-15.png',
];

$current_avatar = get_user_avatar(get_current_user_id());
$user_id = $user->ID;
?>

<div class="accordions">
    <div class="accordion">
        <div class="title"><?php _e( 'Personal Details', 'woocommerce' ); ?></div>
        <div class="box">
			<form class="woocommerce-EditAccountForm edit-account fields" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>> 
				<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
				<input type="hidden" name="account_first_name" value="<?php echo get_first_name($user_id) ?>">
				<input type="hidden" name="account_last_name" value="<?php echo get_last_name($user_id) ?>">

				<div class="row">
					<div class="col-sm-6">
						<div class="field input-text">
							<label>Full Name</label>
							<p><?php echo get_full_name($user_id) ?></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="field input-text">
							<label for="billing_mobile_phone"><?php _e( 'Phone Number', 'woocommerce' ); ?> <span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--phone input-text" name="billing_mobile_phone" id="billing_mobile_phone" value="<?php

							$billing_phone = $user->billing_mobile_phone;

							if ( empty( $billing_phone ) )
								$billing_phone = get_user_meta( $user_id, 'billing_phone', true );
							
							echo esc_attr( $billing_phone ); ?>" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="field input-text">
							<label for="account_email"><?php _e( 'Email Address', 'woocommerce' ); ?> &nbsp;<span class="required">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="field input-text">
							<label for="account_shipping_address"><?php _e( 'Your Shipping Address', 'woocommerce' ); ?> &nbsp;<span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--email input-text" name="account_shipping_address" id="account_shipping_address" value="<?php
								
								$address_1 = $user->account_shipping_address;

								if ( empty( $address_1 ) )
									$address_1 = get_user_meta( $user_id, 'billing_address_1', true );
								
								echo esc_attr( $address_1 );
							?>" />
						</div>
					</div>
				</div>


				<?php do_action( 'woocommerce_edit_account_form' ); ?>

				<div>
					<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
					<button type="submit" class="btn" name="save_account_details" value="Save">Save</button>
					<input type="hidden" name="action" value="save_account_details" />
				</div>

				<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
			</form>
		</div>
    </div>

	<div class="accordion">
		<div class="title"><?php _e( 'Avatar', 'woocommerce' ); ?></div>
		<div class="box">
			<form id="update-avatar" action="<?php echo get_permalink() ?>" method="post">
				<input type="hidden" name="action" value="save_account_avatar">

				<div class="avatars">
					<?php foreach ( $avatars as $avatar ) : ?>
						<label class="avatar">
							<div class="inner">
								<input type="radio" name="avatar" value="<?php echo $avatar ?>" <?php if ($current_avatar == $avatar) echo 'checked' ?>>
								<div class="overlay">&nbsp;</div>
								<img src="<?php echo get_user_avatar_url( $avatar ) ?>" alt="avatar">
							</div>
						</label>
					<?php endforeach; ?>
				</div>
			</form>
		</div>
	</div>

	<div class="accordion">
		<div class="title">Change Password</div>
		
		<div class="box">
			
			<form id="update-account-password" class="fields" action="" method="post">

				<div class="mf-step">
					<div class="field input-text">
						<label for="password_current">Current Password</label>
						<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
					</div>

					<div class="pass-wrap">
						<a  href="#_" class="btn btn-filled mf-next" name="" value="">Next</a>
					</div>

				</div>
				
				<div class="mf-step" style="display:none;">
					<div class="field input-text">
							<label for="password_1">New Password</label>
							<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
							
							<p class="small">* password must contain at least 6 characters, an uppercase character and a number.</p>
						</div>
						<div class="pass-wrap">
							<a  href="#_" class="btn btn-filled mf-next" name="" value="">Next</a>
						</div>

				</div>

				<div class="mf-step" style="display:none;">
						<div class="field input-text">
							<label for="password_2">Confirm New Password</label>
							<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
						</div>
						<?php wp_nonce_field( 'save_account_password', 'save-account-password-nonce' ); ?>
						<div class="pass-wrap">
							<button type="submit" class="btn btn-filled" name="save_account_password" value="Save">Save</button>
						</div>
					<input type="hidden" name="action" value="save_account_password" />

				</div>
				

				<!-- <div class="row">
					<div class="col-sm-6">
						
					</div>
					<div class="col-sm-6">
						
					</div>
				</div> -->

				<div>
					<?php wp_nonce_field( 'save_account_password', 'save-account-password-nonce' ); ?>
					<button type="submit" class="btn" name="save_account_password" value="Save" style="display:none;">Save</button>
					<input type="hidden" name="action" value="save_account_password" />
				</div>

			</form>

		</div>

	</div>

	<!-- <div class="accordion">
		<div class="title">Billing Address</div>
		<div class="box">
			<?php woocommerce_account_edit_address('billing') ?>
		</div>
	</div>

	<div class="accordion">
		<div class="title">Shipping Address</div>
		<div class="box">
			<?php woocommerce_account_edit_address('shipping') ?>
		</div>
	</div> -->
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
