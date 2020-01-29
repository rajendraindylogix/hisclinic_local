<?php
/*
    Widget Title: Posts
*/

$categories = get_categories();
$posts = get_posts([
    'posts_per_page' => get_option('posts_per_page')
]);

$total = wp_count_posts();
$more = (count($posts) < $total->publish);
?>

<div class="posts-container">
    <div class="posts-filter list-scroller">
        <ul>
            <li><a href="#" class="active" data-category="all">All</a></li>

            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="#" data-category="<?php echo $category->slug ?>"><?php echo $category->name ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="posts">
        <?php
            foreach ($posts as $p) {
                get_template_part_with_args('template-parts/post-item', ['p' => $p]);
            }
        ?>
    </div>

    <div class="load-more-container" <?php if (!$more): ?> style="display:none" <?php endif; ?>>
        <a href="#" class="btn load-more">Load More</a>
    </div>
</div>