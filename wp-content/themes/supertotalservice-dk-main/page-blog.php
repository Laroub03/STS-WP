<?php
/* Template Name: Nyheder */
get_header();
$sts_news = new WP_Query(array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 20,
));
?>
<main id="main-content">
  <div class="hub-hero">
    <div class="container">
      <span class="eyebrow">📰 Nyt fra STS</span>
      <h1>Nyheder</h1>
      <p>Seneste nyt, viden og opdateringer fra STS ApS.</p>
    </div>
  </div>
  <section class="section">
    <div class="container">
      <?php if ($sts_news->have_posts()) : ?>
        <div class="section-head"><div><span class="eyebrow">Seneste artikler</span><h2>Følg med i hvad der sker hos STS.</h2></div></div>
        <div class="blog-grid">
          <?php while ($sts_news->have_posts()) : $sts_news->the_post();
              $sts_image = function_exists('sts_news_image') ? sts_news_image(get_the_ID()) : '';
          ?>
            <article class="blog-card">
              <?php if ($sts_image) : ?><img src="<?php echo esc_url($sts_image); ?>" alt="<?php the_title_attribute(); ?>" class="blog-card-image" loading="lazy">
              <?php else : ?><div class="blog-card-image blog-card-image-empty" aria-hidden="true">📰</div><?php endif; ?>
              <div class="blog-card-content">
                <div class="blog-card-date"><?php echo esc_html(get_the_date('Y-m-d')); ?></div>
                <h3 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="blog-card-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                <a href="<?php the_permalink(); ?>" class="blog-card-link">Læs mere <span>→</span></a>
              </div>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      <?php else : ?>
        <div class="blog-empty"><p>📰 Ingen nyheder endnu. Venligst kom tilbage senere.</p></div>
      <?php endif; ?>
    </div>
  </section>
  <section class="cta-band"><div class="container"><h2>Skal vi løse jeres næste opgave?</h2><p>Send en forespørgsel og få et tilbud inden for en arbejdsdag. Helt gratis og uforpligtende.</p><div class="cta-actions"><a class="btn-white" href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontakt os nu</a><a class="btn-outline-white" href="<?php echo esc_url(home_url('/service/')); ?>">Se vores ydelser</a></div></div></section>
</main>
<?php get_footer(); ?>