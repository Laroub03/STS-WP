<?php
get_header();
while (have_posts()) : the_post();
    $sts_image = function_exists('sts_content_service_image') ? sts_content_service_image(get_the_ID()) : '';
    $sts_hero_title = get_post_meta(get_the_ID(), '_sts_service_hero_title', true);
    $sts_icon = get_post_meta(get_the_ID(), '_sts_service_icon', true);
    $sts_benefits = (array) get_post_meta(get_the_ID(), '_sts_service_benefits', true);
    $sts_process = (array) get_post_meta(get_the_ID(), '_sts_service_process', true);
?>
<main id="main-content">
<section class="hub-hero">
  <div class="container">
    <span class="eyebrow"><?php echo esc_html($sts_icon ?: '🔧'); ?> Service</span>
    <h1><?php echo esc_html($sts_hero_title ?: get_the_title()); ?></h1>
    <?php if (has_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
    <div class="hero-actions"><a class="btn btn-primary" href="<?php echo esc_url(home_url('/kontakt/')); ?>">Få et tilbud</a><a class="btn btn-secondary" href="<?php echo esc_url(home_url('/service/')); ?>">Tilbage til alle ydelser</a></div>
  </div>
</section>
<?php if ($sts_image) : ?><section class="section"><div class="container"><img class="service-detail-main-image" src="<?php echo esc_url($sts_image); ?>" alt="<?php the_title_attribute(); ?>"></div></section><?php endif; ?>
<section class="section"><div class="container">
  <div class="section-head"><div><span class="eyebrow">Om servicen</span><h2><?php the_title(); ?></h2></div></div>
  <?php if (get_the_content()) : ?><div class="service-description-card entry-content"><?php the_content(); ?></div><?php endif; ?>
  <?php if ($sts_benefits) : ?><div class="service-description-card" style="margin-top:1.5rem"><h3>Fordele</h3><ul><?php foreach ($sts_benefits as $benefit) : ?><li><?php echo esc_html($benefit); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
</div></section>
  <?php if (!empty($sts_process['steps'])) : ?>
    <section class="section-content">
      <?php if (!empty($sts_process['eyebrow'])) : ?><span class="eyebrow"><?php echo esc_html($sts_process['eyebrow']); ?></span><?php endif; ?>
      <?php if (!empty($sts_process['title'])) : ?><h2><?php echo esc_html($sts_process['title']); ?></h2><?php endif; ?>
      <div class="grid-layout-container">
        <?php foreach ($sts_process['steps'] as $index => $sts_step) : if (empty($sts_step['title']) && empty($sts_step['description'])) continue; ?>
          <article class="step-card"><strong><?php echo esc_html($index + 1); ?></strong><h3><?php echo esc_html($sts_step['title']); ?></h3><p><?php echo esc_html($sts_step['description']); ?></p></article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php endwhile; get_footer(); ?>