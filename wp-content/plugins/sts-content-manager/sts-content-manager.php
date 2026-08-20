<?php
/**
 * Plugin Name: STS Content Manager
 * Description: Importerer og administrerer STS-serviceydelser og nyheder fra den originale side.
 * Version: 1.0.0
 * Author: STS ApS
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('STS_CONTENT_VERSION', '1.0.0');

function sts_content_register_service_type() {
    register_post_type('sts_service', array(
        'labels' => array(
            'name' => 'Serviceydelser',
            'singular_name' => 'Serviceydelse',
            'add_new_item' => 'Tilføj serviceydelse',
            'edit_item' => 'Rediger serviceydelse',
            'new_item' => 'Ny serviceydelse',
            'view_item' => 'Vis serviceydelse',
            'search_items' => 'Søg i serviceydelser',
            'not_found' => 'Ingen serviceydelser fundet',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'ydelse', 'with_front' => false),
        'show_in_rest' => true,
    ));
}
add_action('init', 'sts_content_register_service_type');

function sts_content_original_file($name) {
    $path = ABSPATH . 'supertotalservice.dk/data/' . $name;
    return is_readable($path) ? $path : '';
}

function sts_content_original_media_url($path) {
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return home_url('/supertotalservice.dk/' . ltrim($path, '/'));
}

function sts_content_slug($value) {
    return sanitize_title($value);
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
        update_post_meta($id, '_sts_service_id', sanitize_text_field($service['id'] ?? ''));
        update_post_meta($id, '_sts_service_icon', sanitize_text_field($service['icon'] ?? ''));
        update_post_meta($id, '_sts_service_category', sanitize_key($service['category'] ?? ''));
        update_post_meta($id, '_sts_service_hero_title', sanitize_text_field($service['hero_title'] ?? ''));
        update_post_meta($id, '_sts_service_image', esc_url_raw(sts_content_original_media_url($service['image'] ?? '')));
        update_post_meta($id, '_sts_service_benefits', array_map('sanitize_text_field', (array) ($service['benefits'] ?? array())));
        $count++;
    }
    return $count;
}

function sts_content_import_news() {
    $file = sts_content_original_file('blog.json');
    if (!$file) {
        return 0;
    }
    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data) || empty($data['posts']) || !is_array($data['posts'])) {
        return 0;
    }
    $count = 0;
    foreach ($data['posts'] as $post_data) {
        $slug = sts_content_slug($post_data['slug'] ?? $post_data['title'] ?? '');
        if ($slug === '') {
            continue;
        }
        $existing = get_page_by_path($slug, OBJECT, 'post');
        $post = array(
            'post_type' => 'post',
            'post_status' => !empty($post_data['published']) ? 'publish' : 'draft',
            'post_name' => $slug,
            'post_title' => sanitize_text_field($post_data['title'] ?? ''),
            'post_excerpt' => sanitize_textarea_field($post_data['excerpt'] ?? ''),
            'post_content' => wp_kses_post($post_data['content'] ?? ''),
            'post_date' => !empty($post_data['created']) ? gmdate('Y-m-d H:i:s', strtotime($post_data['created'])) : current_time('mysql'),
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
        update_post_meta($id, '_sts_original_id', sanitize_text_field($post_data['id'] ?? ''));
        update_post_meta($id, '_sts_news_image', esc_url_raw(sts_content_original_media_url($post_data['image'] ?? '')));
        $count++;
    }
    return $count;
}

function sts_content_activate() {
    sts_content_register_service_type();
    sts_content_import_services();
    sts_content_import_news();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sts_content_activate');

function sts_content_admin_menu() {
    add_submenu_page('edit.php?post_type=sts_service', 'STS import', 'Importér original data', 'manage_options', 'sts-content-import', 'sts_content_import_page');
}
add_action('admin_menu', 'sts_content_admin_menu');

function sts_content_import_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $message = '';
    if (isset($_POST['sts_import_nonce']) && wp_verify_nonce($_POST['sts_import_nonce'], 'sts_import')) {
        $services = sts_content_import_services();
        $news = sts_content_import_news();
        $message = sprintf('%d serviceydelser og %d nyheder blev importeret.', $services, $news);
    }
    ?>
    <div class="wrap">
        <h1>STS Content Manager</h1>
        <p>Importer den originale sides serviceydelser og nyheder til WordPress. Eksisterende poster med samme slug opdateres.</p>
        <?php if ($message) : ?><div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div><?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('sts_import', 'sts_import_nonce'); ?>
            <p><button class="button button-primary" type="submit">Importér / synkronisér original data</button></p>
        </form>
        <p>Redigér eller slet serviceydelser under <a href="<?php echo esc_url(admin_url('edit.php?post_type=sts_service')); ?>">Serviceydelser</a> og nyheder under <a href="<?php echo esc_url(admin_url('edit.php')); ?>">Indlæg</a>.</p>
    </div>
    <?php
}

function sts_content_service_meta_box() {
    add_meta_box('sts-service-details', 'STS serviceoplysninger', 'sts_content_service_meta_box_html', 'sts_service', 'normal', 'high');
}
add_action('add_meta_boxes', 'sts_content_service_meta_box');

function sts_content_service_meta_box_html($post) {
    wp_nonce_field('sts_service_meta', 'sts_service_meta_nonce');
    $fields = array(
        'icon' => get_post_meta($post->ID, '_sts_service_icon', true),
        'category' => get_post_meta($post->ID, '_sts_service_category', true),
        'hero_title' => get_post_meta($post->ID, '_sts_service_hero_title', true),
        'image' => get_post_meta($post->ID, '_sts_service_image', true),
        'benefits' => implode("\n", (array) get_post_meta($post->ID, '_sts_service_benefits', true)),
    );
    foreach ($fields as $key => $value) {
        $label = array('icon' => 'Ikon', 'category' => 'Kategori (byg, mal eller ren)', 'hero_title' => 'Hero-titel', 'image' => 'Billed-URL', 'benefits' => 'Fordele (én pr. linje)')[$key];
        echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
        $type = $key === 'benefits' ? 'textarea' : 'text';
        if ($type === 'textarea') {
            echo '<textarea class="widefat" rows="4" name="sts_service_' . esc_attr($key) . '">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input class="widefat" type="text" name="sts_service_' . esc_attr($key) . '" value="' . esc_attr($value) . '"></label></p>';
        }
    }
}

function sts_content_save_service_meta($post_id) {
    if (!isset($_POST['sts_service_meta_nonce']) || !wp_verify_nonce($_POST['sts_service_meta_nonce'], 'sts_service_meta') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    update_post_meta($post_id, '_sts_service_icon', sanitize_text_field($_POST['sts_service_icon'] ?? ''));
    update_post_meta($post_id, '_sts_service_category', sanitize_key($_POST['sts_service_category'] ?? ''));
    update_post_meta($post_id, '_sts_service_hero_title', sanitize_text_field($_POST['sts_service_hero_title'] ?? ''));
    update_post_meta($post_id, '_sts_service_image', esc_url_raw($_POST['sts_service_image'] ?? ''));
    update_post_meta($post_id, '_sts_service_benefits', array_filter(array_map('sanitize_text_field', preg_split('/\r\n|\r|\n/', $_POST['sts_service_benefits'] ?? ''))));
}
add_action('save_post_sts_service', 'sts_content_save_service_meta');

function sts_content_news_image($post_id) {
    return get_post_meta($post_id, '_sts_news_image', true);
}
function sts_content_service_image($post_id) {
    return get_post_meta($post_id, '_sts_service_image', true);
}
