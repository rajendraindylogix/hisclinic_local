<?php get_header(); ?>

<div class="container">
    <div class="search-results">

        <header class="page-header">
            <?php if ( have_posts() ) : ?>
                <h2 class="page-title"><?php printf('Search Results for: %s', '<span>' . get_search_query() . '</span>' ); ?></h2>
            <?php else : ?>
                <h2 class="page-title">Nothing Found</h2>
            <?php endif; ?>
        </header><!-- .page-header -->

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php if (have_posts()): ?>
                    <div class="posts-container">
                        <div class="posts">
                            <?php
                                while ( have_posts() ) :
                                    the_post();
                                    get_template_part_with_args('template-parts/post-item', ['p' => $post]);
                                endwhile;
                            ?>
                        </div>
                    </div>

                    <?php
                        the_posts_pagination([
                            'prev_text' => '<',
                            'next_text' => '>',
                        ]);
                    ?>
                <?php else: ?>
                    <div class="no-results">
                        <p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>

                        <?php get_search_form(); ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<?php
get_footer();