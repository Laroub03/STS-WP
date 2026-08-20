<?php
/**
 * Plugin Name: STS News Manager
 * Description: Opretter og administrerer STS-nyheder samt importerer nyheder fra den originale side.
 * Version: 1.0.0
 * Author: STS ApS
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_news_original_file() {
    $path = ABSPATH . 'supertotalservice.dk/data/blog.json';
    return is_readable($path) ? $path : '';
}

function sts_news_original_image_url($path) {
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return home_url('/supertotalservice.dk/' . ltrim($path, '/'));
}

function sts_news_enqueue_media($hook) {
    if (in_array($hook, array('post.php', 'post-new.php'), true) && (!empty($_GET['post_type']) && $_GET['post_type'] === 'post' || !empty($_GET['post']))) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'sts_news_enqueue_media');

function sts_news_import() {
    $file = sts_news_original_file();
    if (!$file) {
        return 0;
    }
    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data) || empty($data['posts']) || !is_array($data['posts'])) {
        return 0;
    }
    $count = 0;
    foreach ($data['posts'] as $source) {
        $slug = sanitize_title($source['slug'] ?? $source['title'] ?? '');
        if ($slug === '') {
            continue;
        }
        $existing = get_page_by_path($slug, OBJECT, 'post');
        $post = array(
            'post_type' => 'post',
            'post_status' => !empty($source['published']) ? 'publish' : 'draft',
            'post_name' => $slug,
            'post_title' => sanitize_text_field($source['title'] ?? ''),
            'post_excerpt' => sanitize_textarea_field($source['excerpt'] ?? ''),
            'post_content' => wp_kses_post($source['content'] ?? ''),
        );
        if (!empty($source['created']) && strtotime($source['created'])) {
            $post['post_date'] = gmdate('Y-m-d H:i:s', strtotime($source['created']));
        }
        if ($existing) {
            $post['ID'] = $existing->ID;
            $id = wp_update_post($post, true);
        } else {
            $id = wp_insert_post($post, true);
        }
        if (is_wp_error($id)) {
            continue;
        }
        update_post_meta($id, '_sts_news_original_id', sanitize_text_field($source['id'] ?? ''));
        update_post_meta($id, '_sts_news_image', esc_url_raw(sts_news_original_image_url($source['image'] ?? '')));
        $count++;
    }
    return $count;
}

function sts_news_activation() {
    sts_news_import();
}
register_activation_hook(__FILE__, 'sts_news_activation');

function sts_news_admin_menu() {
    add_menu_page('STS Nyheder', 'Nyheder', 'edit_posts', 'sts-news-manager', 'sts_news_admin_page', 'dashicons-megaphone', 26);
    add_submenu_page('sts-news-manager', 'Alle nyheder', 'Alle nyheder', 'edit_posts', 'edit.php', null);
    add_submenu_page('sts-news-manager', 'Tilføj nyhed', 'Tilføj nyhed', 'edit_posts', 'post-new.php', null);
}
add_action('admin_menu', 'sts_news_admin_menu');

function sts_news_admin_page() {
    if (!current_user_can('edit_posts')) {
        return;
    }
    $message = '';
    if (isset($_POST['sts_news_import_nonce']) && wp_verify_nonce($_POST['sts_news_import_nonce'], 'sts_news_import') && current_user_can('manage_options')) {
        $message = sprintf('%d nyheder blev importeret eller opdateret.', sts_news_import());
    }
    ?>
    <div class="wrap">
        <h1>STS Nyheder</h1>
        <?php if ($message) : ?><div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div><?php endif; ?>
        <p>Opret, rediger, udgiv, gem som kladde eller slet nyheder med WordPress’ normale indlægseditor.</p>
        <p><a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php')); ?>">Tilføj nyhed</a> <a class="button" href="<?php echo esc_url(admin_url('edit.php')); ?>">Administrer nyheder</a></p>
        <?php if (current_user_can('manage_options')) : ?>
            <hr><h2>Original data</h2>
            <p>Importer nyheder fra den originale sides <code>blog.json</code>. Eksisterende nyheder med samme slug opdateres.</p>
            <form method="post"><?php wp_nonce_field('sts_news_import', 'sts_news_import_nonce'); ?><button class="button" type="submit">Importér / synkronisér nyheder</button></form>
        <?php endif; ?>
    </div>
    <?php
}

function sts_news_meta_box() {
    add_meta_box('sts-news-details', 'STS nyhedsoplysninger', 'sts_news_meta_box_html', 'post', 'side', 'high');
}
add_action('add_meta_boxes', 'sts_news_meta_box');

function sts_news_meta_box_html($post) {
    wp_nonce_field('sts_news_meta', 'sts_news_meta_nonce');
    $image = get_post_meta($post->ID, '_sts_news_image', true);
    echo '<p><label for="sts-news-image"><strong>Fremhævet billede</strong></label><input class="widefat" id="sts-news-image" type="url" name="sts_news_image" value="' . esc_attr($image) . '"></p>';
    echo '<p><button type="button" class="button" id="sts-select-news-image">Vælg eller upload billede</button></p>';
    if ($image) {
        echo '<img src="' . esc_url($image) . '" alt="" style="max-width:100%;height:auto">';
    }
    echo '<script>jQuery(function($){$("#sts-select-news-image").on("click",function(e){e.preventDefault();var frame=wp.media({title:"Vælg nyhedsbillede",button:{text:"Brug billede"},multiple:false});frame.on("select",function(){var item=frame.state().get("selection").first().toJSON();$("#sts-news-image").val(item.url);});frame.open();});});</script>';
}

function sts_news_save_meta($post_id) {
    if (!isset($_POST['sts_news_meta_nonce']) || !wp_verify_nonce($_POST['sts_news_meta_nonce'], 'sts_news_meta') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    update_post_meta($post_id, '_sts_news_image', esc_url_raw($_POST['sts_news_image'] ?? ''));
}
add_action('save_post_post', 'sts_news_save_meta');

function sts_news_image($post_id) {
    return get_post_meta($post_id, '_sts_news_image', true);
}

function sts_news_duplicate_row_action($actions, $post) {
    if ($post->post_type === 'post' && current_user_can('edit_post', $post->ID)) {
        $url = wp_nonce_url(admin_url('admin-post.php?action=sts_news_duplicate&post_id=' . $post->ID), 'sts_news_duplicate_' . $post->ID);
        $actions['sts_duplicate'] = '<a href="' . esc_url($url) . '">Duplikér</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'sts_news_duplicate_row_action', 10, 2);

function sts_news_duplicate() {
    $post_id = absint($_GET['post_id'] ?? 0);
    if (!$post_id || !current_user_can('edit_post', $post_id) || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_news_duplicate_' . $post_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    $original = get_post($post_id);
    $copy_id = wp_insert_post(array(
        'post_type' => 'post',
        'post_status' => 'draft',
        'post_title' => $original->post_title . ' (Kopi)',
        'post_content' => $original->post_content,
        'post_excerpt' => $original->post_excerpt,
    ));
    if ($copy_id && !is_wp_error($copy_id)) {
        update_post_meta($copy_id, '_sts_news_image', get_post_meta($post_id, '_sts_news_image', true));
        wp_safe_redirect(admin_url('post.php?post=' . $copy_id . '&action=edit'));
        exit;
    }
    wp_safe_redirect(admin_url('edit.php'));
    exit;
}
add_action('admin_post_sts_news_duplicate', 'sts_news_duplicate');
