<?php
/**
 * EC-CPT-018 — render template for WC-connected cart/checkout pages.
 *
 * WordPress's template hierarchy picks the theme's page-{slug}.php for
 * /cart and /checkout BY SLUG, regardless of the _wp_page_template meta
 * the connect flow clears. Those converted templates are static
 * snapshots that never call the_content(), so the [woocommerce_cart] /
 * [woocommerce_checkout] shortcode the connect wrote into the page would
 * never render. wpconvert_wc_connected_page_template() routes connected
 * pages here instead: theme chrome (header/footer) around the live
 * WooCommerce shortcode output.
 *
 * @package WPConvert_CPT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<div class="wpc-wc-connected-page woocommerce" style="max-width:1100px;margin:0 auto;padding:2rem 1.25rem 4rem;">
<?php
while ( have_posts() ) {
    the_post();
    ?>
    <h1 class="wpc-wc-connected-title" style="margin-bottom:1.5rem;"><?php the_title(); ?></h1>
    <div class="wpc-wc-connected-content">
        <?php the_content(); ?>
    </div>
    <?php
}
?>
</div>
<?php
get_footer();
