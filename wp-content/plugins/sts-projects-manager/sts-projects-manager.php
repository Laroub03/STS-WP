<?php
/**
 * Plugin Name: STS Projects Manager
 * Description: Administrerer STS-projekter og projektsiden (/projekter/) med billedkarrusel, før/efter-billeder og projektdetaljer.
 * Version: 1.0.0
 * Author: STS ApS
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('STS_PROJECTS_VERSION', '1.0.0');
define('STS_PROJECTS_PATH', plugin_dir_path(__FILE__));
define('STS_PROJECTS_URL', plugin_dir_url(__FILE__));
define('STS_PROJECTS_SLUG', 'projekter');
define('STS_PROJECTS_TEMPLATE', 'sts-projects-archive');

require_once STS_PROJECTS_PATH . 'includes/helpers.php';
if (is_admin()) {
    require_once STS_PROJECTS_PATH . 'includes/admin.php';
}

/* ── Post type ─────────────────────────────────────────────────── */

/**
 * Public so /projekter/<slug>/ resolves, but without the default admin UI —
 * projects are edited through the custom "STS Projekter" screens.
 */
function sts_projects_register_post_type() {
    register_post_type('sts_project', array(
        'labels' => array(
            'name' => 'Projekter',
            'singular_name' => 'Projekt',
        ),
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui' => false,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'show_in_rest' => false,
        'supports' => array('title', 'editor', 'excerpt', 'page-attributes'),
        'has_archive' => false,
        'rewrite' => array('slug' => STS_PROJECTS_SLUG, 'with_front' => false),
    ));
}
add_action('init', 'sts_projects_register_post_type');

/* ── Archive page + templates ──────────────────────────────────── */

function sts_projects_register_template($templates) {
    $templates[STS_PROJECTS_TEMPLATE] = 'STS Projektoversigt';
    return $templates;
}
add_filter('theme_page_templates', 'sts_projects_register_template');

function sts_projects_template_include($template) {
    if (is_singular('sts_project')) {
        $file = STS_PROJECTS_PATH . 'templates/project-single.php';
        if (is_readable($file)) {
            return $file;
        }
    }
    if (is_page() && sts_projects_page_uses_template(get_queried_object_id())) {
        $file = STS_PROJECTS_PATH . 'templates/projects-archive.php';
        if (is_readable($file)) {
            return $file;
        }
    }
    return $template;
}
add_filter('template_include', 'sts_projects_template_include', 100);

function sts_projects_page_uses_template($page_id) {
    return get_post_meta($page_id, '_sts_project_template', true) === '1'
        || get_page_template_slug($page_id) === STS_PROJECTS_TEMPLATE;
}

/**
 * The theme rewrites _wp_page_template back to its generated page-<slug>.php on
 * every repair pass — keep the projects page pointed at our template.
 */
function sts_projects_protect_page_template($check, $object_id, $meta_key, $meta_value) {
    if ($meta_key !== '_wp_page_template' || $meta_value === STS_PROJECTS_TEMPLATE) {
        return $check;
    }
    if (get_post_meta($object_id, '_sts_project_template', true) === '1') {
        return false;
    }
    return $check;
}
add_filter('update_post_metadata', 'sts_projects_protect_page_template', 10, 4);

function sts_projects_ensure_archive_page() {
    $page = sts_projects_archive_page();
    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Projekter',
            'post_name' => STS_PROJECTS_SLUG,
            'post_content' => '',
        ), true);
        if (is_wp_error($page_id)) {
            return 0;
        }
    } else {
        $page_id = $page->ID;
    }
    update_option('sts_projects_page_id', $page_id);
    update_post_meta($page_id, '_sts_project_template', '1');
    update_post_meta($page_id, '_wp_page_template', STS_PROJECTS_TEMPLATE);
    return $page_id;
}

function sts_projects_activate() {
    sts_projects_register_post_type();
    sts_projects_ensure_archive_page();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sts_projects_activate');

function sts_projects_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'sts_projects_deactivate');

/**
 * Self-heal after a theme repair pass or a plugin update replaced the files
 * without the activation hook running again.
 */
function sts_projects_maybe_upgrade() {
    if (get_option('sts_projects_installed') === STS_PROJECTS_VERSION) {
        return;
    }
    sts_projects_ensure_archive_page();
    flush_rewrite_rules();
    update_option('sts_projects_installed', STS_PROJECTS_VERSION);
}
add_action('admin_init', 'sts_projects_maybe_upgrade');

/* ── Navigation ────────────────────────────────────────────────── */

/**
 * The primary menu is imported from the theme's menus.json. The JSON now lists
 * "Projekter", but a menu imported before that still won't have it, so inject a
 * synthetic item right after "Service" when it is missing.
 */
function sts_projects_inject_nav_item($items, $args) {
    $location = is_object($args) && !empty($args->theme_location) ? $args->theme_location : '';
    if ($location !== 'primary' || !is_array($items) || !$items) {
        return $items;
    }
    $url = sts_projects_archive_url();
    foreach ($items as $item) {
        $title = strtolower(trim((string) ($item->title ?? '')));
        if ($title === 'projekter' || rtrim((string) ($item->url ?? ''), '/') === rtrim($url, '/')) {
            return $items;
        }
    }

    $new = new stdClass();
    $new->ID = 0;
    $new->db_id = 0;
    $new->menu_item_parent = 0;
    $new->object_id = (int) get_option('sts_projects_page_id');
    $new->object = 'custom';
    $new->type = 'custom';
    $new->type_label = 'Brugerdefineret link';
    $new->title = 'Projekter';
    $new->url = $url;
    $new->target = '';
    $new->attr_title = '';
    $new->description = '';
    $new->xfn = '';
    $new->post_parent = 0;
    $new->menu_order = 0;
    $new->classes = array('');
    $new->current = false;
    $new->current_item_ancestor = false;
    $new->current_item_parent = false;
    if (is_singular('sts_project') || (is_page() && sts_projects_page_uses_template(get_queried_object_id()))) {
        $new->current = true;
        $new->classes[] = 'current-menu-item';
    }

    $position = -1;
    foreach ($items as $index => $item) {
        $title = strtolower(trim((string) ($item->title ?? '')));
        if ($title === 'service') {
            $position = $index + 1;
            break;
        }
        if ($title === 'nyheder') {
            $position = $index;
            break;
        }
    }
    if ($position < 0) {
        $items[] = $new;
        return array_values($items);
    }
    array_splice($items, $position, 0, array($new));
    return array_values($items);
}
add_filter('wp_nav_menu_objects', 'sts_projects_inject_nav_item', 10, 2);

/* ── Front-end assets ──────────────────────────────────────────── */

function sts_projects_enqueue_assets() {
    if (!is_singular('sts_project') && !(is_page() && sts_projects_page_uses_template(get_queried_object_id()))) {
        return;
    }
    $css = STS_PROJECTS_PATH . 'assets/css/projects.css';
    wp_enqueue_style(
        'sts-projects',
        STS_PROJECTS_URL . 'assets/css/projects.css',
        array(),
        file_exists($css) ? filemtime($css) : STS_PROJECTS_VERSION
    );
    $js = STS_PROJECTS_PATH . 'assets/js/projects.js';
    wp_enqueue_script(
        'sts-projects',
        STS_PROJECTS_URL . 'assets/js/projects.js',
        array(),
        file_exists($js) ? filemtime($js) : STS_PROJECTS_VERSION,
        true
    );
}
// Priority 20 so the plugin stylesheet is queued after the theme's globbed CSS.
add_action('wp_enqueue_scripts', 'sts_projects_enqueue_assets', 20);

/**
 * SEO/browser title for the CPT singles, which have no page of their own.
 */
function sts_projects_document_title($title) {
    if (is_singular('sts_project')) {
        $title['title'] = get_the_title() . ' | Projekter';
    }
    return $title;
}
add_filter('document_title_parts', 'sts_projects_document_title');
