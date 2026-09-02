<?php
/**
 * Shared data helpers for STS projects.
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_projects_categories() {
    return array(
        'byg' => 'STS Byg',
        'mal' => 'STS Mal',
        'ren' => 'STS Ren',
    );
}

function sts_projects_category_label($key) {
    $categories = sts_projects_categories();
    return isset($categories[$key]) ? $categories[$key] : 'STS Ren';
}

function sts_projects_hero_classes() {
    return array('hero-blue', 'hero-navy', 'hero-teal', 'hero-slate', 'hero-amber', 'hero-green', 'hero-red', 'hero-indigo');
}

/**
 * Deterministic hero colour so a project keeps the same shade across renders.
 */
function sts_projects_hero_class($slug) {
    $classes = sts_projects_hero_classes();
    $key = sanitize_title($slug);
    $score = 0;
    $length = strlen($key);
    for ($i = 0; $i < $length; $i++) {
        $score += ord($key[$i]);
    }
    return $classes[$score % count($classes)];
}

function sts_projects_fallback_image() {
    return get_template_directory_uri() . '/assets/images/haandvaerkere.jpg';
}

/**
 * Multi-value fields are stored as arrays; older/imported rows may hold a string.
 */
function sts_projects_list_meta($post_id, $key) {
    $value = get_post_meta($post_id, $key, true);
    if (is_string($value)) {
        $value = preg_split('/\r\n|\r|\n/', $value);
    }
    if (!is_array($value)) {
        return array();
    }
    $value = array_map('trim', array_map('strval', $value));
    return array_values(array_filter($value, 'strlen'));
}

function sts_projects_gallery($post_id) {
    return sts_projects_list_meta($post_id, '_sts_project_gallery');
}

/**
 * Card/hero image: explicit cover, else the "after" shot, else the first gallery image.
 */
function sts_projects_cover($post_id) {
    $cover = trim((string) get_post_meta($post_id, '_sts_project_cover', true));
    if ($cover !== '') {
        return $cover;
    }
    $after = trim((string) get_post_meta($post_id, '_sts_project_after_image', true));
    if ($after !== '') {
        return $after;
    }
    $gallery = sts_projects_gallery($post_id);
    if ($gallery) {
        return $gallery[0];
    }
    return sts_projects_fallback_image();
}

function sts_projects_get_all($args = array()) {
    $defaults = array(
        'post_type' => 'sts_project',
        'post_status' => array('publish', 'draft'),
        'numberposts' => -1,
        'orderby' => 'menu_order date',
        'order' => 'ASC',
    );
    return get_posts(array_merge($defaults, $args));
}

function sts_projects_get_published($category = '') {
    $args = array('post_status' => 'publish');
    if ($category !== '') {
        $args['meta_key'] = '_sts_project_category';
        $args['meta_value'] = $category;
    }
    return sts_projects_get_all($args);
}

/**
 * Normalised view model used by both front-end templates.
 */
function sts_projects_view_data($post) {
    $post = get_post($post);
    if (!$post) {
        return array();
    }
    $id = $post->ID;
    $category = (string) get_post_meta($id, '_sts_project_category', true);
    if (!array_key_exists($category, sts_projects_categories())) {
        $category = 'ren';
    }
    $hero_class = (string) get_post_meta($id, '_sts_project_hero_class', true);
    if (!in_array($hero_class, sts_projects_hero_classes(), true)) {
        $hero_class = sts_projects_hero_class($post->post_name);
    }

    return array(
        'id' => $id,
        'title' => get_the_title($post),
        'slug' => $post->post_name,
        'url' => get_permalink($post),
        'excerpt' => (string) $post->post_excerpt,
        'content' => (string) $post->post_content,
        'category' => $category,
        'category_label' => sts_projects_category_label($category),
        'hero_class' => $hero_class,
        'cover' => sts_projects_cover($id),
        'gallery' => sts_projects_gallery($id),
        'before_image' => trim((string) get_post_meta($id, '_sts_project_before_image', true)),
        'after_image' => trim((string) get_post_meta($id, '_sts_project_after_image', true)),
        'location' => trim((string) get_post_meta($id, '_sts_project_location', true)),
        'client' => trim((string) get_post_meta($id, '_sts_project_client', true)),
        'address' => trim((string) get_post_meta($id, '_sts_project_address', true)),
        'scope' => trim((string) get_post_meta($id, '_sts_project_scope', true)),
        'duration' => trim((string) get_post_meta($id, '_sts_project_duration', true)),
        'completed' => trim((string) get_post_meta($id, '_sts_project_completed', true)),
        'services' => sts_projects_list_meta($id, '_sts_project_services'),
        'materials' => sts_projects_list_meta($id, '_sts_project_materials'),
        'featured' => get_post_meta($id, '_sts_project_featured', true) === '1',
    );
}

/**
 * Optional facts, ready to render as a definition list. Empty values are dropped
 * so a sparsely filled project never shows blank rows.
 */
function sts_projects_fact_rows($data) {
    $rows = array(
        'Lokation' => $data['location'],
        'Kunde' => $data['client'],
        'Adresse' => $data['address'],
        'Omfang' => $data['scope'],
        'Tidsforbrug' => $data['duration'],
        'Afsluttet' => $data['completed'],
    );
    return array_filter($rows, 'strlen');
}

function sts_projects_archive_page() {
    $page_id = (int) get_option('sts_projects_page_id');
    if ($page_id) {
        $page = get_post($page_id);
        if ($page && $page->post_type === 'page' && $page->post_status !== 'trash') {
            return $page;
        }
    }
    $page = get_page_by_path(STS_PROJECTS_SLUG);
    if ($page) {
        update_option('sts_projects_page_id', $page->ID);
        return $page;
    }
    return null;
}

function sts_projects_archive_url() {
    $page = sts_projects_archive_page();
    return $page ? get_permalink($page) : home_url('/' . STS_PROJECTS_SLUG . '/');
}
