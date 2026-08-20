<?php
/*
Template Name: Header
*/
get_header(); ?>

<div class="page-components-header">
    <div class="page-content">
        <div class="section-content"><div class="container" id="content">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="STS ApS – Gå til forsiden">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-sts-rgb.png" alt="STS ApS logo" width="140" height="auto" data-wpc-id="wpc_a5e8677094" data-wpc-editable="image">
    </a>
    <button class="nav-toggle" type="button" aria-label="Åbn menu" aria-expanded="false">☰</button>
    
  </div></div>
    </div>
</div>
<?php get_footer(); ?>
