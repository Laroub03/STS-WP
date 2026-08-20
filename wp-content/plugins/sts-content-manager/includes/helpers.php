<?php
/**
 * Shared data helpers for STS services.
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_content_categories() {
    return array(
        'byg' => 'STS Byg',
        'mal' => 'STS Mal',
        'ren' => 'STS Ren',
    );
}

function sts_content_default_benefits() {
    return array(
        'Fleksibel aftale',
        'Professionel udførelse',
        'Hurtig opstart',
        'Kvalitetssikret levering',
    );
}

function sts_content_default_process() {
    return array(
        'eyebrow' => 'Sådan arbejder vi',
        'title' => 'En struktureret proces fra første kontakt til færdig opgave.',
        'steps' => array(
            array('title' => 'Behovsafdækning', 'description' => 'Vi gennemgår opgaven og afklarer behov, ønsker og rammer.'),
            array('title' => 'Plan og opstart', 'description' => 'I får en konkret plan med opstart, ansvar og tydelige forventninger.'),
            array('title' => 'Udførelse', 'description' => 'Vores team løser opgaven effektivt og med fokus på høj kvalitet.'),
            array('title' => 'Opfølgning', 'description' => 'Vi følger op, dokumenterer arbejdet og justerer ved behov.'),
        ),
    );
}

/**
 * Mirrors the Danish slugify used by the original static CMS.
 */
function sts_content_slug($value) {
    $value = (string) $value;
    $value = str_replace(
        array('ø', 'Ø', 'å', 'Å', 'æ', 'Æ'),
        array('o', 'o', 'a', 'a', 'ae', 'ae'),
        $value
    );
    $value = strtolower($value);
    $value = preg_replace('/\s+/u', '-', $value);
    $value = preg_replace('/[^a-z0-9-]/u', '-', $value);
    $value = preg_replace('/-+/', '-', $value);
    $value = trim($value, '-');
    return $value !== '' ? $value : sanitize_title($value);
}

function sts_content_hero_classes() {
    return array('hero-blue', 'hero-navy', 'hero-teal', 'hero-slate', 'hero-amber', 'hero-green', 'hero-red', 'hero-indigo');
}

/**
 * Same deterministic hero colour pick as the original generator.
 */
function sts_content_hero_class($slug) {
    $classes = sts_content_hero_classes();
    $key = sts_content_slug($slug);
    $score = 0;
    $length = strlen($key);
    for ($i = 0; $i < $length; $i++) {
        $score += ord($key[$i]);
    }
    return $classes[$score % count($classes)];
}

function sts_content_default_image($slug) {
    $slug = sts_content_slug($slug);
    $aliases = array('vicevaertservice' => 'ejendomsservice');
    $file = isset($aliases[$slug]) ? $aliases[$slug] : $slug;
    $dir = get_template_directory() . '/assets/images/';
    if ($file !== '' && is_readable($dir . $file . '.jpg')) {
        return get_template_directory_uri() . '/assets/images/' . $file . '.jpg';
    }
    return get_template_directory_uri() . '/assets/images/haandvaerkere.jpg';
}

function sts_content_service_image($post_id) {
    $custom = get_post_meta($post_id, '_sts_service_image', true);
    if ($custom) {
        return $custom;
    }
    $post = get_post($post_id);
    return sts_content_default_image($post ? $post->post_name : '');
}

function sts_content_get_services($category = '') {
    $args = array(
        'post_type' => 'sts_service',
        'post_status' => array('publish', 'draft'),
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    if ($category !== '') {
        $args['meta_key'] = '_sts_service_category';
        $args['meta_value'] = sanitize_key($category);
    }
    return get_posts($args);
}

/**
 * The real front-end page a service is bound to (e.g. /maler/).
 */
function sts_content_service_page($service) {
    $service = get_post($service);
    if (!$service) {
        return null;
    }
    $page_id = (int) get_post_meta($service->ID, '_sts_service_page_id', true);
    if ($page_id) {
        $page = get_post($page_id);
        if ($page && $page->post_type === 'page' && $page->post_status !== 'trash') {
            return $page;
        }
    }
    $page = get_page_by_path($service->post_name);
    return ($page && $page->post_type === 'page') ? $page : null;
}

function sts_content_service_url($service) {
    $page = sts_content_service_page($service);
    if ($page) {
        return get_permalink($page);
    }
    $service = get_post($service);
    return $service ? home_url('/' . $service->post_name . '/') : home_url('/');
}

function sts_content_service_for_page($page_id) {
    $page = get_post($page_id);
    if (!$page) {
        return null;
    }
    $service_id = (int) get_post_meta($page->ID, '_sts_service_id', true);
    if ($service_id) {
        $service = get_post($service_id);
        if ($service && $service->post_type === 'sts_service') {
            return $service;
        }
    }
    $service = get_page_by_path($page->post_name, OBJECT, 'sts_service');
    return $service ?: null;
}

/**
 * Everything the front-end service template needs, with sane fallbacks.
 */
function sts_content_view_data($service, $page_id = 0) {
    $service = $service ? get_post($service) : null;
    $page = $page_id ? get_post($page_id) : null;

    if ($service) {
        $slug = $service->post_name;
        $title = $service->post_title;
        $description = $service->post_excerpt;
        $content = $service->post_content;
        $icon = get_post_meta($service->ID, '_sts_service_icon', true);
        $eyebrow = get_post_meta($service->ID, '_sts_service_eyebrow', true);
        $hero_title = get_post_meta($service->ID, '_sts_service_hero_title', true);
        $hero_text = get_post_meta($service->ID, '_sts_service_hero_text', true);
        $hero_class = get_post_meta($service->ID, '_sts_service_hero_class', true);
        $category = get_post_meta($service->ID, '_sts_service_category', true);
        $benefits = array_filter((array) get_post_meta($service->ID, '_sts_service_benefits', true));
        $process = (array) get_post_meta($service->ID, '_sts_service_process', true);
        $show_about = get_post_meta($service->ID, '_sts_service_show_about', true) === '1';
        $image = sts_content_service_image($service->ID);
    } else {
        $slug = $page ? $page->post_name : '';
        $title = $page ? $page->post_title : '';
        $description = $page ? $page->post_excerpt : '';
        $content = $page ? $page->post_content : '';
        $icon = '';
        $eyebrow = '';
        $hero_title = '';
        $hero_text = '';
        $hero_class = '';
        $category = '';
        $benefits = array();
        $process = array();
        $show_about = false;
        $image = sts_content_default_image($slug);
    }

    $defaults = sts_content_default_process();
    $steps = array();
    for ($i = 0; $i < 4; $i++) {
        $step = isset($process['steps'][$i]) && is_array($process['steps'][$i]) ? $process['steps'][$i] : array();
        $steps[] = array(
            'number' => $i + 1,
            'title' => trim((string) ($step['title'] ?? '')) !== '' ? $step['title'] : $defaults['steps'][$i]['title'],
            'description' => trim((string) ($step['description'] ?? '')) !== '' ? $step['description'] : $defaults['steps'][$i]['description'],
        );
    }

    if (trim((string) $content) === '') {
        $content = '<h2>' . esc_html($title) . '</h2><p>' . esc_html($description) . '</p>';
    }

    $icon = $icon !== '' ? $icon : '🔧';

    return array(
        'slug' => $slug,
        'title' => $title,
        'description' => $description !== '' ? $description : 'Professionel service leveret af STS ApS.',
        'content' => $content,
        'show_about' => $show_about,
        'icon' => $icon,
        'eyebrow' => $eyebrow !== '' ? $eyebrow : trim($icon . ' ' . $title),
        'hero_title' => $hero_title !== '' ? $hero_title : $title,
        'hero_text' => $hero_text !== '' ? $hero_text : ($description !== '' ? $description : 'Professionel service leveret af STS ApS.'),
        'category' => $category,
        'benefits' => $benefits ? $benefits : sts_content_default_benefits(),
        'process' => array(
            'eyebrow' => trim((string) ($process['eyebrow'] ?? '')) !== '' ? $process['eyebrow'] : $defaults['eyebrow'],
            'title' => trim((string) ($process['title'] ?? '')) !== '' ? $process['title'] : $defaults['title'],
            'steps' => $steps,
        ),
        'image' => $image,
        'hero_class' => $hero_class !== '' ? $hero_class : sts_content_hero_class($slug),
    );
}
