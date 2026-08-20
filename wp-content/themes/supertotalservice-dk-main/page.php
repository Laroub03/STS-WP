<?php
/**
 * page.php — Generic page template (fallback)
 *
 * Automatically resolves the correct page-{slug}.php template for hierarchical
 * pages (e.g. /cypress-tx/dental-implants -> page-cypress-tx-dental-implants.php).
 *
 * EC-PAGE-FALLBACK-002: get_page_uri() with no argument relies on the global post,
 * which is not set until the Loop runs. Resolving the URI from get_queried_object_id()
 * first fixes Starter /blog/1 (page-blog-1.php) showing only the auto-generated stub.
 *
 * EC-PAGE-FALLBACK-003: get_page_uri( $id ) can still return only the leaf slug (e.g. "1")
 * when parent/child linkage is wrong or WP returns a shortened path — we then look for
 * page-1.php (missing) and fall through to the stub. Also try flattening $wp->request
 * (rewrite path like "blog/1") so page-blog-1.php is found.
 *
 * EC-PAGE-FALLBACK-004: Prefer wpconvert_page_hier_path( $page_id ) (ancestor chain) and
 * the raw REQUEST_URI path (subdirectory-safe) when $wp->request is empty (some hosts).
 */
$page_id = get_queried_object_id();
$candidates = array();
if ($page_id && get_post_type($page_id) === 'page') {
    if (function_exists('wpconvert_page_hier_path')) {
        $hp = wpconvert_page_hier_path($page_id);
        if ($hp !== '') {
            $candidates[] = str_replace('/', '-', $hp);
            // EC-PAGE-SLUG-002: blog/1-2 still resolves page-blog-1.php when routes.json has /blog/1
            $parts_h = explode('/', $hp);
            $last_h = array_pop($parts_h);
            if ($last_h !== null && $last_h !== '') {
                $routes_pg = get_template_directory() . '/assets/data/routes.json';
                if (file_exists($routes_pg) && preg_match('/^(\d+)-\d+$/', $last_h, $mh)) {
                    $routes_pg_data = json_decode(file_get_contents($routes_pg), true);
                    $try_rel_h = (count($parts_h) ? implode('/', $parts_h) . '/' : '') . $mh[1];
                    if (is_array($routes_pg_data) && isset($routes_pg_data['/' . $try_rel_h])) {
                        $candidates[] = str_replace('/', '-', $try_rel_h);
                    }
                }
            }
        }
    }
    $u = trim(get_page_uri($page_id), '/');
    if ($u !== '') {
        $candidates[] = str_replace('/', '-', $u);
    }
}
global $wp;
if (isset($wp->request) && is_string($wp->request) && $wp->request !== '') {
    $candidates[] = str_replace('/', '-', trim($wp->request, '/'));
}
if (!empty($_SERVER['REQUEST_URI'])) {
    $raw_path = wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_string($raw_path)) {
        $rp = trim($raw_path, '/');
        $home_p = trim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
        if ($home_p !== '' && ($rp === $home_p || strpos($rp, $home_p . '/') === 0)) {
            $rp = ($rp === $home_p) ? '' : substr($rp, strlen($home_p) + 1);
        }
        if ($rp !== '') {
            $candidates[] = str_replace('/', '-', $rp);
        }
    }
}
$candidates = array_values(array_unique(array_filter($candidates)));

foreach ($candidates as $flat_slug) {
    $template = 'page-' . $flat_slug . '.php';
    if ($flat_slug !== '' && file_exists(get_template_directory() . '/' . $template)) {
        include(get_template_directory() . '/' . $template);
        return;
    }
}

// Fallback: render page content with header/footer
get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="container mx-auto px-4 py-12 max-w-4xl">
          <h1 class="text-3xl md:text-4xl font-bold mb-6"><?php the_title(); ?></h1>
          <div class="prose max-w-none">
            <?php the_content(); ?>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  </main>
</div>

<?php get_footer(); ?>