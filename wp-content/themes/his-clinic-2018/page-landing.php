<?php

// Template Name: Landing Page

get_header('landing');

while (have_posts()):
	the_post();
	the_content();
endwhile;

get_footer('landing');
