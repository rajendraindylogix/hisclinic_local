	<?php 
	/**
	 * Request Treatment Chnage page.
	 */
	$user_id               = get_current_user_id(); 

	$suggested_product     = ! empty( get_user_meta( $user_id, 'suggested_product', true ) ) ? get_user_meta( $user_id, 'suggested_product', true ) : '' ;

	// if( empty( $suggested_product ) ) :

		?>
	
			<!-- <div class="account-top no-subscription">
				<h3><?php _e( 'You dont have any prescriptions yet!', 'woocommerce' ); ?></h3>
				<p><?php _e( 'Our doctors are currently reviewing your medical form. You will be able to make orders and purchase after review/prescription.', 'woocommerce' ); ?></p>
			</div> -->
	
		<?php
	
		// 	return;
	
		// endif;
	
	
	$suggested_product_obj = get_page_by_path( basename( untrailingslashit( $suggested_product ) ) , OBJECT, 'product');
	$suggested_product_id  = $suggested_product_obj->ID;

	$args = array(
		'exclude' => array(5878,539,472),
	);
	if ( ! empty( $suggested_product_id ) ) :
		// Get products that aren't the current product.
		$args = array(
			'exclude' => array(5878,539,472,$suggested_product_id),
		);
	endif;

	$related_products = wc_get_products( $args );

	?>

	<!---------------------------
	---------- ORDERS HTML------- 
	---------------------------->

	<?php if ( ! empty( $suggested_product ) ) : ?>
	
		<div class="account-top rtc-top">
				<h3><?php printf( 'Your current prescription is: %1$s', $suggested_product_obj->post_title ); ?></h3>
				<p><?php _e( 'Your recommended treatment is based on:', 'woocommerce' ); ?></p>
				<ul>
					<li><?php _e( 'Your sexual activity.', 'woocommerce' ); ?></li>
					<li><?php _e( 'The symptoms of erectile dysfunction that you experience', 'woocommerce' ); ?></li>
					<li><?php _e( 'Your treatment history', 'woocommerce' ); ?></li>
					<li><?php _e( 'Your medical history', 'woocommerce' ); ?></li>
				</ul>
				<p><?php _e( 'Other prescription options include ', 'woocommerce' ); ?> 
				<?php 
					$num_of_items = count( $related_products );
					$num_count    = 0;
					foreach( $related_products as $key => $product ) {
						
						echo $product->get_name();
						
						$num_count = $num_count + 1;

						$is_second_last = $num_count == $num_of_items - 1 ? true : false;
						
						if ( $num_count < $num_of_items && ! $is_second_last ) {
							echo ", ";
						}

						if ( $is_second_last )
							echo __( " and ", "woocommerce" );
					}
				?>.
				<?php _e( 'You can request a treatment change below. Please note that your request for a treatment change will need to be reviewed by the doctor and you will be unable to purchase until it is approved.', 'woocommerce' ); ?></p>
		</div>


	<?php endif; ?>

	<div class="accordions accordions-myOrders extend-trchange">
		
		<?php foreach( $related_products as $key => $product ) : ?>
			
			<div class="accordion">
				<div class="title"><?php echo esc_html( $product->get_name() ); ?></div>
				<div class="box">
					<table class="table table-bordered">
						<tr>
							<th colspan="2">
								<div class="table--title">
									<span class="top"><?php echo esc_html( $product->get_name() ); ?></span>
									<span class="bottom"><?php the_field( 'order_information', $product->get_id() ); ?></span>
								</div>
							</th>
						</tr>
						<tr>
							<td>
								<div class="item">
									<?php _e( 'To be taken', 'woocommerce' ); ?>
									<span><?php the_field( 'to_be_taken', $product->get_id() ); ?></span>
								</div>
							</td>
							<td>
								<div class="item">
									<?php _e( 'Average Time to take effect', 'woocommerce' ); ?>
									<span><?php the_field( 'average_time_to_take_effect', $product->get_id() ); ?></span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="item">
									<?php _e( 'Available pack sizes', 'woocommerce' ); ?>
									<span><?php the_field( 'available_pack_sizes', $product->get_id() ); ?></span>
								</div>
							</td>
							<td>
								<div class="item">
									<?php _e( 'Lasts for', 'woocommerce' ); ?>
									<span><?php the_field( 'lasts_for', $product->get_id() ); ?></span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="item download-wrap">
									<?php _e( 'Download Fact sheet', 'woocommerce' ); ?>
									<a href="<?php the_field( 'factsheet', $product->get_id() ); ?>" target="_blank">
										<span class="download-icon"> <img src="<?php echo get_template_directory_uri();?>/assets/img/download.svg" alt="Download Fact sheet"></span>
									</a>
								</div>
							</td>
							<td>
								<div class="item">
									<?php _e( 'Pricing', 'woocommerce' ); ?>
									<span class="black"><?php _e( 'From', 'woocommerce' ); ?> <?php echo get_woocommerce_currency_symbol() . ' ' . $product->get_variation_price('min'); ?></span>
								</div>
							</td>
						</tr>
					</table>

					<div class="accordion-bottom">
						<?php
							$active_treatment_change = get_user_meta( get_current_user_id(), 'active_change_request', true );

							if ( ! $active_treatment_change ) :
						?>
								<form id="product-request-<?php echo $product->get_id(); ?>" class="treatment-change-form">
									<div class="label-textarea">
											<label class="label-treatment-change-reason" for="treatment-change-reason-<?php echo esc_attr( $product->get_id() ); ?>"><?php _e( 'Why would you like to request a treatment change?', 'woocommerce' ); ?></label>
											<textarea class="input-text" required="required" name="treatment_change_reson" id="treatment-change-reason-<?php echo esc_attr( $product->get_id() ); ?>" placeholder="Insert your message…"></textarea>
									</div>
									<input type="hidden" name="action" value="hc_dashboard_treatment_change">
									<input type="hidden" name="current_treatment" value="<?php echo esc_attr( get_the_title( $suggested_product_id  ) ); ?>">
									<input type="hidden" name="requested_by" value="<?php echo esc_attr( get_current_user_id() ); ?>">
									<input type="hidden" name="treatment_change_to" value="<?php echo esc_attr( $product->get_id() ); ?>">
									<a href="#" class="btn btn-filled treatment-change-request"><?php _e( 'Request Treatment Change', 'woocommerce' ); ?></a>
								</form>
						<?php 
							else :

								echo __( 'You have recently requested for treatment change. Your treatment change request is currently being reviewed by our doctors.', 'woocommerce' );

							endif;
						?>
						<div class="loading-spinner" style="display:none;"></div>
						<div class="treatment-change-message"></div>
					</div>
				</div>
			</div>
		
		<?php endforeach; ?>
		
	</div>
