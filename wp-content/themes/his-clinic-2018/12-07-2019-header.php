<?php
	$hide_breadcrumbs = (!empty($hide_breadcrumbs)) ? $hide_breadcrumbs : get_field('hide_breadcrumbs');
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<!-- Anti-flicker snippet (recommended)  -->
<style>.async-hide { opacity: 0 !important} </style>
<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
})(window,document.documentElement,'async-hide','dataLayer',4000,
{'GTM-MTMJZLR':true});</script>

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
							<img src="<?php img() ?>/hisclinic-logo.png" alt="<?php echo get_bloginfo('title') ?>">
						</span>
						<span class="mobile">
							<img src="<?php img() ?>/res-logo.png" alt="<?php echo get_bloginfo('title') ?>">
						</span>
					</a>
				</div>
				<?php if(!is_page_template( 'medical-form/page-medicalform.php' )) :?>
				<div class="col-xs-9 col-sm-10">
					<div class="desktop">
						<?php if (is_user_logged_in()): ?>
							<a href="<?php echo home_url('shop') ?>" class="btn filled">
								<span class="text">See Treatments</span>
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

	<?php get_template_part('template-parts/res-menu') ?>

	<?php if ( !is_front_page() && function_exists('yoast_breadcrumb') && !is_page_template( 'medical-form/page-medicalform.php' )) : ?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<?php yoast_breadcrumb( '<p id="breadcrumbs">','</p>' ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>