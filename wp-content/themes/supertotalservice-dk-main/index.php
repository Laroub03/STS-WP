<?php get_header(); ?>

<div class="container">
    <?php if (have_posts()) : ?>
        <div class="posts-container">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php
                        if (is_singular()) :
                            the_title('<h1 class="entry-title">', '</h1>');
                        else :
                            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                        endif;
                        ?>
                    </header>
                    
                    <div class="entry-content">
                        <?php
                        if (is_singular()) :
                            the_content();
                        else :
                            the_excerpt();
                        endif;
                        ?>
                    </div>
                    
                    
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php
        the_posts_navigation();
        ?>
        
    <?php else : ?>
        <div class="no-posts">
            <h2>Nothing found</h2>
            <p>It seems we can't find what you're looking for.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>