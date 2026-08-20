<?php
/**
 * Plugin Name: STS Content Manager
 * Description: Importerer og administrerer STS-serviceydelser fra den originale side.
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

function sts_content_enqueue_admin_media($hook) {
    if (in_array($hook, array('post.php', 'post-new.php'), true) && isset($_GET['post_type']) && $_GET['post_type'] === 'sts_service') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'sts_content_enqueue_admin_media');

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
        $message = sprintf('%d serviceydelser blev importeret.', $services);
    }
    ?>
    <div class="wrap">
        <h1>STS Content Manager</h1>
        <p>Importer den originale sides serviceydelser til WordPress. Eksisterende poster med samme slug opdateres.</p>
        <?php if ($message) : ?><div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div><?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('sts_import', 'sts_import_nonce'); ?>
            <p><button class="button button-primary" type="submit">Importér / synkronisér original data</button></p>
        </form>
        <p>Redigér, slet eller opret serviceydelser under <a href="<?php echo esc_url(admin_url('edit.php?post_type=sts_service')); ?>">Serviceydelser</a>. Nyheder administreres af det separate STS News Manager-plugin.</p>
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
    echo '<p><label><strong>Ikon</strong><br><input class="widefat" type="text" name="sts_service_icon" value="' . esc_attr($fields['icon']) . '"></label></p>';
    echo '<p><label><strong>Kategori</strong><br><select class="widefat" name="sts_service_category">';
    foreach (array('byg' => 'STS Byg', 'mal' => 'STS Mal', 'ren' => 'STS Ren') as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected($fields['category'], $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></label></p>';
    echo '<p><label><strong>Hero-titel</strong><br><input class="widefat" type="text" name="sts_service_hero_title" value="' . esc_attr($fields['hero_title']) . '"></label></p>';
    echo '<p><label><strong>Billede</strong><br><input class="widefat" id="sts-service-image" type="url" name="sts_service_image" value="' . esc_attr($fields['image']) . '"></label> <button type="button" class="button" id="sts-select-service-image">Vælg eller upload billede</button></p>';
    if ($fields['image']) {
        echo '<p><img src="' . esc_url($fields['image']) . '" alt="" style="max-width:260px;height:auto;display:block"></p>';
    }
    echo '<p><label><strong>Fordele, én pr. linje</strong><br><textarea class="widefat" rows="4" name="sts_service_benefits">' . esc_textarea($fields['benefits']) . '</textarea></label></p>';
    $process = (array) get_post_meta($post->ID, '_sts_service_process', true);
    echo '<hr><h3>Service Process Section</h3>';
    echo '<p><label>Process eyebrow<br><input class="widefat" name="sts_service_process_eyebrow" value="' . esc_attr($process['eyebrow'] ?? '') . '"></label></p>';
    echo '<p><label>Process titel<br><input class="widefat" name="sts_service_process_title" value="' . esc_attr($process['title'] ?? '') . '"></label></p>';
    for ($step = 1; $step <= 4; $step++) {
        echo '<p><strong>Box ' . $step . '</strong><br><label>Titel<br><input class="widefat" name="sts_service_process_step_' . $step . '_title" value="' . esc_attr($process['steps'][$step - 1]['title'] ?? '') . '"></label><br><label>Beskrivelse<br><textarea class="widefat" rows="2" name="sts_service_process_step_' . $step . '_description">' . esc_textarea($process['steps'][$step - 1]['description'] ?? '') . '</textarea></label></p>';
    }
    echo '<script>jQuery(function($){$("#sts-select-service-image").on("click",function(e){e.preventDefault();var frame=wp.media({title:"Vælg servicebillede",button:{text:"Brug billede"},multiple:false});frame.on("select",function(){var item=frame.state().get("selection").first().toJSON();$("#sts-service-image").val(item.url);});frame.open();});});</script>';
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
    $process = array(
        'eyebrow' => sanitize_text_field($_POST['sts_service_process_eyebrow'] ?? ''),
        'title' => sanitize_text_field($_POST['sts_service_process_title'] ?? ''),
        'steps' => array(),
    );
    for ($step = 1; $step <= 4; $step++) {
        $process['steps'][] = array(
            'title' => sanitize_text_field($_POST['sts_service_process_step_' . $step . '_title'] ?? ''),
            'description' => sanitize_textarea_field($_POST['sts_service_process_step_' . $step . '_description'] ?? ''),
        );
    }
    update_post_meta($post_id, '_sts_service_process', $process);
}
add_action('save_post_sts_service', 'sts_content_save_service_meta');

function sts_content_service_image($post_id) {
    return get_post_meta($post_id, '_sts_service_image', true);
}

function sts_content_duplicate_row_action($actions, $post) {
    if ($post->post_type === 'sts_service' && current_user_can('edit_post', $post->ID)) {
        $url = wp_nonce_url(admin_url('admin-post.php?action=sts_content_duplicate&post_id=' . $post->ID), 'sts_content_duplicate_' . $post->ID);
        $actions['sts_duplicate'] = '<a href="' . esc_url($url) . '">Duplikér</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'sts_content_duplicate_row_action', 10, 2);

function sts_content_duplicate_service() {
    $post_id = absint($_GET['post_id'] ?? 0);
    if (!$post_id || !current_user_can('edit_post', $post_id) || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_content_duplicate_' . $post_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    $original = get_post($post_id);
    $copy_id = wp_insert_post(array(
        'post_type' => 'sts_service',
        'post_status' => 'draft',
        'post_title' => $original->post_title . ' (Kopi)',
        'post_content' => $original->post_content,
        'post_excerpt' => $original->post_excerpt,
    ));
    if ($copy_id && !is_wp_error($copy_id)) {
        foreach (array('_sts_service_icon', '_sts_service_category', '_sts_service_hero_title', '_sts_service_image', '_sts_service_benefits', '_sts_service_process') as $key) {
            update_post_meta($copy_id, $key, get_post_meta($post_id, $key, true));
        }
        wp_safe_redirect(admin_url('post.php?post=' . $copy_id . '&action=edit'));
        exit;
    }
    wp_safe_redirect(admin_url('edit.php?post_type=sts_service'));
    exit;
}
add_action('admin_post_sts_content_duplicate', 'sts_content_duplicate_service');
