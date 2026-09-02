<?php
/**
 * Front-end template for an STS service page (/<slug>/).
 * Mirrors the converted service pages; every part is editable under STS Services.
 */

if (!defined('ABSPATH')) {
    exit;
}

$sts_page_id = get_queried_object_id();
$sts_data = sts_content_view_data(sts_content_service_for_page($sts_page_id), $sts_page_id);
$sts_contact = home_url('/kontakt/');
$sts_services = home_url('/service/');

get_header(); ?>

<div class="page-<?php echo esc_attr($sts_data['slug']); ?>">
    <div class="page-content">
        <div class="section-hero"><div class="<?php echo esc_attr($sts_data['hero_class']); ?> service-hero" id="hero">
  <div class="container">
    <span class="eyebrow"><?php echo esc_html($sts_data['eyebrow']); ?></span>
    <h1><?php echo esc_html($sts_data['hero_title']); ?></h1>
    <p><?php echo esc_html($sts_data['hero_text']); ?></p>
    <div style="margin-top:1.5rem">
      <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Få et gratis tilbud</a>
    </div>
  </div>
</div></div>
<div class="grid-layout-container section-content"><section class="section" id="content">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow"><?php echo esc_html($sts_data['process']['eyebrow']); ?></span>
        <h2><?php echo esc_html($sts_data['process']['title']); ?></h2>
      </div>
    </div>
    <div class="steps-grid">
<?php foreach ($sts_data['process']['steps'] as $sts_step) : ?>
      <div class="step-item"><div class="step-num"><?php echo esc_html($sts_step['number']); ?></div><h3><?php echo esc_html($sts_step['title']); ?></h3><p><?php echo esc_html($sts_step['description']); ?></p></div>
<?php endforeach; ?>
    </div>
  </div>
</section></div>
<div class="grid-layout-container section-cta"><section class="section section-tinted" id="cta">
  <div class="container content-grid">
    <div class="info-card">
      <span class="eyebrow">Hvad du får</span>
      <h2>Fordele ved at vælge STS ApS</h2>
      <ul class="list-check">
<?php foreach ($sts_data['benefits'] as $sts_benefit) : ?>
<li><?php echo esc_html($sts_benefit); ?></li>
<?php endforeach; ?>
      </ul>
      <div style="margin-top:1.5rem">
        <a class="btn btn-primary" href="<?php echo esc_url($sts_contact); ?>">Bestil et tilbud</a>
      </div>
    </div>
    <div class="hero-card service-page-media-card">
      <img class="service-page-main-image" src="<?php echo esc_url($sts_data['image']); ?>" alt="<?php echo esc_attr($sts_data['title']); ?>">
      <div style="margin-top:1rem">
        <div data-service-metrics=""></div>
      </div>
    </div>
  </div>
</section></div>
<?php if ($sts_data['show_about']) : ?>
<div class="section-content"><section class="section" id="content">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Om servicen</span>
                <h2><?php echo esc_html($sts_data['title']); ?></h2>
            </div>
        </div>
        <div style="background:white; border-radius:10px; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.08)">
            <?php
            $sts_body = $sts_data['content'];
            if (!preg_match('/<(p|h[1-6]|ul|ol|div|section|table)\b/i', $sts_body)) {
                $sts_body = wpautop($sts_body);
            }
            echo wp_kses_post($sts_body);
            ?>
        </div>
    </div>
</section></div>
<?php endif; ?>
<div class="grid-layout-container section-content"><section class="section section-tinted" id="content">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Alle ydelser</span>
                <h2>Vælg mellem STS Byg, STS Mal og STS Ren.</h2>
            </div>
        </div>
        <div class="pillar-grid" data-pillar-list-root="" data-path-prefix="../">
<article class="pillar-card pillar-byg" data-pillar-card-category="byg">
                <div class="pillar-head">
                    <h3>STS Byg</h3>
                </div>
                <p>Til byggepladser, nedrivning og praktisk bemanding med fokus på sikker drift og fremdrift.</p>
                <a class="pillar-cta" data-pillar-category-link="byg" href="<?php echo esc_url(home_url('/sts-byg/')); ?>">Se STS Byg</a>
            </article>

            <article class="pillar-card pillar-mal" data-pillar-card-category="mal">
                <div class="pillar-head">
                    <h3>STS Mal</h3>
                </div>
                <p>Til opfriskning, finish og løbende vedligehold med professionelle maler- og håndværksløsninger.</p>
                <a class="pillar-cta" data-pillar-category-link="mal" href="<?php echo esc_url(home_url('/sts-mal/')); ?>">Se STS Mal</a>
            </article>

            <article class="pillar-card pillar-ren" data-pillar-card-category="ren">
                <div class="pillar-head">
                    <h3>STS Ren</h3>
                </div>
                <p>Til daglig drift, rengøring og ejendomspleje med stabile aftaler og synlig kvalitet.</p>
                <a class="pillar-cta" data-pillar-category-link="ren" href="<?php echo esc_url(home_url('/sts-ren/')); ?>">Se STS Ren</a>
            </article>
        </div>
    </div>
</section></div>
<div class="section-cta"><section class="cta-band" id="cta">
  <div class="container">
    <h2>Klar til at starte?</h2>
    <p>Kontakt os i dag og få et konkret og uforpligtende tilbud tilpasset jeres behov.</p>
    <div class="cta-actions">
      <a class="btn-white" href="<?php echo esc_url($sts_contact); ?>">Kontakt os</a>
      <a class="btn-outline-white" href="<?php echo esc_url($sts_services); ?>">Se alle ydelser</a>
    </div>
  </div>
</section></div>
    </div>
</div>
<?php get_footer(); ?>
