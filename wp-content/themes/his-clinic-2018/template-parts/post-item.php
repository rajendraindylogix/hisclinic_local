<?php
global $part_args;
$p = $part_args['p'];
$link = get_permalink($p->ID);
?>

<article class="post-item">
    <div class="row">
        <div class="col-sm-5">
            <div class="image">
                <a href="<?php echo get_permalink($p) ?>">
                    <?php echo get_the_post_thumbnail($p, 'post_featured') ?>
                </a>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="text">
                <h5 class="h5 pink date"><?php echo get_the_date('F d, Y', $p) ?></h5>
                <h3 class="h3 title">
                    <a href="<?php echo $link ?>"><?php echo $p->post_title ?></a>
                </h3>
                
                <p class="excerpt">
                    <?php echo get_post_excerpt($p, 40) ?>
                </p>

                <a href="<?php echo $link ?>" class="more">Read More</a>
            </div>
        </div>
    </div>
</article>