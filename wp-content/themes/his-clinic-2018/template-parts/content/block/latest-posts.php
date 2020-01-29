<?php
/*
    Widget Title: Latest Posts
*/

$args = [
    'numberposts' => 8,
    'post_status' => 'publish',
];

$posts = wp_get_recent_posts($args, OBJECT);
?>

<div class="card-carousel">
    <div class="cards">
        <?php
            foreach ($posts as $p) {
                $image = get_the_post_thumbnail_url($p, 'post_carousel');
                
                get_template_part_with_args('template-parts/content/carousel-card/card', [
                    'image' => $image,
                    'title' => $p->post_title,
                    'content' => get_post_excerpt($p),
					'link' => get_permalink($p),
                ]);
            }
        ?>
    </div>
</div>