<?php
/**
 * Plugin Name: STS Content Manager
 * Description: Administrerer STS-serviceydelser og de rigtige servicesider (/&lt;slug&gt;/) via en genbrugelig sidetemplate.
 * Version: 2.0.0
 * Author: STS ApS
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('STS_CONTENT_VERSION', '2.1.1');
define('STS_CONTENT_PATH', plugin_dir_path(__FILE__));
define('STS_CONTENT_TEMPLATE', 'sts-service-page');

require_once STS_CONTENT_PATH . 'includes/helpers.php';
require_once STS_CONTENT_PATH . 'includes/theme-import.php';
if (is_admin()) {
    require_once STS_CONTENT_PATH . 'includes/admin.php';
}

/**
 * The CPT is a data store only — the public URL is the real page at /<slug>/.
 */
function sts_content_register_service_type() {
    register_post_type('sts_service', array(
        'labels' => array(
            'name' => 'Serviceydelser',
            'singular_name' => 'Serviceydelse',
        ),
        'public' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'show_ui' => false,
        'show_in_menu' => false,
        'show_in_rest' => false,
        'supports' => array('title', 'editor', 'excerpt'),
        'has_archive' => false,
        'rewrite' => false,
    ));
}
add_action('init', 'sts_content_register_service_type');

/* ── Page template ─────────────────────────────────────────────── */

function sts_content_register_template($templates) {
    $templates[STS_CONTENT_TEMPLATE] = 'STS Serviceside';
    return $templates;
}
add_filter('theme_page_templates', 'sts_content_register_template');

function sts_content_template_include($template) {
    if (is_page() && sts_content_page_uses_template(get_queried_object_id())) {
        $file = STS_CONTENT_PATH . 'templates/service-page.php';
        if (is_readable($file)) {
            return $file;
        }
    }
    return $template;
}
add_filter('template_include', 'sts_content_template_include', 100);

function sts_content_page_uses_template($page_id) {
    return get_post_meta($page_id, '_sts_service_template', true) === '1'
        || get_page_template_slug($page_id) === STS_CONTENT_TEMPLATE;
}

function sts_content_set_page_template($page_id, $enabled) {
    if ($enabled) {
        update_post_meta($page_id, '_sts_service_template', '1');
        update_post_meta($page_id, '_wp_page_template', STS_CONTENT_TEMPLATE);
    } else {
        delete_post_meta($page_id, '_sts_service_template');
        if (get_page_template_slug($page_id) === STS_CONTENT_TEMPLATE) {
            delete_post_meta($page_id, '_wp_page_template');
        }
    }
}

/**
 * The theme rewrites _wp_page_template back to its generated page-<slug>.php on
 * every repair pass — keep our own pages pointed at the service template.
 */
function sts_content_protect_page_template($check, $object_id, $meta_key, $meta_value) {
    if ($meta_key !== '_wp_page_template' || $meta_value === STS_CONTENT_TEMPLATE) {
        return $check;
    }
    if (get_post_meta($object_id, '_sts_service_template', true) === '1') {
        return false;
    }
    return $check;
}
add_filter('update_post_metadata', 'sts_content_protect_page_template', 10, 4);

function sts_content_flag_template_on_save($post_id) {
    if (get_page_template_slug($post_id) === STS_CONTENT_TEMPLATE) {
        update_post_meta($post_id, '_sts_service_template', '1');
    }
}
add_action('save_post_page', 'sts_content_flag_template_on_save');

/**
 * Old CPT permalinks (/ydelse/<slug>/) now point at the real page.
 */
function sts_content_redirect_legacy_urls() {
    if (is_admin()) {
        return;
    }
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if (strpos($path, 'ydelse/') !== 0) {
        return;
    }
    $slug = sanitize_title(substr($path, strlen('ydelse/')));
    if ($slug === '') {
        return;
    }
    $service = get_page_by_path($slug, OBJECT, 'sts_service');
    $page = get_page_by_path($slug);
    if (!$service && !$page) {
        return;
    }
    wp_safe_redirect($service ? sts_content_service_url($service) : get_permalink($page), 301);
    exit;
}
add_action('template_redirect', 'sts_content_redirect_legacy_urls', 1);

/* ── Service <-> page binding ──────────────────────────────────── */

/**
 * Ensures the service has a real front-end page, creating it with the STS
 * service template when none exists.
 */
function sts_content_sync_service_page($service_id) {
    $service = get_post($service_id);
    if (!$service || $service->post_type !== 'sts_service') {
        return 0;
    }
    $page = sts_content_service_page($service);
    if ($page) {
        $page_id = $page->ID;
        if ($page->post_name !== $service->post_name) {
            wp_update_post(array('ID' => $page_id, 'post_name' => $service->post_name));
        }
    } else {
        $page_id = wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $service->post_title,
            'post_name' => $service->post_name,
            'post_content' => '',
        ), true);
        if (is_wp_error($page_id)) {
            return 0;
        }
    }
    sts_content_set_page_template($page_id, true);
    update_post_meta($page_id, '_sts_service_id', $service_id);
    update_post_meta($service_id, '_sts_service_page_id', $page_id);
    return $page_id;
}

/* ── Import from the original static CMS ───────────────────────── */

/**
 * One-off migration: bind every service to its real page, copy the converted
 * page content into the service fields and switch the page to the template.
 */
function sts_content_maybe_link_pages() {
    if (get_option('sts_content_pages_linked') === STS_CONTENT_VERSION) {
        return;
    }
    foreach (sts_content_get_services() as $service) {
        sts_content_seed_service_from_theme($service->ID, true);
        sts_content_sync_service_page($service->ID);
    }
    update_option('sts_content_pages_linked', STS_CONTENT_VERSION);
}
add_action('admin_init', 'sts_content_maybe_link_pages');

function sts_content_original_file($name) {
    $path = ABSPATH . 'supertotalservice.dk/data/' . $name;
    return is_readable($path) ? $path : '';
}

/**
 * Stock images from the original CMS already exist as theme assets, so those
 * paths resolve to the default image instead of a hard-coded custom URL.
 */
function sts_content_import_image($path) {
    $path = trim((string) $path);
    if ($path === '' || strpos($path, '/media/uploads/stock-images/') === 0) {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return home_url('/supertotalservice.dk/' . ltrim($path, '/'));
}

function sts_content_import_services() {
    $file = sts_content_original_file('services.json');
    if (!$file) {
        return 0;
    }
    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data) || empty($data['services']) || !is_array($data['services'])) {
        return 0;
    }
    $count = 0;
    foreach ($data['services'] as $service) {
        $slug = sts_content_slug($service['slug'] ?? $service['title'] ?? '');
        if ($slug === '') {
            continue;
        }
        $existing = get_page_by_path($slug, OBJECT, 'sts_service');
        $post = array(
            'post_type' => 'sts_service',
            'post_status' => 'publish',
            'post_name' => $slug,
            'post_title' => sanitize_text_field($service['title'] ?? ''),
            'post_excerpt' => sanitize_textarea_field($service['description'] ?? ''),
            'post_content' => wp_kses_post($service['full_description'] ?? ''),
        );
        if ($existing) {
            $post['ID'] = $existing->ID;
            $id = wp_update_post($post, true);
        } else {
            $id = wp_insert_post($post, true);
        }
        if (is_wp_error($id)) {
            continue;
        }
        update_post_meta($id, '_sts_service_source_id', sanitize_text_field($service['id'] ?? ''));
        update_post_meta($id, '_sts_service_icon', sanitize_text_field($service['icon'] ?? ''));
        update_post_meta($id, '_sts_service_category', sanitize_key($service['category'] ?? ''));
        update_post_meta($id, '_sts_service_hero_title', sanitize_text_field($service['hero_title'] ?? ''));
        update_post_meta($id, '_sts_service_image', esc_url_raw(sts_content_import_image($service['image'] ?? '')));
        update_post_meta($id, '_sts_service_benefits', array_map('sanitize_text_field', (array) ($service['benefits'] ?? array())));

        $source_process = (array) ($service['process_section'] ?? array());
        $process = array(
            'eyebrow' => sanitize_text_field($source_process['eyebrow'] ?? ''),
            'title' => sanitize_text_field($source_process['title'] ?? ''),
            'steps' => array(),
        );
        foreach ((array) ($source_process['steps'] ?? array()) as $source_step) {
            $process['steps'][] = array(
                'title' => sanitize_text_field($source_step['title'] ?? ''),
                'description' => sanitize_textarea_field($source_step['description'] ?? ''),
            );
        }
        update_post_meta($id, '_sts_service_process', $process);

        sts_content_sync_service_page($id);
        $count++;
    }
    return $count;
}

function sts_content_activate() {
    sts_content_register_service_type();
    sts_content_import_services();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sts_content_activate');
