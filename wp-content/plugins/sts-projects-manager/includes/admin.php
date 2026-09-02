<?php
/**
 * Admin screens for STS projects — mirrors the STS Services admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_projects_admin_menu() {
    add_menu_page('STS Projekter', 'STS Projekter', 'edit_pages', 'sts-projects', 'sts_projects_list_page', 'dashicons-portfolio', 27);
    add_submenu_page('sts-projects', 'Alle projekter', 'Alle projekter', 'edit_pages', 'sts-projects', 'sts_projects_list_page');
    add_submenu_page('sts-projects', 'Tilføj projekt', 'Tilføj projekt', 'edit_pages', 'sts-project-edit', 'sts_projects_edit_page');
}
add_action('admin_menu', 'sts_projects_admin_menu');

function sts_projects_admin_assets($hook) {
    if (strpos((string) $hook, 'sts-project-edit') !== false) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'sts_projects_admin_assets');

function sts_projects_admin_url($page, $args = array()) {
    return add_query_arg(array_merge(array('page' => $page), $args), admin_url('admin.php'));
}

function sts_projects_admin_notice() {
    if (empty($_GET['sts_projects_notice'])) {
        return;
    }
    $messages = array(
        'created' => 'Projektet blev oprettet.',
        'updated' => 'Projektet blev opdateret.',
        'deleted' => 'Projektet blev flyttet til papirkurven.',
        'duplicated' => 'Projektet blev duplikeret.',
    );
    $key = sanitize_key($_GET['sts_projects_notice']);
    if (isset($messages[$key])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$key]) . '</p></div>';
    }
}
add_action('admin_notices', 'sts_projects_admin_notice');

function sts_projects_list_page() {
    if (!current_user_can('edit_pages')) {
        return;
    }
    $projects = sts_projects_get_all();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Projekter</h1>
        <a href="<?php echo esc_url(sts_projects_admin_url('sts-project-edit')); ?>" class="page-title-action">Tilføj nyt projekt</a>
        <hr class="wp-header-end">
        <p>Alle udgivne projekter vises på <a href="<?php echo esc_url(sts_projects_archive_url()); ?>" target="_blank" rel="noopener"><code><?php echo esc_html(sts_projects_archive_url()); ?></code></a>. Hvert projekt får sin egen side med billedkarrusel og før/efter-billeder.</p>
        <?php if (!$projects) : ?>
            <div class="notice notice-info"><p>Ingen projekter endnu. <a href="<?php echo esc_url(sts_projects_admin_url('sts-project-edit')); ?>">Opret det første</a>.</p></div>
        <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:110px">Billede</th>
                    <th>Titel</th>
                    <th style="width:110px">Kategori</th>
                    <th style="width:150px">Lokation</th>
                    <th style="width:90px">Billeder</th>
                    <th style="width:260px">Handlinger</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($projects as $project) :
                $category = get_post_meta($project->ID, '_sts_project_category', true);
                $gallery_count = count(sts_projects_gallery($project->ID));
                ?>
                <tr>
                    <td><img src="<?php echo esc_url(sts_projects_cover($project->ID)); ?>" alt="" style="width:88px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #ddd;display:block"></td>
                    <td>
                        <strong><a href="<?php echo esc_url(sts_projects_admin_url('sts-project-edit', array('project' => $project->ID))); ?>"><?php echo esc_html($project->post_title); ?></a></strong>
                        <?php if ($project->post_status !== 'publish') : ?><span class="post-state"> — Kladde</span><?php endif; ?>
                        <?php if (get_post_meta($project->ID, '_sts_project_featured', true) === '1') : ?><span class="post-state"> — Fremhævet</span><?php endif; ?>
                    </td>
                    <td><?php echo esc_html(sts_projects_category_label($category)); ?></td>
                    <td><?php echo esc_html(get_post_meta($project->ID, '_sts_project_location', true)); ?></td>
                    <td><?php echo (int) $gallery_count; ?></td>
                    <td>
                        <a class="button button-primary" href="<?php echo esc_url(sts_projects_admin_url('sts-project-edit', array('project' => $project->ID))); ?>">Redigér</a>
                        <a class="button" href="<?php echo esc_url(get_permalink($project)); ?>" target="_blank" rel="noopener">Vis</a>
                        <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=sts_projects_duplicate&project=' . $project->ID), 'sts_projects_duplicate_' . $project->ID)); ?>">Duplikér</a>
                        <a class="button button-link-delete" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=sts_projects_delete&project=' . $project->ID), 'sts_projects_delete_' . $project->ID)); ?>" onclick="return confirm('Flyt dette projekt til papirkurven?');">Slet</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}

function sts_projects_edit_page() {
    if (!current_user_can('edit_pages')) {
        return;
    }
    $project_id = absint($_GET['project'] ?? 0);
    $project = $project_id ? get_post($project_id) : null;
    if ($project && $project->post_type !== 'sts_project') {
        $project = null;
    }
    $id = $project ? $project->ID : 0;
    $meta = array(
        'category' => $id ? get_post_meta($id, '_sts_project_category', true) : 'ren',
        'hero_class' => $id ? get_post_meta($id, '_sts_project_hero_class', true) : '',
        'location' => $id ? get_post_meta($id, '_sts_project_location', true) : '',
        'client' => $id ? get_post_meta($id, '_sts_project_client', true) : '',
        'address' => $id ? get_post_meta($id, '_sts_project_address', true) : '',
        'scope' => $id ? get_post_meta($id, '_sts_project_scope', true) : '',
        'duration' => $id ? get_post_meta($id, '_sts_project_duration', true) : '',
        'completed' => $id ? get_post_meta($id, '_sts_project_completed', true) : '',
        'cover' => $id ? get_post_meta($id, '_sts_project_cover', true) : '',
        'before_image' => $id ? get_post_meta($id, '_sts_project_before_image', true) : '',
        'after_image' => $id ? get_post_meta($id, '_sts_project_after_image', true) : '',
        'featured' => $id ? get_post_meta($id, '_sts_project_featured', true) : '0',
    );
    $services = $id ? implode("\n", sts_projects_list_meta($id, '_sts_project_services')) : '';
    $materials = $id ? implode("\n", sts_projects_list_meta($id, '_sts_project_materials')) : '';
    $gallery = $id ? sts_projects_gallery($id) : array();
    $slug = $project ? $project->post_name : '';
    ?>
    <div class="wrap">
        <h1><?php echo $project ? 'Redigér projekt' : 'Opret projekt'; ?></h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:900px">
            <input type="hidden" name="action" value="sts_projects_save">
            <input type="hidden" name="project" value="<?php echo esc_attr($id); ?>">
            <?php wp_nonce_field('sts_projects_save', 'sts_projects_nonce'); ?>

            <h2>Grundoplysninger</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-project-title">Projekttitel *</label></th>
                    <td><input class="large-text" type="text" id="sts-project-title" name="title" required value="<?php echo esc_attr($project ? $project->post_title : ''); ?>" placeholder="F.eks. Facademaling af erhvervsejendom">
                        <p class="description">Et klart navn der beskriver opgaven.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-slug">URL-slug *</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-slug" name="slug" required value="<?php echo esc_attr($slug); ?>">
                        <p class="description">Projektet bliver <code><?php echo esc_html(home_url('/' . STS_PROJECTS_SLUG . '/')); ?><span id="sts-project-slug-preview"><?php echo esc_html($slug); ?></span>/</code></p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-category">Kategori *</label></th>
                    <td><select id="sts-project-category" name="category">
                        <?php foreach (sts_projects_categories() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($meta['category'], $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Bruges til filtrering på projektoversigten.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-location">Lokation *</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-location" name="location" required value="<?php echo esc_attr($meta['location']); ?>" placeholder="F.eks. København Ø">
                        <p class="description">By eller kvarter — styrker lokal SEO og troværdighed.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-excerpt">Kort beskrivelse *</label></th>
                    <td><textarea class="large-text" rows="2" id="sts-project-excerpt" name="excerpt" required><?php echo esc_textarea($project ? $project->post_excerpt : ''); ?></textarea>
                        <p class="description">Vises på projektkortene i oversigten.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-status">Status</label></th>
                    <td><select id="sts-project-status" name="status">
                        <option value="publish" <?php selected($project ? $project->post_status : 'publish', 'publish'); ?>>Udgivet</option>
                        <option value="draft" <?php selected($project ? $project->post_status : 'publish', 'draft'); ?>>Kladde</option>
                    </select></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-order">Sortering</label></th>
                    <td><input class="small-text" type="number" id="sts-project-order" name="menu_order" value="<?php echo esc_attr($project ? $project->menu_order : 0); ?>">
                        <p class="description">Lavest tal vises først.</p>
                        <p><label><input type="checkbox" name="featured" value="1" <?php checked($meta['featured'], '1'); ?>> Fremhæv dette projekt øverst i oversigten</label></p></td>
                </tr>
            </table>

            <h2>Udførte ydelser</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-project-services">Ydelser (én pr. linje) *</label></th>
                    <td><textarea class="large-text" rows="6" id="sts-project-services" name="services" placeholder="Gipsreparation&#10;Højtryksrensning&#10;Hovedrengøring"><?php echo esc_textarea($services); ?></textarea>
                        <p class="description">Præcis hvilket arbejde der blev udført. Vises som flueben-liste.</p></td>
                </tr>
            </table>

            <h2>Før &amp; efter</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-project-before-image">Før-billede</label></th>
                    <td>
                        <input class="large-text" type="text" id="sts-project-before-image" name="before_image" value="<?php echo esc_attr($meta['before_image']); ?>" data-sts-image-field>
                        <p><button type="button" class="button" data-sts-image-pick="#sts-project-before-image">Upload / vælg billede</button>
                           <button type="button" class="button" data-sts-image-clear="#sts-project-before-image">Fjern</button></p>
                        <img data-sts-image-preview="#sts-project-before-image" src="<?php echo esc_url($meta['before_image']); ?>" alt="" style="width:260px;height:160px;object-fit:cover;border:1px solid #ddd;border-radius:6px<?php echo $meta['before_image'] ? '' : ';display:none'; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-after-image">Efter-billede</label></th>
                    <td>
                        <input class="large-text" type="text" id="sts-project-after-image" name="after_image" value="<?php echo esc_attr($meta['after_image']); ?>" data-sts-image-field>
                        <p><button type="button" class="button" data-sts-image-pick="#sts-project-after-image">Upload / vælg billede</button>
                           <button type="button" class="button" data-sts-image-clear="#sts-project-after-image">Fjern</button></p>
                        <img data-sts-image-preview="#sts-project-after-image" src="<?php echo esc_url($meta['after_image']); ?>" alt="" style="width:260px;height:160px;object-fit:cover;border:1px solid #ddd;border-radius:6px<?php echo $meta['after_image'] ? '' : ';display:none'; ?>">
                        <p class="description">Udfyld begge felter for at vise en side-om-side sammenligning på projektsiden.</p>
                    </td>
                </tr>
            </table>

            <h2>Billedkarrusel</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Galleribilleder</th>
                    <td>
                        <p>
                            <button type="button" class="button button-secondary" id="sts-project-gallery-add">Tilføj billeder</button>
                            <button type="button" class="button" id="sts-project-gallery-clear">Ryd alle</button>
                        </p>
                        <div id="sts-project-gallery-list" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px">
                            <?php foreach ($gallery as $gallery_image) : ?>
                                <div class="sts-project-gallery-item" style="position:relative;width:140px">
                                    <input type="hidden" name="gallery[]" value="<?php echo esc_attr($gallery_image); ?>">
                                    <img src="<?php echo esc_url($gallery_image); ?>" alt="" style="width:140px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:6px;display:block">
                                    <button type="button" class="button button-small" data-sts-gallery-remove style="width:100%;margin-top:4px">Fjern</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="description">Vises som karrusel på projektsiden — samme karrusel som på forsiden.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-cover">Forsidebillede</label></th>
                    <td>
                        <input class="large-text" type="text" id="sts-project-cover" name="cover" value="<?php echo esc_attr($meta['cover']); ?>" data-sts-image-field placeholder="Tomt = efter-billedet eller første galleribillede">
                        <p><button type="button" class="button" data-sts-image-pick="#sts-project-cover">Upload / vælg billede</button>
                           <button type="button" class="button" data-sts-image-clear="#sts-project-cover">Fjern</button></p>
                        <img data-sts-image-preview="#sts-project-cover" src="<?php echo esc_url($meta['cover']); ?>" alt="" style="width:260px;height:160px;object-fit:cover;border:1px solid #ddd;border-radius:6px<?php echo $meta['cover'] ? '' : ';display:none'; ?>">
                        <p class="description">Billedet der vises på projektkortet i oversigten.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-hero-class">Hero-farve</label></th>
                    <td><select id="sts-project-hero-class" name="hero_class">
                        <option value="">Automatisk</option>
                        <?php foreach (sts_projects_hero_classes() as $hero_class) : ?>
                            <option value="<?php echo esc_attr($hero_class); ?>" <?php selected($meta['hero_class'], $hero_class); ?>><?php echo esc_html(str_replace('hero-', '', $hero_class)); ?></option>
                        <?php endforeach; ?>
                    </select></td>
                </tr>
            </table>

            <h2>Projektdetaljer (valgfrit)</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-project-client">Kundenavn</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-client" name="client" value="<?php echo esc_attr($meta['client']); ?>">
                        <p class="description">Udelad ved private kunder — medtag kun genkendelige erhvervskunder.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-address">Adresse</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-address" name="address" value="<?php echo esc_attr($meta['address']); ?>">
                        <p class="description">Brug kun en præcis adresse ved offentlige erhvervsbygninger.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-scope">Omfang / størrelse</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-scope" name="scope" value="<?php echo esc_attr($meta['scope']); ?>" placeholder="F.eks. 1.200 m² fordelt på 3 etager"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-duration">Tidsforbrug</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-duration" name="duration" value="<?php echo esc_attr($meta['duration']); ?>" placeholder="F.eks. Udført på 3 dage"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-completed">Afsluttet</label></th>
                    <td><input class="regular-text" type="text" id="sts-project-completed" name="completed" value="<?php echo esc_attr($meta['completed']); ?>" placeholder="F.eks. Marts 2026"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-project-materials">Materialer / produkter (én pr. linje)</label></th>
                    <td><textarea class="large-text" rows="4" id="sts-project-materials" name="materials"><?php echo esc_textarea($materials); ?></textarea>
                        <p class="description">F.eks. premium malingsmærker eller miljøvenlige rengøringsmidler.</p></td>
                </tr>
            </table>

            <h2>Fuld beskrivelse</h2>
            <p class="description">Den uddybende tekst der vises på projektsiden.</p>
            <?php
            wp_editor($project ? $project->post_content : '', 'sts_project_description', array(
                'textarea_name' => 'description',
                'textarea_rows' => 10,
                'media_buttons' => true,
            ));
            ?>

            <p class="submit">
                <button type="submit" class="button button-primary"><?php echo $project ? 'Opdatér projekt' : 'Opret projekt'; ?></button>
                <a class="button" href="<?php echo esc_url(sts_projects_admin_url('sts-projects')); ?>">Annullér</a>
            </p>
        </form>
    </div>
    <script>
    jQuery(function ($) {
        function slugify(value) {
            return String(value || '').toLowerCase()
                .replace(/ø/g, 'o').replace(/å/g, 'a').replace(/æ/g, 'ae')
                .replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '-')
                .replace(/-+/g, '-').replace(/^-|-$/g, '');
        }
        function syncPreview() {
            $('#sts-project-slug-preview').text(slugify($('#sts-project-slug').val() || $('#sts-project-title').val()));
        }
        $('#sts-project-title').on('input', function () {
            if (!$('#sts-project-slug').data('touched')) {
                $('#sts-project-slug').val(slugify(this.value));
            }
            syncPreview();
        });
        $('#sts-project-slug').on('input', function () { $(this).data('touched', true); syncPreview(); });
        <?php if ($project) : ?>$('#sts-project-slug').data('touched', true);<?php endif; ?>
        syncPreview();

        function refreshPreview(selector) {
            var url = $.trim($(selector).val());
            var $img = $('[data-sts-image-preview="' + selector + '"]');
            if (url) { $img.attr('src', url).show(); } else { $img.attr('src', '').hide(); }
        }
        $('[data-sts-image-field]').on('input', function () { refreshPreview('#' + this.id); });
        $('[data-sts-image-clear]').on('click', function () {
            var selector = $(this).data('sts-image-clear');
            $(selector).val('');
            refreshPreview(selector);
        });
        $('[data-sts-image-pick]').on('click', function (e) {
            e.preventDefault();
            var selector = $(this).data('sts-image-pick');
            var frame = wp.media({ title: 'Vælg billede', button: { text: 'Brug billede' }, multiple: false });
            frame.on('select', function () {
                $(selector).val(frame.state().get('selection').first().toJSON().url);
                refreshPreview(selector);
            });
            frame.open();
        });

        function galleryItem(url) {
            return $('<div class="sts-project-gallery-item" style="position:relative;width:140px"></div>')
                .append($('<input type="hidden" name="gallery[]">').val(url))
                .append($('<img alt="" style="width:140px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:6px;display:block">').attr('src', url))
                .append('<button type="button" class="button button-small" data-sts-gallery-remove style="width:100%;margin-top:4px">Fjern</button>');
        }
        $('#sts-project-gallery-add').on('click', function (e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Vælg galleribilleder', button: { text: 'Tilføj til galleri' }, multiple: 'add' });
            frame.on('select', function () {
                frame.state().get('selection').toJSON().forEach(function (item) {
                    $('#sts-project-gallery-list').append(galleryItem(item.url));
                });
            });
            frame.open();
        });
        $('#sts-project-gallery-clear').on('click', function (e) {
            e.preventDefault();
            $('#sts-project-gallery-list').empty();
        });
        $('#sts-project-gallery-list').on('click', '[data-sts-gallery-remove]', function (e) {
            e.preventDefault();
            $(this).closest('.sts-project-gallery-item').remove();
        });
    });
    </script>
    <?php
}

/* ── Form handlers ─────────────────────────────────────────────── */

function sts_projects_sanitize_lines($value) {
    $lines = preg_split('/\r\n|\r|\n/', (string) $value);
    $lines = array_map('sanitize_text_field', array_map('trim', $lines));
    return array_values(array_filter($lines, 'strlen'));
}

function sts_projects_handle_save() {
    if (!current_user_can('edit_pages') || !isset($_POST['sts_projects_nonce']) || !wp_verify_nonce($_POST['sts_projects_nonce'], 'sts_projects_save')) {
        wp_die('Ugyldig forespørgsel.');
    }

    $project_id = absint($_POST['project'] ?? 0);
    $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    if ($title === '') {
        wp_die('Projektet skal have en titel.');
    }
    $slug = sanitize_title(wp_unslash($_POST['slug'] ?? ''));
    if ($slug === '') {
        $slug = sanitize_title($title);
    }
    $status = (($_POST['status'] ?? 'publish') === 'draft') ? 'draft' : 'publish';

    $postarr = array(
        'post_type' => 'sts_project',
        'post_status' => $status,
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => sanitize_textarea_field(wp_unslash($_POST['excerpt'] ?? '')),
        'post_content' => wp_kses_post(wp_unslash($_POST['description'] ?? '')),
        'menu_order' => (int) ($_POST['menu_order'] ?? 0),
    );

    if ($project_id && get_post_type($project_id) === 'sts_project') {
        $postarr['ID'] = $project_id;
        $result = wp_update_post($postarr, true);
        $notice = 'updated';
    } else {
        $result = wp_insert_post($postarr, true);
        $notice = 'created';
    }
    if (is_wp_error($result)) {
        wp_die(esc_html($result->get_error_message()));
    }
    $project_id = (int) $result;

    $categories = sts_projects_categories();
    $category = sanitize_key(wp_unslash($_POST['category'] ?? 'ren'));
    if (!isset($categories[$category])) {
        $category = 'ren';
    }
    $hero_class = sanitize_html_class(wp_unslash($_POST['hero_class'] ?? ''));
    if (!in_array($hero_class, sts_projects_hero_classes(), true)) {
        $hero_class = '';
    }

    $gallery = array();
    foreach ((array) ($_POST['gallery'] ?? array()) as $image) {
        $image = esc_url_raw(trim(wp_unslash($image)));
        if ($image !== '') {
            $gallery[] = $image;
        }
    }

    update_post_meta($project_id, '_sts_project_category', $category);
    update_post_meta($project_id, '_sts_project_hero_class', $hero_class);
    update_post_meta($project_id, '_sts_project_location', sanitize_text_field(wp_unslash($_POST['location'] ?? '')));
    update_post_meta($project_id, '_sts_project_client', sanitize_text_field(wp_unslash($_POST['client'] ?? '')));
    update_post_meta($project_id, '_sts_project_address', sanitize_text_field(wp_unslash($_POST['address'] ?? '')));
    update_post_meta($project_id, '_sts_project_scope', sanitize_text_field(wp_unslash($_POST['scope'] ?? '')));
    update_post_meta($project_id, '_sts_project_duration', sanitize_text_field(wp_unslash($_POST['duration'] ?? '')));
    update_post_meta($project_id, '_sts_project_completed', sanitize_text_field(wp_unslash($_POST['completed'] ?? '')));
    update_post_meta($project_id, '_sts_project_services', sts_projects_sanitize_lines(wp_unslash($_POST['services'] ?? '')));
    update_post_meta($project_id, '_sts_project_materials', sts_projects_sanitize_lines(wp_unslash($_POST['materials'] ?? '')));
    update_post_meta($project_id, '_sts_project_before_image', esc_url_raw(wp_unslash($_POST['before_image'] ?? '')));
    update_post_meta($project_id, '_sts_project_after_image', esc_url_raw(wp_unslash($_POST['after_image'] ?? '')));
    update_post_meta($project_id, '_sts_project_cover', esc_url_raw(wp_unslash($_POST['cover'] ?? '')));
    update_post_meta($project_id, '_sts_project_gallery', $gallery);
    update_post_meta($project_id, '_sts_project_featured', empty($_POST['featured']) ? '0' : '1');

    wp_safe_redirect(sts_projects_admin_url('sts-project-edit', array('project' => $project_id, 'sts_projects_notice' => $notice)));
    exit;
}
add_action('admin_post_sts_projects_save', 'sts_projects_handle_save');

function sts_projects_handle_duplicate() {
    $project_id = absint($_GET['project'] ?? 0);
    if (!$project_id || !current_user_can('edit_pages') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_projects_duplicate_' . $project_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    $original = get_post($project_id);
    if (!$original || $original->post_type !== 'sts_project') {
        wp_die('Projektet blev ikke fundet.');
    }
    $copy_id = wp_insert_post(array(
        'post_type' => 'sts_project',
        'post_status' => 'draft',
        'post_title' => $original->post_title . ' (Kopi)',
        'post_name' => wp_unique_post_slug($original->post_name . '-kopi', 0, 'draft', 'sts_project', 0),
        'post_excerpt' => $original->post_excerpt,
        'post_content' => $original->post_content,
        'menu_order' => $original->menu_order,
    ), true);
    if (is_wp_error($copy_id)) {
        wp_die(esc_html($copy_id->get_error_message()));
    }
    foreach (get_post_meta($project_id) as $key => $values) {
        if (strpos($key, '_sts_project_') === 0) {
            update_post_meta($copy_id, $key, maybe_unserialize($values[0]));
        }
    }
    wp_safe_redirect(sts_projects_admin_url('sts-project-edit', array('project' => $copy_id, 'sts_projects_notice' => 'duplicated')));
    exit;
}
add_action('admin_post_sts_projects_duplicate', 'sts_projects_handle_duplicate');

function sts_projects_handle_delete() {
    $project_id = absint($_GET['project'] ?? 0);
    if (!$project_id || !current_user_can('delete_pages') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_projects_delete_' . $project_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    if (get_post_type($project_id) === 'sts_project') {
        wp_trash_post($project_id);
    }
    wp_safe_redirect(sts_projects_admin_url('sts-projects', array('sts_projects_notice' => 'deleted')));
    exit;
}
add_action('admin_post_sts_projects_delete', 'sts_projects_handle_delete');
