<?php  
/**
* Template Name: No Header Footer
*
* @package WordPress
*/
?>
<?php
	$hide_breadcrumbs = (!empty($hide_breadcrumbs)) ? $hide_breadcrumbs : get_field('hide_breadcrumbs');
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<?php the_field('google_tag_manager', 'option'); ?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="icon" type="image/png" href="<?php echo home_url('favicon.png'); ?>">

	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>

	<!-- Hotjar Tracking Code for https://hisclinic.com.au -->
	<script>
		(function(h,o,t,j,a,r){
			h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
			h._hjSettings={hjid:1126382,hjsv:6};
			a=o.getElementsByTagName('head')[0];
			r=o.createElement('script');r.async=1;
			r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
			a.appendChild(r);
		})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
	</script>
</head>



<body <?php body_class(); ?>>
	<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>

	<header class="main-header">
		<div class="container">
			<div class="row">
				<div class="col-xs-3 col-sm-2">
					<a href="<?php echo home_url() ?>" class="logo">
						<span class="desktop">
							<img src="<?php img() ?>/logo.png" alt="<?php echo get_bloginfo('title') ?>">
						</span>
						<span class="mobile">
							<img src="<?php img() ?>/res-logo.png" alt="<?php echo get_bloginfo('title') ?>">
						</span>
					</a>
				</div>
				<?php if( is_wc_endpoint_url( 'order-received' ) ) : ?>
					<div class="col-xs-9 col-sm-10">
						<div class="desktop">
							<?php if (is_user_logged_in()): 
								
								$cur_usr_id        = get_current_user_id();
								$suggested_product = get_user_meta( $cur_usr_id, 'suggested_product', true );
								$default_prod_id   = function_exists( 'get_field' ) ? get_field( 'default_product', 'option' ) : '';
							
								if ( ! empty( $suggested_product ) ) {

									$shop_url = $suggested_product;

								} else {
									$shop_url = get_permalink( $default_prod_id );
								}
								
								if ( is_singular( 'product' ) ) :

									global $post;
									$post_id = $post->ID;
		
									$shop_url = home_url( '/order-details' );
									$shop_url = add_query_arg( 'prod_id', $post_id, $shop_url );
		
								endif;
							?>
								<a href="<?php echo esc_url( $shop_url ); ?>" class="btn filled">
									<span class="text"><?php _e( 'Start Your Order', 'woocommerce' ); ?></span>
								</a>
							<?php else: ?>
								<a href="<?php echo home_url('medical-form') ?>" class="btn filled check-eligibility">
									<span class="text">Check Eligibility</span>
								</a>
							<?php endif ?>
						</div>

						<span class="mobile">
							<div class="menu-toggle">
								<div class="opener">
									<img src="<?php img() ?>/menu-opener.svg" alt="Menu">
								</div>
								<div class="closer">
									<img src="<?php img() ?>/menu-closer.svg" alt="Menu">
								</div>
							</div>
						</span>

						<?php if (is_user_logged_in()): ?>
							<?php if (get_cart_count()): ?>
								<a href="<?php echo home_url('cart') ?>" class="cart-icon">
									<img src="<?php img() ?>/briefcase.svg" alt="Cart">
									<span class="number"><?php echo get_cart_count(); ?></span>
								</a>
							<?php else: ?>
								<a href="<?php echo home_url('cart') ?>" class="cart-icon empty">
									<img src="<?php img() ?>/briefcase-empty.svg" alt="Cart">
									<span class="number"></span>
								</a>
							<?php endif ?>
						<?php endif ?>

						<span class="desktop">
							<?php wp_nav_menu(['menu' => 'top-menu']) ?>
						</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>
<?php 
    
    while (have_posts()):
        the_post();
        the_content();
    endwhile;

?>
<?php if( is_wc_endpoint_url( 'order-received' ) ) :

	$sm = get_field('social_media', 'option');
	$content = get_field('footer_content', 'option');

	// No need to check for empty
	$column_1 = $content[0]['column_1'][0];
	$column_2 = $content[0]['column_2'][0];
	$column_3 = $content[0]['column_3'][0];
	$column_4 = $content[0]['column_4'][0];
?>
	<footer class="main-footer">
		<div class="container">
			<div class="line-1">
				<div class="row">
					<div class="col-sm-6 sm">
						<?php if ( $sm ) : ?>
							<?php foreach ($sm as $s): ?>
								<a href="<?php echo $s['url'] ?>" target="_blank">
									<img src="<?php echo $s['icon']['url'] ?>" alt="<?php echo $s['name'] ?>">
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="col-sm-6">
						<div class="newsletter-signup">
							<div id="mc_embed_signup">
								<form action="https://hisclinic.us19.list-manage.com/subscribe/post?u=51fe2ebc7592adc47d2357f71&amp;id=ede6b98756" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
									<div id="mc_embed_signup_scroll">
										<div class="mc-field-group field input-text">
											<input type="email" name="EMAIL" id="mce-EMAIL" class="required email" placeholder="Your Email">
											<button type="submit" class="btn filled" id="mc-embedded-subscribe"><span>Subscribe</span></button>
										</div>

										<div id="mce-responses" class="clear">
											<div class="response" id="mce-error-response" style="display:none"></div>
											<div class="response" id="mce-success-response" style="display:none"></div>
										</div>

										<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_51fe2ebc7592adc47d2357f71_ede6b98756" tabindex="-1" value=""></div>
									</div>
								</form>
							</div>

							<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
							<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
						</div>
					</div>
				</div>
			</div>

			<div class="line-2">
				<div class="row">
					<div class="col-sm-3 col-sm-push-4 column-3">
						<?php if ($column_3['title']): ?>
							<h3><?php echo $column_3['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_3['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-push-4 column-4">
						<?php if ($column_4['title']): ?>
							<h3><?php echo $column_4['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_4['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-push-4 column-2">
						<?php if ($column_2['title']): ?>
							<h3><?php echo $column_2['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_2['content'] ?>
					</div>
					<div class="col-sm-3 col-sm-pull-9 column-1">
						<?php if ($column_1['title']): ?>
							<h3><?php echo $column_1['title'] ?></h3>
						<?php endif; ?>

						<?php echo $column_1['content'] ?>
					</div>
				</div>
				<p><small>Website by <a href="https://jaywing.com.au/" target="_blank">Jaywing</a></small></p>
			</div>
		</div>
	</footer>
<?php endif; ?>

	<?php wp_footer(); ?>

	<!-- <script type="text/javascript">
		(function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document, 'https://hisclinic.ladesk.com/scripts/track.js', function(e){ LiveAgent.createButton('7b0d4bcb', e); });
	</script> -->
</body>
</html>
