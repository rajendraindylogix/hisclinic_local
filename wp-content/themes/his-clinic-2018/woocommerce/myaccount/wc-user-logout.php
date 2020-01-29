<?php 
/**
 * Dashboard Log Out
 * @package His_Clinic
 */
?>
<div class="account-top sign-out">
   <h3><?php _e( 'Sign out of your account?', 'woocommerce' ); ?></h3>
   <p><?php _e( 'You will need to sign back in to access your account and product information. ', 'woocommerce' ); ?></p>
   <a href="<?php echo esc_url( wc_logout_url() ); ?>" class="btn btn-filled"><?php _e( 'Sign Out', 'woocommerce' ); ?></a>
</div>

