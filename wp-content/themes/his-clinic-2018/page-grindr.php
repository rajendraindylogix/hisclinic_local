<?php

// Template Name: Grindr Landing Page

get_header(); ?>

	<?php
		while (have_posts()):
			the_post();

			get_template_part('template-parts/grindr-page', get_post_type());
			endwhile;
	?>

<?php get_footer();