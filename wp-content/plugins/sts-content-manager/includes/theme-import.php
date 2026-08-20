<?php
/**
 * Seeds service fields from the converted page-<slug>.php templates so the
 * template renders exactly what the existing service pages show today.
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_content_theme_page_source($slug) {
    $file = get_template_directory() . '/page-' . $slug . '.php';
    return is_readable($file) ? file_get_contents($file) : '';
}

function sts_content_strip_markup($html) {
    $html = preg_replace('/<\?php.*?\?>/s', '', (string) $html);
    $html = preg_replace('/\s+data-wpc-[a-z-]+="[^"]*"/i', '', $html);
    return trim($html);
}

function sts_content_text($html) {
    $text = wp_strip_all_tags(sts_content_strip_markup($html));
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $text));
}

/**
 * @return array Fields found in the converted page (empty when nothing matched).
 */
function sts_content_parse_theme_page($slug) {
    $src = sts_content_theme_page_source($slug);
    if ($src === '') {
        return array();
    }
    // PHP tags contain ">" and would break the attribute-level matching below.
    $src = sts_content_strip_markup($src);
    $fields = array();

    if (preg_match('#<div class="(hero-[a-z]+) service-hero"#i', $src, $m)) {
        $fields['hero_class'] = $m[1];
    }
    if (preg_match('#service-hero.*?<span class="eyebrow"[^>]*>(.*?)</span>.*?<h1[^>]*>(.*?)</h1>\s*<p[^>]*>(.*?)</p>#s', $src, $m)) {
        $fields['eyebrow'] = sts_content_text($m[1]);
        $fields['hero_title'] = sts_content_text($m[2]);
        $fields['hero_text'] = sts_content_text($m[3]);
    }

    $steps = array();
    $chunks = explode('<div class="step-item">', $src);
    array_shift($chunks);
    foreach ($chunks as $chunk) {
        if (preg_match('#<h3[^>]*>(.*?)</h3>\s*<p[^>]*>(.*?)</p>#s', $chunk, $m)) {
            $steps[] = array(
                'title' => sts_content_text($m[1]),
                'description' => sts_content_text($m[2]),
            );
        }
    }
    if ($steps) {
        $fields['process'] = array('eyebrow' => '', 'title' => '', 'steps' => array_slice($steps, 0, 4));
        $head_end = strpos($src, 'steps-grid');
        $head_start = strrpos(substr($src, 0, $head_end), '<section');
        if ($head_start !== false) {
            $head = substr($src, $head_start, $head_end - $head_start);
            if (preg_match('#<span class="eyebrow"[^>]*>(.*?)</span>#s', $head, $m)) {
                $fields['process']['eyebrow'] = sts_content_text($m[1]);
            }
            if (preg_match('#<h2[^>]*>(.*?)</h2>#s', $head, $m)) {
                $fields['process']['title'] = sts_content_text($m[1]);
            }
        }
    }

    if (preg_match('#<ul class="list-check"[^>]*>(.*?)</ul>#s', $src, $m)) {
        preg_match_all('#<li[^>]*>(.*?)</li>#s', $m[1], $items);
        $benefits = array_filter(array_map('sts_content_text', $items[1]));
        if ($benefits) {
            $fields['benefits'] = array_values($benefits);
        }
    }

    if (preg_match('#<img[^>]*assets/images/([^"\']+\.(?:jpg|jpeg|png|webp))#i', $src, $m)) {
        $fields['image_file'] = $m[1];
    }

    if (preg_match('#Om servicen.*?<div style="background:white[^"]*"[^>]*>(.*?)</div>\s*</div>#s', $src, $m)) {
        $fields['about'] = sts_content_strip_markup($m[1]);
    }

    return $fields;
}

/**
 * Copies the converted page content into the service so editing it in the
 * admin drives the live page.
 */
function sts_content_seed_service_from_theme($service_id, $force = false) {
    $service = get_post($service_id);
    if (!$service || $service->post_type !== 'sts_service') {
        return false;
    }
    $fields = sts_content_parse_theme_page($service->post_name);
    if (!$fields) {
        return false;
    }

    if (!empty($fields['hero_class'])) {
        update_post_meta($service_id, '_sts_service_hero_class', $fields['hero_class']);
    }
    foreach (array('eyebrow', 'hero_title', 'hero_text') as $key) {
        if (!empty($fields[$key]) && ($force || get_post_meta($service_id, '_sts_service_' . $key, true) === '')) {
            update_post_meta($service_id, '_sts_service_' . $key, sanitize_text_field($fields[$key]));
        }
    }
    if (!empty($fields['benefits'])) {
        update_post_meta($service_id, '_sts_service_benefits', array_map('sanitize_text_field', $fields['benefits']));
    }
    if (!empty($fields['process'])) {
        update_post_meta($service_id, '_sts_service_process', $fields['process']);
    }
    if (!empty($fields['image_file'])) {
        $url = get_template_directory_uri() . '/assets/images/' . $fields['image_file'];
        if ($url !== sts_content_default_image($service->post_name)) {
            update_post_meta($service_id, '_sts_service_image', esc_url_raw($url));
        } else {
            delete_post_meta($service_id, '_sts_service_image');
        }
    }
    if (!empty($fields['about'])) {
        wp_update_post(array('ID' => $service_id, 'post_content' => wp_kses_post($fields['about'])));
        update_post_meta($service_id, '_sts_service_show_about', '1');
    } else {
        update_post_meta($service_id, '_sts_service_show_about', '0');
    }
    return true;
}

function sts_content_seed_all_services($force = false) {
    $count = 0;
    foreach (sts_content_get_services() as $service) {
        if (sts_content_seed_service_from_theme($service->ID, $force)) {
            $count++;
        }
    }
    return $count;
}
