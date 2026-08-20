<?php
get_header();
while (have_posts()) : the_post();
    $sts_image = function_exists('sts_content_service_image') ? sts_content_service_image(get_the_ID()) : '';
    $sts_hero_title = get_post_meta(get_the_ID(), '_sts_service_hero_title', true);
    $sts_icon = get_post_meta(get_the_ID(), '_sts_service_icon', true);
    $sts_benefits = (array) get_post_meta(get_the_ID(), '_sts_service_benefits', true);
?>
<main class="service-hero">
  <div class="container">
    <span class="eyebrow"><?php echo esc_html($sts_icon ?: '🔧'); ?> Serviceydelse</span>
    <h1><?php echo esc_html($sts_hero_title ?: get_the_title()); ?></h1>
    <?php if (has_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
  </div>
</main>
<main class="container section-content">
  <?php if ($sts_image) : ?><img class="service-detail-main-image" src="<?php echo esc_url($sts_image); ?>" alt="<?php the_title_attribute(); ?>"><?php endif; ?>
  <article class="service-description-card entry-content"><?php the_content(); ?>
    <?php if ($sts_benefits) : ?><h2>Det får I</h2><ul><?php foreach ($sts_benefits as $benefit) : ?><li><?php echo esc_html($benefit); ?></li><?php endforeach; ?></ul><?php endif; ?>
  </article>
</main>
<?php endwhile; get_footer(); ?>