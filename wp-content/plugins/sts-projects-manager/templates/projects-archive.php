<?php
/**
 * Front-end template for the projects overview page (/projekter/).
 */

if (!defined('ABSPATH')) {
    exit;
}

$sts_projects = sts_projects_get_published();
usort($sts_projects, function ($a, $b) {
    $a_featured = get_post_meta($a->ID, '_sts_project_featured', true) === '1' ? 0 : 1;
    $b_featured = get_post_meta($b->ID, '_sts_project_featured', true) === '1' ? 0 : 1;
    if ($a_featured !== $b_featured) {
        return $a_featured - $b_featured;
    }
    if ($a->menu_order !== $b->menu_order) {
        return $a->menu_order - $b->menu_order;
    }
    return strcmp($b->post_date, $a->post_date);
});

$sts_used_categories = array();
foreach ($sts_projects as $sts_project) {
    $sts_category = get_post_meta($sts_project->ID, '_sts_project_category', true);
    if (array_key_exists($sts_category, sts_projects_categories())) {
        $sts_used_categories[$sts_category] = sts_projects_category_label($sts_category);
    }
}

$sts_contact = home_url('/kontakt/');
$sts_services_url = home_url('/service/');

get_header(); ?>

<div class="page-projekter">
  <div class="page-content">

    <div class="section-hero"><div class="hero-blue service-hero" id="hero">
      <div class="container">
        <span class="eyebrow">Projekter</span>
        <h1>Se hvad vi har bygget, malet og gjort rent.</h1>
        <p>Et udvalg af opgaver STS ApS har gennemført for virksomheder, ejendomme og byggeprojekter i hele Danmark – med før- og efterbilleder, udførte ydelser og resultatet.</p>
        <div style="margin-top:1.5rem">
          <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Få et gratis tilbud</a>
        </div>
      </div>
    </div></div>

    <section class="section" id="content">
      <div class="container">
        <div class="section-head">
          <div>
            <span class="eyebrow">Udvalgte opgaver</span>
            <h2>Gennemførte projekter fra hele landet.</h2>
          </div>
        </div>

<?php if (!$sts_projects) : ?>
        <div class="info-card">
          <h3>Projekterne er på vej.</h3>
          <p>Vi er ved at samle billeder og beskrivelser fra vores seneste opgaver. Kontakt os imens for referencer fra opgaver, der ligner jeres.</p>
          <div style="margin-top:1.5rem">
            <a class="btn btn-primary" href="<?php echo esc_url($sts_contact); ?>">Kontakt os</a>
          </div>
        </div>
<?php else : ?>
  <?php if (count($sts_used_categories) > 1) : ?>
        <div class="project-filter" data-project-filter>
          <button type="button" class="project-filter-btn is-active" data-project-filter-value="all">Alle projekter</button>
    <?php foreach ($sts_used_categories as $sts_key => $sts_label) : ?>
          <button type="button" class="project-filter-btn" data-project-filter-value="<?php echo esc_attr($sts_key); ?>"><?php echo esc_html($sts_label); ?></button>
    <?php endforeach; ?>
        </div>
  <?php endif; ?>

        <div class="project-grid" data-project-grid>
  <?php foreach ($sts_projects as $sts_project) :
      $sts_data = sts_projects_view_data($sts_project);
      $sts_image_count = count($sts_data['gallery']);
      ?>
          <article class="project-card" data-project-category="<?php echo esc_attr($sts_data['category']); ?>">
            <a class="project-card-media" href="<?php echo esc_url($sts_data['url']); ?>" aria-label="<?php echo esc_attr($sts_data['title']); ?>">
              <img src="<?php echo esc_url($sts_data['cover']); ?>" alt="<?php echo esc_attr($sts_data['title']); ?>" loading="lazy">
              <span class="project-card-tag"><?php echo esc_html($sts_data['category_label']); ?></span>
      <?php if ($sts_data['before_image'] && $sts_data['after_image']) : ?>
              <span class="project-card-badge">Før &amp; efter</span>
      <?php endif; ?>
            </a>
            <div class="project-card-body">
      <?php if ($sts_data['location']) : ?>
              <span class="project-card-location"><?php echo esc_html($sts_data['location']); ?></span>
      <?php endif; ?>
              <h3><a href="<?php echo esc_url($sts_data['url']); ?>"><?php echo esc_html($sts_data['title']); ?></a></h3>
      <?php if ($sts_data['excerpt']) : ?>
              <p><?php echo esc_html($sts_data['excerpt']); ?></p>
      <?php endif; ?>
      <?php if ($sts_data['services']) : ?>
              <ul class="project-chips">
        <?php foreach (array_slice($sts_data['services'], 0, 3) as $sts_service) : ?>
                <li><?php echo esc_html($sts_service); ?></li>
        <?php endforeach; ?>
        <?php if (count($sts_data['services']) > 3) : ?>
                <li class="project-chip-more">+<?php echo (int) (count($sts_data['services']) - 3); ?> mere</li>
        <?php endif; ?>
              </ul>
      <?php endif; ?>
              <div class="project-card-foot">
                <a class="pillar-cta" href="<?php echo esc_url($sts_data['url']); ?>">Se projektet</a>
      <?php if ($sts_image_count) : ?>
                <span class="project-card-count"><?php echo (int) $sts_image_count; ?> billeder</span>
      <?php endif; ?>
              </div>
            </div>
          </article>
  <?php endforeach; ?>
        </div>
        <p class="project-empty-note" data-project-empty hidden>Ingen projekter i denne kategori endnu.</p>
<?php endif; ?>
      </div>
    </section>

    <section class="cta-band" id="cta">
      <div class="container">
        <h2>Skal jeres projekt være det næste?</h2>
        <p>Fortæl os om opgaven, så vender vi tilbage med en konkret plan og et uforpligtende tilbud.</p>
        <div class="cta-actions">
          <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Kontakt os</a>
          <a class="btn-outline-white" href="<?php echo esc_url($sts_services_url); ?>">Se alle ydelser</a>
        </div>
      </div>
    </section>

  </div>
</div>
<?php get_footer(); ?>
