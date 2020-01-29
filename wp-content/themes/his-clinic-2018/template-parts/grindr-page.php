<div class="lp-header">
	<div class="container">
		<div class="row">
			<div class="col-lg-9 col-md-9 col-12 left">
				<img src="<?php img() ?>/lp-logo.png" id="desktop-logo" alt="His Clinic" width="190" height="108">
				<img src="<?php img() ?>/Logo-mobile-white.png" id="mobile-logo" alt="His Clinic" width="96" height="54">
			</div>
			<div class="col-lg-3 col-md-3 col-12 right">
				<a href="<?php echo home_url('medical-form') ?>" class="check">Check your eligibly to order</a>
			</div>
		</div>
	</div>
</div>
<div class="section-1">
	<div class="container">
		<div class="row">
			<div class="col-lg-9 col-md-9 col-12 left">
				<div class="text-container">
					<?php the_content(); ?>
					<a href="<?php echo home_url('medical-form') ?>" class="check">Check your eligibly to order</a><br/>
					<img src="<?php img() ?>/drop-down.png" alt="His Clinic" width="47" height="47" id="down">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-12 right">
			</div>
		</div>
	</div>
</div>
<?php $image = get_field('section_2_image'); ?>
<div id="section-2" class="section-2" style="background-image: url(<?php echo $image['url']; ?>); ">
	<div class="container">
		<div class="row">
			<div class="col-lg-9 col-md-9 col-12 left">
				<?php if( !empty($image) ): ?>
					<div class="img-container">
						<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" id="for-mobile-img" />
					</div>
				<?php endif; ?>
				<div class="text-container">
					<?php the_field('section_2_content'); ?>
					<a href="<?php echo home_url('medical-form') ?>" class="check">Check your eligibly to order</a>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-12 right">
				
			</div>
		</div>
	</div>
</div>
<?php $image2 = get_field('section_3_image'); ?>
<div class="section-3" style="background-image: url(<?php echo $image2['url']; ?>); ">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-12 left">
				<?php if( !empty($image2) ): ?>
					<img src="<?php echo $image2['url']; ?>" alt="<?php echo $image2['alt']; ?>" />
				<?php endif; ?>
			</div>
			<div class="col-lg-6 col-md-6 col-12 right">
				<div class="text-container">
					<?php the_field('section_3_content'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="get-started-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 col-md-2 col-12"></div>
			<div class="col-lg-8 col-md-8 col-12">
				<h2>Get started</h2>
				<p>We’re here to help you treat your ED in a way that’s quick, simple, and discreet. Talk with a friendly member of our all-male customer service team now or check your eligibility.</p>
				<a href="<?php echo home_url('medical-form') ?>" class="check">Check your eligibly to order</a>
			</div>
			<div class="col-lg-2 col-md-2 col-12"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("#down").click(function(){
	    $('html, body').animate({
        	scrollTop: $("#section-2").offset().top
	    }, 800);	
	});
</script>