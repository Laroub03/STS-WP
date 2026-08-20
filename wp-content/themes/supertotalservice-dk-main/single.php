<?php
get_header();
while (have_posts()) : the_post();
    $sts_image = function_exists('sts_news_image') ? sts_news_image(get_the_ID()) : '';
?>
<main id="main-content">
  <div class="hub-hero"><div class="container"><span class="eyebrow">📰 Nyt fra STS</span><h1><?php the_title(); ?></h1></div></div>
  <article class="blog-post-container">
    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="back-link">← Tilbage til nyheder</a>
    <div class="blog-post-meta"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y-m-d')); ?></time><span>Super Total Service</span></div>
    <?php if ($sts_image) : ?><img src="<?php echo esc_url($sts_image); ?>" alt="<?php the_title_attribute(); ?>" class="blog-post-image"><?php endif; ?>
    <div class="blog-post-content entry-content"><?php the_content(); ?></div>
    <div class="blog-post-cta"><p><strong>Har du spørgsmål til denne artikel?</strong></p><p>Vi hjælper gerne med mere information om vores services</p><a href="<?php echo esc_url(home_url('/kontakt/')); ?>" class="btn btn-primary">Kontakt os</a></div>
  </article>
</main>
<?php endwhile; get_footer(); ?>
