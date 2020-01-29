<!DOCTYPE html>
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
	<style>
		.product-card .inner .price .woocommerce-Price-currencySymbol:before{
			content: "FROM";
			display: inline-block;
			margin-right: 4px;
			position: relative;
		}
	</style>
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
				<div class="col-xs-9 col-sm-10">
					<div class="desktop">
						<a href="<?php echo site_url( '/medical-form/' ); ?>" class="btn filled check-eligibility">
							<span class="text">Check Eligibility To Access Treatments</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>