<?php get_header(); ?>

<div class="single-post">
	<main id="main" class="site-main" role="main">
		<?php
			while (have_posts()):
				the_post();

				get_template_part('template-parts/content', get_post_type());
			endwhile;
		?>
	</main>
</div>

<?php get_footer(); ?>
