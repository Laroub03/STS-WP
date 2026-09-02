<?php
/**
 * Front-end template for a single project (/projekter/<slug>/).
 */

if (!defined('ABSPATH')) {
    exit;
}

$sts_data = sts_projects_view_data(get_queried_object_id());
$sts_facts = sts_projects_fact_rows($sts_data);
$sts_contact = home_url('/kontakt/');
$sts_archive = sts_projects_archive_url();

// The carousel always has something to show: gallery first, then the before/after
// shots, then the cover image as a last resort.
$sts_slides = array();
foreach ($sts_data['gallery'] as $sts_image) {
    $sts_slides[] = array('image' => $sts_image, 'caption' => $sts_data['title']);
}
if (!$sts_slides) {
    if ($sts_data['before_image']) {
        $sts_slides[] = array('image' => $sts_data['before_image'], 'caption' => 'Før');
    }
    if ($sts_data['after_image']) {
        $sts_slides[] = array('image' => $sts_data['after_image'], 'caption' => 'Efter');
    }
}
if (!$sts_slides) {
    $sts_slides[] = array('image' => $sts_data['cover'], 'caption' => $sts_data['title']);
}

get_header(); ?>

<div class="page-projekt">
  <div class="page-content">

    <div class="section-hero"><div class="<?php echo esc_attr($sts_data['hero_class']); ?> service-hero" id="hero">
      <div class="container">
        <span class="eyebrow"><?php echo esc_html($sts_data['category_label']); ?><?php echo $sts_data['location'] ? ' · ' . esc_html($sts_data['location']) : ''; ?></span>
        <h1><?php echo esc_html($sts_data['title']); ?></h1>
<?php if ($sts_data['excerpt']) : ?>
        <p><?php echo esc_html($sts_data['excerpt']); ?></p>
<?php endif; ?>
        <div style="margin-top:1.5rem">
          <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Få et gratis tilbud</a>
          <a class="btn-outline-white" href="<?php echo esc_url($sts_archive); ?>">Alle projekter</a>
        </div>
      </div>
    </div></div>

    <section class="section" id="content">
      <div class="container content-grid">
        <div class="info-card">
          <span class="eyebrow">Udførte ydelser</span>
          <h2>Det løste vi på opgaven</h2>
<?php if ($sts_data['services']) : ?>
          <ul class="list-check">
  <?php foreach ($sts_data['services'] as $sts_service) : ?>
            <li><?php echo esc_html($sts_service); ?></li>
  <?php endforeach; ?>
          </ul>
<?php endif; ?>
<?php if ($sts_facts) : ?>
          <dl class="project-facts">
  <?php foreach ($sts_facts as $sts_label => $sts_value) : ?>
            <div class="project-fact">
              <dt><?php echo esc_html($sts_label); ?></dt>
              <dd><?php echo esc_html($sts_value); ?></dd>
            </div>
  <?php endforeach; ?>
          </dl>
<?php endif; ?>
          <div style="margin-top:1.5rem">
            <a class="btn btn-primary" href="<?php echo esc_url($sts_contact); ?>">Bestil et tilbud</a>
          </div>
        </div>

        <?php /* data-wpc-carousel opts this out of the theme's universal carousel JS (which would display:none our slides); the theme's initHeroCarousel() drives it. */ ?>
        <div class="hero-card hero-carousel" data-hero-carousel data-wpc-carousel tabindex="0" role="group" aria-roledescription="karrusel" aria-label="Billeder fra projektet <?php echo esc_attr($sts_data['title']); ?>">
          <div class="hero-carousel-viewport">
<?php foreach ($sts_slides as $sts_index => $sts_slide) : ?>
            <figure class="hero-slide<?php echo 0 === $sts_index ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $sts_index ? 'false' : 'true'; ?>">
              <img src="<?php echo esc_url($sts_slide['image']); ?>" alt="<?php echo esc_attr($sts_data['title'] . ' – billede ' . ($sts_index + 1)); ?>" loading="<?php echo 0 === $sts_index ? 'eager' : 'lazy'; ?>">
              <figcaption><?php echo esc_html($sts_slide['caption']); ?></figcaption>
            </figure>
<?php endforeach; ?>
            <button type="button" class="hero-carousel-nav hero-carousel-prev" data-hero-prev aria-label="Forrige billede">&#8249;</button>
            <button type="button" class="hero-carousel-nav hero-carousel-next" data-hero-next aria-label="Næste billede">&#8250;</button>
          </div>
        </div>
      </div>
    </section>

<?php if ($sts_data['before_image'] && $sts_data['after_image']) : ?>
    <section class="section section-tinted" id="content">
      <div class="container">
        <div class="section-head">
          <div>
            <span class="eyebrow">Før &amp; efter</span>
            <h2>Resultatet taler for sig selv.</h2>
          </div>
        </div>
        <div class="project-compare">
          <figure class="project-compare-item">
            <img src="<?php echo esc_url($sts_data['before_image']); ?>" alt="<?php echo esc_attr($sts_data['title'] . ' – før'); ?>" loading="lazy">
            <figcaption><span class="project-compare-label is-before">Før</span></figcaption>
          </figure>
          <figure class="project-compare-item">
            <img src="<?php echo esc_url($sts_data['after_image']); ?>" alt="<?php echo esc_attr($sts_data['title'] . ' – efter'); ?>" loading="lazy">
            <figcaption><span class="project-compare-label is-after">Efter</span></figcaption>
          </figure>
        </div>
      </div>
    </section>
<?php endif; ?>

<?php if (trim($sts_data['content']) !== '' || $sts_data['materials']) : ?>
    <section class="section" id="content">
      <div class="container">
        <div class="section-head">
          <div>
            <span class="eyebrow">Om projektet</span>
            <h2><?php echo esc_html($sts_data['title']); ?></h2>
          </div>
        </div>
        <div class="project-body">
  <?php if (trim($sts_data['content']) !== '') : ?>
          <div class="project-body-text"><?php echo wp_kses_post(wpautop($sts_data['content'])); ?></div>
  <?php endif; ?>
  <?php if ($sts_data['materials']) : ?>
          <div class="info-card project-materials">
            <span class="eyebrow">Materialer &amp; produkter</span>
            <ul class="list-check">
    <?php foreach ($sts_data['materials'] as $sts_material) : ?>
              <li><?php echo esc_html($sts_material); ?></li>
    <?php endforeach; ?>
            </ul>
          </div>
  <?php endif; ?>
        </div>
      </div>
    </section>
<?php endif; ?>

<?php
$sts_related = sts_projects_get_published();
$sts_related = array_values(array_filter($sts_related, function ($sts_item) use ($sts_data) {
    return $sts_item->ID !== $sts_data['id'];
}));
$sts_related = array_slice($sts_related, 0, 3);
if ($sts_related) : ?>
    <section class="section section-tinted" id="content">
      <div class="container">
        <div class="section-head">
          <div>
            <span class="eyebrow">Flere projekter</span>
            <h2>Se andre opgaver vi har løst.</h2>
          </div>
        </div>
        <div class="project-grid">
  <?php foreach ($sts_related as $sts_item) :
      $sts_item_data = sts_projects_view_data($sts_item); ?>
          <article class="project-card">
            <a class="project-card-media" href="<?php echo esc_url($sts_item_data['url']); ?>" aria-label="<?php echo esc_attr($sts_item_data['title']); ?>">
              <img src="<?php echo esc_url($sts_item_data['cover']); ?>" alt="<?php echo esc_attr($sts_item_data['title']); ?>" loading="lazy">
              <span class="project-card-tag"><?php echo esc_html($sts_item_data['category_label']); ?></span>
            </a>
            <div class="project-card-body">
    <?php if ($sts_item_data['location']) : ?>
              <span class="project-card-location"><?php echo esc_html($sts_item_data['location']); ?></span>
    <?php endif; ?>
              <h3><a href="<?php echo esc_url($sts_item_data['url']); ?>"><?php echo esc_html($sts_item_data['title']); ?></a></h3>
              <div class="project-card-foot">
                <a class="pillar-cta" href="<?php echo esc_url($sts_item_data['url']); ?>">Se projektet</a>
              </div>
            </div>
          </article>
  <?php endforeach; ?>
        </div>
      </div>
    </section>
<?php endif; ?>

    <section class="cta-band" id="cta">
      <div class="container">
        <h2>Skal vi løse en lignende opgave for jer?</h2>
        <p>Kontakt os i dag og få et konkret og uforpligtende tilbud tilpasset jeres behov.</p>
        <div class="cta-actions">
          <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Kontakt os</a>
          <a class="btn-outline-white" href="<?php echo esc_url($sts_archive); ?>">Se alle projekter</a>
        </div>
      </div>
    </section>

  </div>
</div>
<?php get_footer(); ?>
