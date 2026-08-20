<?php
/*
Template Name: Serviceydelser | Erhvervsrengøring, håndværk og byggepladsservice - STS ApS
*/
get_header(); ?>

<div class="page-service">
    <div class="page-content">
        <div class="section-hero"><div class="hub-hero" id="hero">
        <div class="container">
          <span class="eyebrow" data-wpc-id="wpc_140d64d2fb" data-wpc-editable="text">🏢 Professionelle serviceydelser</span>
          <h1 data-wpc-id="wpc_557ae7fe0b" data-wpc-editable="heading">En samlet partner for alt inden for drift, service og bygge.</h1>
          <p data-wpc-id="wpc_8cb71c10cd" data-wpc-editable="text">Vi dækker erhvervsrengøring, håndværk, byggepladsservice, asbestsanering og meget mere - tilpasset præcist til jeres behov.</p>
          <div class="hub-stat-row">
            <div class="hub-stat" data-wpc-id="wpc_8d4e988d15" data-wpc-editable="heading"><strong>18+</strong><span>Serviceydelser</span></div>
            <div class="hub-stat" data-wpc-id="wpc_06fecc04f6" data-wpc-editable="heading"><strong>200+</strong><span>Virksomheder betjent</span></div>
            <div class="hub-stat" data-wpc-id="wpc_dca2931a9d" data-wpc-editable="heading"><strong>20</strong><span>År i branchen</span></div>
            <div class="hub-stat" data-wpc-id="wpc_7e05cc9e4f" data-wpc-editable="heading"><strong>24/7</strong><span>Beredskab</span></div>
          </div>
        </div>
      </div></div>
<div class="grid-layout-container section-content"><section class="section" id="content" data-wpc-id="wpc_939e62c566" data-wpc-editable="section">
        <div class="container">
          <div class="section-head">
            <div>
              <span class="eyebrow" data-wpc-id="wpc_ffa5aafb83" data-wpc-editable="text">Vores serviceunivers</span>
              <h2 data-wpc-id="wpc_1e4f281e61" data-wpc-editable="heading">Vælg mellem STS Byg, STS Mal og STS Ren.</h2>
            </div>
          </div>
          <div id="service-pillars-directory" class="pillar-grid" data-pillar-list-root="" data-path-prefix="../">
            <article class="pillar-card pillar-byg" data-pillar-card-category="byg">
              <div class="pillar-head">
                <span class="pillar-icon">🏗️</span>
                <h3 data-wpc-id="wpc_e476499724" data-wpc-editable="heading">STS Byg</h3>
              </div>
              <p data-wpc-id="wpc_e6521ab4b6" data-wpc-editable="text">Til byggepladser, nedrivning og praktisk bemanding med fokus på sikker drift og fremdrift.</p>
              <a class="pillar-cta" data-pillar-category-link="byg" href="<?php echo home_url('/sts-byg/'); ?>" data-wpc-id="wpc_bf1aa0a70d" data-wpc-editable="button">Se STS Byg</a>
            </article>

            <article class="pillar-card pillar-mal" data-pillar-card-category="mal">
              <div class="pillar-head">
                <span class="pillar-icon">🎨</span>
                <h3 data-wpc-id="wpc_9fb4640e6c" data-wpc-editable="heading">STS Mal</h3>
              </div>
              <p data-wpc-id="wpc_e05368f22f" data-wpc-editable="text">Til opfriskning, finish og løbende vedligehold med professionelle maler- og håndværksløsninger.</p>
              <a class="pillar-cta" data-pillar-category-link="mal" href="<?php echo home_url('/sts-mal/'); ?>" data-wpc-id="wpc_3083158648" data-wpc-editable="button">Se STS Mal</a>
            </article>

            <article class="pillar-card pillar-ren" data-pillar-card-category="ren">
              <div class="pillar-head">
                <span class="pillar-icon">🫧</span>
                <h3 data-wpc-id="wpc_e8d7c7c18a" data-wpc-editable="heading">STS Ren</h3>
              </div>
              <p data-wpc-id="wpc_d4f4a31c3e" data-wpc-editable="text">Til daglig drift, rengøring og ejendomspleje med stabile aftaler og synlig kvalitet.</p>
              <a class="pillar-cta" data-pillar-category-link="ren" href="<?php echo home_url('/sts-ren/'); ?>" data-wpc-id="wpc_b332e71e40" data-wpc-editable="button">Se STS Ren</a>
            </article>
          </div>
          <?php
          $sts_services = get_posts(array(
              'post_type' => 'sts_service',
              'post_status' => 'publish',
              'numberposts' => -1,
              'orderby' => 'title',
              'order' => 'ASC',
          ));
          if ($sts_services) :
          ?>
          <div class="service-directory-grid" aria-label="Alle serviceydelser">
            <?php foreach ($sts_services as $sts_service) :
                $sts_image = function_exists('sts_content_service_image') ? sts_content_service_image($sts_service->ID) : '';
                $sts_icon = get_post_meta($sts_service->ID, '_sts_service_icon', true);
                $sts_description = get_the_excerpt($sts_service);
            ?>
              <article class="service-directory-card">
                <?php if ($sts_image) : ?><img src="<?php echo esc_url($sts_image); ?>" alt="<?php echo esc_attr($sts_service->post_title); ?>" loading="lazy"><?php endif; ?>
                <div class="service-card-body">
                  <div class="service-tag-icon"><?php echo esc_html($sts_icon ?: '🔧'); ?></div>
                  <h3><?php echo esc_html($sts_service->post_title); ?></h3>
                  <p><?php echo esc_html($sts_description); ?></p>
                  <a class="service-link" href="<?php echo esc_url(get_permalink($sts_service)); ?>">Læs mere</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </section></div>
<div class="section-cta"><section class="cta-band" id="cta" data-wpc-id="wpc_9a9062ffd3" data-wpc-editable="section">
        <div class="container">
          <h2 data-wpc-id="wpc_33ae7ec8b7" data-wpc-editable="heading">Skal vi løse jeres næste opgave?</h2>
          <p data-wpc-id="wpc_ef5e9e6acb" data-wpc-editable="text">Send en forespørgsel og få et tilbud inden for en arbejdsdag. Helt gratis og uforpligtende.</p>
          <div class="cta-actions">
            <a class="btn-white" href="<?php echo home_url('/kontakt/'); ?>" data-wpc-id="wpc_e8e7b098ce" data-wpc-editable="button">Kontakt os nu</a>
            <a class="btn-outline-white" href="<?php echo home_url('/hvem-er-sts/'); ?>" data-wpc-id="wpc_647de1b862" data-wpc-editable="button">Læs om os</a>
          </div>
        </div>
      </section></div>
    </div>
</div>
<?php get_footer(); ?>
