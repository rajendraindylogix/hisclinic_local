<?php
    $posts = get_related_posts(['limit' => 20]);
?>

<div class="related-posts">
    <div class="heading">
        <div class="container">
            <div class="inner">
                <h2 class="h2">Men's Health</h2>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card-carousel">
            <div class="posts">
                <?php
                    foreach ($posts as $p) {
                        $image = get_the_post_thumbnail_url($p, 'post_carousel');

                        get_template_part_with_args('template-parts/content/carousel-card/card', [
                            'image' => $image,
                            'title' => $p->post_title,
                            'content' => get_post_excerpt($p, 20),
                            'link' => get_permalink($p),
                        ]);
                    }
                ?>
            </div>
        </div>
        <div class="back-to">
            <a href="<?php echo home_url('mens-health-blog-erectile-dysfunction/') ?>" class="btn">Back to Blog</a>
        </div>
    </div>
</div>