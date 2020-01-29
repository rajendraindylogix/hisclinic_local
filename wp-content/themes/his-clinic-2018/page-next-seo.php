<?php

// Template Name: SEO Next Page

get_header(); ?>

	<?php
		while (have_posts()):
			the_post();

			get_template_part('template-parts/seo-next-page', get_post_type());
			endwhile;
	?>

<?php get_footer();
	
