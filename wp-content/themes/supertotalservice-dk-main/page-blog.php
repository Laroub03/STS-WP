<?php
/* Template Name: Nyheder */
get_header();
$sts_news = new WP_Query(array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 20,
));
?>
<main class="container section-content">
  <div class="section-head">
    <div><span class="eyebrow">Nyheder</span><h1>Seneste nyt fra STS ApS</h1></div>
  </div>
  <?php if ($sts_news->have_posts()) : ?>
    <div class="service-directory-grid">
      <?php while ($sts_news->have_posts()) : $sts_news->the_post();
          $sts_image = function_exists('sts_content_news_image') ? sts_content_news_image(get_the_ID()) : '';
      ?>
        <article class="service-directory-card">
          <?php if ($sts_image) : ?><img src="<?php echo esc_url($sts_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"><?php endif; ?>
          <div class="service-card-body">
            <p class="eyebrow"><?php echo esc_html(get_the_date()); ?></p>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html(get_the_excerpt()); ?></p>
            <a class="service-link" href="<?php the_permalink(); ?>">Læs artiklen</a>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  <?php else : ?>
    <p>Der er endnu ingen nyheder.</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>