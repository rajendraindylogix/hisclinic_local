<div class="main-content">
	<div class="container">
		<div class="intro">
			<h1 class="h1 main-title"><?php the_title() ?></h1>

			<div class="details">
				<span class="author h5"><?php the_author() ?></span>
				<span class="divider">·</span>
				<span class="date h5"><?php the_date() ?></span>
			</div>
		</div>
	</div>
	<div class="the-content">
		<?php the_content(); ?>
		
		<div class="container">
			<div class="post-footer">
				<div class="author">
					<div class="pic">
						<?php $avatar = get_user_avatar(get_the_author_meta('ID')); ?>
						<img src="<?php echo get_user_avatar_url($avatar) ?>" alt="<?php the_author() ?>">
					</div>
					<div class="text">
						<h5 class="h5"><?php the_author() ?></h5>
						<p class="small"><?php the_author_description() ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
		<?php comments_template(); ?>
	</div>

	<?php 
		if (!get_field('hide_related_posts')) {
			get_template_part('template-parts/content/post/related');
		}
	?>

	<div class="more">
		<div class="container">
			<h3 class="h2">Have a question?</h3>

			<a href="<?php echo home_url('contact-us') ?>" class="btn">Contact us</a>
		</div>
	</div>
</div>

<?php
