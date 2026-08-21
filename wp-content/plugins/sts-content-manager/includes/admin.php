<?php
/**
 * Admin screens for STS services — mirrors the original STS CMS admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function sts_content_admin_menu() {
    add_menu_page('STS Services', 'STS Services', 'edit_pages', 'sts-services', 'sts_content_services_list_page', 'dashicons-admin-tools', 26);
    add_submenu_page('sts-services', 'Alle services', 'Alle services', 'edit_pages', 'sts-services', 'sts_content_services_list_page');
    add_submenu_page('sts-services', 'Tilføj service', 'Tilføj service', 'edit_pages', 'sts-service-edit', 'sts_content_service_edit_page');
    add_submenu_page('sts-services', 'Importér original data', 'Importér original data', 'manage_options', 'sts-content-import', 'sts_content_import_page');
}
add_action('admin_menu', 'sts_content_admin_menu');

function sts_content_admin_assets($hook) {
    if (strpos((string) $hook, 'sts-service-edit') !== false) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'sts_content_admin_assets');

function sts_content_admin_url($page, $args = array()) {
    return add_query_arg(array_merge(array('page' => $page), $args), admin_url('admin.php'));
}

function sts_content_admin_notice() {
    if (empty($_GET['sts_notice'])) {
        return;
    }
    $messages = array(
        'created' => 'Servicen blev oprettet, og siden er klar.',
        'updated' => 'Servicen blev opdateret.',
        'deleted' => 'Servicen blev slettet.',
        'duplicated' => 'Servicen blev duplikeret.',
        'imported' => 'Original data blev importeret.',
        'template' => 'Siden blev oprettet og forbundet.',
    );
    $key = sanitize_key($_GET['sts_notice']);
    if (isset($messages[$key])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$key]) . '</p></div>';
    }
}
add_action('admin_notices', 'sts_content_admin_notice');

function sts_content_services_list_page() {
    if (!current_user_can('edit_pages')) {
        return;
    }
    $services = sts_content_get_services();
    $categories = sts_content_categories();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Serviceydelser</h1>
        <a href="<?php echo esc_url(sts_content_admin_url('sts-service-edit')); ?>" class="page-title-action">Tilføj ny service</a>
        <hr class="wp-header-end">
        <p>Hver service styrer den rigtige side på <code><?php echo esc_html(home_url('/')); ?>&lt;slug&gt;/</code>. Alle servicesider bruger STS-servicetemplaten, så en ny service får nøjagtig samme design som de eksisterende.</p>
        <?php if (!$services) : ?>
            <div class="notice notice-info"><p>Ingen services endnu. <a href="<?php echo esc_url(sts_content_admin_url('sts-service-edit')); ?>">Opret den første</a>.</p></div>
        <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:110px">Billede</th>
                    <th>Titel</th>
                    <th style="width:110px">Kategori</th>
                    <th>Beskrivelse</th>
                    <th style="width:200px">Side</th>
                    <th style="width:260px">Handlinger</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($services as $service) :
                $page = sts_content_service_page($service);
                $url = sts_content_service_url($service);
                $description = wp_strip_all_tags($service->post_excerpt);
                ?>
                <tr>
                    <td><img src="<?php echo esc_url(sts_content_service_image($service->ID)); ?>" alt="" style="width:88px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #ddd;display:block"></td>
                    <td>
                        <strong><a href="<?php echo esc_url(sts_content_admin_url('sts-service-edit', array('service' => $service->ID))); ?>"><?php echo esc_html($service->post_title); ?></a></strong>
                        <?php if ($service->post_status !== 'publish') : ?><span class="post-state"> — Kladde</span><?php endif; ?>
                    </td>
                    <td><?php echo esc_html($categories[get_post_meta($service->ID, '_sts_service_category', true)] ?? 'STS Ren'); ?></td>
                    <td><?php echo esc_html(mb_strimwidth($description, 0, 80, '…')); ?></td>
                    <td>
                        <code>/<?php echo esc_html($service->post_name); ?>/</code>
                    </td>
                    <td>
                        <a class="button button-primary" href="<?php echo esc_url(sts_content_admin_url('sts-service-edit', array('service' => $service->ID))); ?>">Redigér</a>
                        <a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">Vis</a>
                        <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=sts_content_duplicate&service=' . $service->ID), 'sts_content_duplicate_' . $service->ID)); ?>">Duplikér</a>
                        <a class="button button-link-delete" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=sts_content_delete&service=' . $service->ID), 'sts_content_delete_' . $service->ID)); ?>" onclick="return confirm('Slet denne service? Den tilhørende side flyttes til papirkurven, hvis den bruger STS-servicetemplaten.');">Slet</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}

function sts_content_service_edit_page() {
    if (!current_user_can('edit_pages')) {
        return;
    }
    $service_id = absint($_GET['service'] ?? 0);
    $service = $service_id ? get_post($service_id) : null;
    if ($service && $service->post_type !== 'sts_service') {
        $service = null;
    }
    $meta = array(
        'icon' => $service ? get_post_meta($service->ID, '_sts_service_icon', true) : '',
        'category' => $service ? get_post_meta($service->ID, '_sts_service_category', true) : 'ren',
        'eyebrow' => $service ? get_post_meta($service->ID, '_sts_service_eyebrow', true) : '',
        'hero_title' => $service ? get_post_meta($service->ID, '_sts_service_hero_title', true) : '',
        'hero_text' => $service ? get_post_meta($service->ID, '_sts_service_hero_text', true) : '',
        'hero_class' => $service ? get_post_meta($service->ID, '_sts_service_hero_class', true) : '',
        'image' => $service ? get_post_meta($service->ID, '_sts_service_image', true) : '',
        'benefits' => $service ? implode("\n", (array) get_post_meta($service->ID, '_sts_service_benefits', true)) : '',
        'show_about' => $service ? get_post_meta($service->ID, '_sts_service_show_about', true) : '0',
    );
    $process = $service ? (array) get_post_meta($service->ID, '_sts_service_process', true) : array();
    $slug = $service ? $service->post_name : '';
    $default_image = sts_content_default_image($slug);
    $preview = $meta['image'] ?: $default_image;
    ?>
    <div class="wrap">
        <h1><?php echo $service ? 'Redigér service' : 'Opret service'; ?></h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:900px">
            <input type="hidden" name="action" value="sts_content_save">
            <input type="hidden" name="service" value="<?php echo esc_attr($service ? $service->ID : 0); ?>">
            <?php wp_nonce_field('sts_content_save', 'sts_content_nonce'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-title">Servicetitel *</label></th>
                    <td><input class="regular-text" type="text" id="sts-title" name="title" required value="<?php echo esc_attr($service ? $service->post_title : ''); ?>">
                        <p class="description">F.eks. Erhvervsrengøring</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-slug">URL-slug *</label></th>
                    <td><input class="regular-text" type="text" id="sts-slug" name="slug" required value="<?php echo esc_attr($slug); ?>">
                        <p class="description">Siden bliver <code><?php echo esc_html(home_url('/')); ?><span id="sts-slug-preview"><?php echo esc_html($slug); ?></span>/</code></p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-category">Kategori *</label></th>
                    <td><select id="sts-category" name="category">
                        <?php foreach (sts_content_categories() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($meta['category'], $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Bestemmer hvilken hovedkategori servicen vises under.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-description">Kort beskrivelse *</label></th>
                    <td><textarea class="large-text" rows="2" id="sts-description" name="description" required><?php echo esc_textarea($service ? $service->post_excerpt : ''); ?></textarea>
                        <p class="description">Bruges på servicekortene under STS Byg / Mal / Ren.</p></td>
                </tr>
            </table>

            <h2>Hero (øverste sektion)</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-eyebrow">Eyebrow</label></th>
                    <td><input class="regular-text" type="text" id="sts-eyebrow" name="eyebrow" value="<?php echo esc_attr($meta['eyebrow']); ?>" placeholder="Servicenavn">
                        <p class="description">Den lille label over overskriften</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-hero-title">Hero-titel</label></th>
                    <td><input class="large-text" type="text" id="sts-hero-title" name="hero_title" value="<?php echo esc_attr($meta['hero_title']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-hero-text">Hero-tekst</label></th>
                    <td><textarea class="large-text" rows="3" id="sts-hero-text" name="hero_text"><?php echo esc_textarea($meta['hero_text']); ?></textarea>
                        <p class="description">Tom = den korte beskrivelse.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-hero-class">Hero-farve</label></th>
                    <td><select id="sts-hero-class" name="hero_class">
                        <option value="">Automatisk</option>
                        <?php foreach (sts_content_hero_classes() as $hero_class) : ?>
                            <option value="<?php echo esc_attr($hero_class); ?>" <?php selected($meta['hero_class'], $hero_class); ?>><?php echo esc_html(str_replace('hero-', '', $hero_class)); ?></option>
                        <?php endforeach; ?>
                    </select></td>
                </tr>
            </table>

            <h2>Fordele ved at vælge STS ApS</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-benefits">Fordele (én pr. linje)</label></th>
                    <td><textarea class="large-text" rows="7" id="sts-benefits" name="benefits"><?php echo esc_textarea($meta['benefits']); ?></textarea>
                        <p class="description">Vises som flueben-listen i „Hvad du får“-boksen.</p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-image">Servicebillede</label></th>
                    <td>
                        <input class="large-text" type="text" id="sts-image" name="image" value="<?php echo esc_attr($meta['image']); ?>" placeholder="Lad feltet være tomt for standardbillede">
                        <p>
                            <button type="button" class="button" id="sts-upload-image">Upload / vælg billede</button>
                            <button type="button" class="button" id="sts-default-image">Brug standardbillede</button>
                        </p>
                        <p class="description">Standardbillede: <code id="sts-default-image-label"><?php echo esc_html($default_image); ?></code></p>
                        <img id="sts-image-preview" src="<?php echo esc_url($preview); ?>" alt="" style="width:260px;height:160px;object-fit:cover;border:1px solid #ddd;border-radius:6px">
                    </td>
                </tr>
            </table>

            <h2>„Sådan arbejder vi“ (boks 1-4)</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="sts-process-eyebrow">Eyebrow</label></th>
                    <td><input class="regular-text" type="text" id="sts-process-eyebrow" name="process_eyebrow" value="<?php echo esc_attr($process['eyebrow'] ?? ''); ?>" placeholder="Sådan arbejder vi"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="sts-process-title">Overskrift</label></th>
                    <td><input class="large-text" type="text" id="sts-process-title" name="process_title" value="<?php echo esc_attr($process['title'] ?? ''); ?>" placeholder="En struktureret proces fra første kontakt til færdig opgave."></td>
                </tr>
                <?php for ($step = 1; $step <= 4; $step++) : ?>
                <tr>
                    <th scope="row">Boks <?php echo (int) $step; ?></th>
                    <td>
                        <input class="regular-text" type="text" name="process_step_<?php echo (int) $step; ?>_title" value="<?php echo esc_attr($process['steps'][$step - 1]['title'] ?? ''); ?>" placeholder="Titel">
                        <textarea class="large-text" rows="2" name="process_step_<?php echo (int) $step; ?>_description" placeholder="Beskrivelse"><?php echo esc_textarea($process['steps'][$step - 1]['description'] ?? ''); ?></textarea>
                    </td>
                </tr>
                <?php endfor; ?>
            </table>

            <h2>„Om servicen“</h2>
            <p><label><input type="checkbox" name="show_about" value="1" <?php checked($meta['show_about'], '1'); ?>> Vis „Om servicen“-sektionen på siden</label></p>
            <?php
            wp_editor($service ? $service->post_content : '', 'sts_full_description', array(
                'textarea_name' => 'full_description',
                'textarea_rows' => 10,
                'media_buttons' => true,
            ));
            ?>

            <p class="submit">
                <button type="submit" class="button button-primary"><?php echo $service ? 'Opdatér service' : 'Opret service'; ?></button>
                <a class="button" href="<?php echo esc_url(sts_content_admin_url('sts-services')); ?>">Annullér</a>
            </p>
        </form>
    </div>
    <script>
    jQuery(function ($) {
        var defaultImages = <?php echo wp_json_encode(sts_content_default_image_map()); ?>;
        var fallbackImage = <?php echo wp_json_encode(sts_content_default_image('')); ?>;

        function slugify(value) {
            return String(value || '').toLowerCase()
                .replace(/ø/g, 'o').replace(/å/g, 'a').replace(/æ/g, 'ae')
                .replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '-')
                .replace(/-+/g, '-').replace(/^-|-$/g, '');
        }
        function currentDefault() {
            var slug = slugify($('#sts-slug').val() || $('#sts-title').val());
            return defaultImages[slug] || fallbackImage;
        }
        function syncPreview() {
            var custom = $.trim($('#sts-image').val());
            $('#sts-image-preview').attr('src', custom || currentDefault());
            $('#sts-default-image-label').text(currentDefault());
            $('#sts-slug-preview').text(slugify($('#sts-slug').val() || $('#sts-title').val()));
        }

        $('#sts-title').on('input', function () {
            if (!$('#sts-slug').data('touched')) {
                $('#sts-slug').val(slugify(this.value));
            }
            syncPreview();
        });
        $('#sts-slug').on('input', function () { $(this).data('touched', true); syncPreview(); });
        $('#sts-image').on('input', syncPreview);
        $('#sts-default-image').on('click', function () { $('#sts-image').val(''); syncPreview(); });
        $('#sts-upload-image').on('click', function (e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Vælg servicebillede', button: { text: 'Brug billede' }, multiple: false });
            frame.on('select', function () {
                $('#sts-image').val(frame.state().get('selection').first().toJSON().url);
                syncPreview();
            });
            frame.open();
        });
        <?php if ($service) : ?>$('#sts-slug').data('touched', true);<?php endif; ?>
        syncPreview();
    });
    </script>
    <?php
}

function sts_content_default_image_map() {
    $map = array();
    foreach (sts_content_get_services() as $service) {
        $map[$service->post_name] = sts_content_default_image($service->post_name);
    }
    foreach (glob(get_template_directory() . '/assets/images/*.jpg') ?: array() as $file) {
        $slug = basename($file, '.jpg');
        $map[$slug] = get_template_directory_uri() . '/assets/images/' . basename($file);
    }
    return $map;
}

function sts_content_handle_save() {
    if (!current_user_can('edit_pages') || !isset($_POST['sts_content_nonce']) || !wp_verify_nonce($_POST['sts_content_nonce'], 'sts_content_save')) {
        wp_die('Ugyldig forespørgsel.');
    }
    $service_id = absint($_POST['service'] ?? 0);
    $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    $slug = sts_content_slug(wp_unslash($_POST['slug'] ?? '') ?: $title);
    if ($title === '' || $slug === '') {
        wp_die('Titel og slug er påkrævet.');
    }

    $postarr = array(
        'post_type' => 'sts_service',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => sanitize_textarea_field(wp_unslash($_POST['description'] ?? '')),
        'post_content' => wp_kses_post(wp_unslash($_POST['full_description'] ?? '')),
    );
    if ($service_id) {
        $postarr['ID'] = $service_id;
        $service_id = wp_update_post($postarr, true);
        $notice = 'updated';
    } else {
        $service_id = wp_insert_post($postarr, true);
        $notice = 'created';
    }
    if (is_wp_error($service_id)) {
        wp_die(esc_html($service_id->get_error_message()));
    }

    update_post_meta($service_id, '_sts_service_icon', sanitize_text_field(wp_unslash($_POST['icon'] ?? '')));
    update_post_meta($service_id, '_sts_service_category', sanitize_key($_POST['category'] ?? 'ren'));
    update_post_meta($service_id, '_sts_service_eyebrow', sanitize_text_field(wp_unslash($_POST['eyebrow'] ?? '')));
    update_post_meta($service_id, '_sts_service_hero_title', sanitize_text_field(wp_unslash($_POST['hero_title'] ?? '')));
    update_post_meta($service_id, '_sts_service_hero_text', sanitize_textarea_field(wp_unslash($_POST['hero_text'] ?? '')));
    update_post_meta($service_id, '_sts_service_hero_class', in_array($_POST['hero_class'] ?? '', sts_content_hero_classes(), true) ? $_POST['hero_class'] : '');
    update_post_meta($service_id, '_sts_service_show_about', empty($_POST['show_about']) ? '0' : '1');
    update_post_meta($service_id, '_sts_service_image', esc_url_raw(wp_unslash($_POST['image'] ?? '')));
    update_post_meta($service_id, '_sts_service_benefits', array_values(array_filter(array_map('sanitize_text_field', preg_split('/\r\n|\r|\n/', wp_unslash($_POST['benefits'] ?? ''))))));

    $process = array(
        'eyebrow' => sanitize_text_field(wp_unslash($_POST['process_eyebrow'] ?? '')),
        'title' => sanitize_text_field(wp_unslash($_POST['process_title'] ?? '')),
        'steps' => array(),
    );
    for ($step = 1; $step <= 4; $step++) {
        $process['steps'][] = array(
            'title' => sanitize_text_field(wp_unslash($_POST['process_step_' . $step . '_title'] ?? '')),
            'description' => sanitize_textarea_field(wp_unslash($_POST['process_step_' . $step . '_description'] ?? '')),
        );
    }
    update_post_meta($service_id, '_sts_service_process', $process);

    sts_content_sync_service_page($service_id);

    wp_safe_redirect(sts_content_admin_url('sts-service-edit', array('service' => $service_id, 'sts_notice' => $notice)));
    exit;
}
add_action('admin_post_sts_content_save', 'sts_content_handle_save');

function sts_content_handle_duplicate() {
    $service_id = absint($_GET['service'] ?? 0);
    if (!$service_id || !current_user_can('edit_pages') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_content_duplicate_' . $service_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    $original = get_post($service_id);
    if (!$original || $original->post_type !== 'sts_service') {
        wp_die('Servicen findes ikke.');
    }
    $copy_id = wp_insert_post(array(
        'post_type' => 'sts_service',
        'post_status' => 'draft',
        'post_title' => $original->post_title . ' (kopi)',
        'post_name' => wp_unique_post_slug($original->post_name . '-kopi', 0, 'draft', 'sts_service', 0),
        'post_excerpt' => $original->post_excerpt,
        'post_content' => $original->post_content,
    ), true);
    if (is_wp_error($copy_id)) {
        wp_die(esc_html($copy_id->get_error_message()));
    }
    foreach (array('_sts_service_icon', '_sts_service_category', '_sts_service_eyebrow', '_sts_service_hero_title', '_sts_service_hero_text', '_sts_service_hero_class', '_sts_service_show_about', '_sts_service_image', '_sts_service_benefits', '_sts_service_process') as $key) {
        update_post_meta($copy_id, $key, get_post_meta($service_id, $key, true));
    }
    wp_safe_redirect(sts_content_admin_url('sts-service-edit', array('service' => $copy_id, 'sts_notice' => 'duplicated')));
    exit;
}
add_action('admin_post_sts_content_duplicate', 'sts_content_handle_duplicate');

function sts_content_handle_delete() {
    $service_id = absint($_GET['service'] ?? 0);
    if (!$service_id || !current_user_can('delete_pages') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_content_delete_' . $service_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    $page = sts_content_service_page($service_id);
    if ($page && sts_content_page_uses_template($page->ID)) {
        wp_trash_post($page->ID);
    }
    wp_trash_post($service_id);
    wp_safe_redirect(sts_content_admin_url('sts-services', array('sts_notice' => 'deleted')));
    exit;
}
add_action('admin_post_sts_content_delete', 'sts_content_handle_delete');

function sts_content_handle_template() {
    $service_id = absint($_GET['service'] ?? 0);
    if (!$service_id || !current_user_can('edit_pages') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'sts_content_template_' . $service_id)) {
        wp_die('Ugyldig forespørgsel.');
    }
    sts_content_sync_service_page($service_id);
    wp_safe_redirect(sts_content_admin_url('sts-services', array('sts_notice' => 'template')));
    exit;
}
add_action('admin_post_sts_content_template', 'sts_content_handle_template');

function sts_content_import_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $message = '';
    if (isset($_POST['sts_import_nonce']) && wp_verify_nonce($_POST['sts_import_nonce'], 'sts_import')) {
        $message = sprintf('%d serviceydelser blev importeret og forbundet til deres sider.', sts_content_import_services());
    }
    if (isset($_POST['sts_seed_nonce']) && wp_verify_nonce($_POST['sts_seed_nonce'], 'sts_seed')) {
        $message = sprintf('%d serviceydelser fik hentet indhold fra de eksisterende sider.', sts_content_seed_all_services(true));
    }
    ?>
    <div class="wrap">
        <h1>Importér original data</h1>
        <p>Importerer serviceydelser fra <code>supertotalservice.dk/data/services.json</code>. Eksisterende services med samme slug opdateres, og hver service forbindes med den rigtige side.</p>
        <?php if ($message) : ?><div class="notice notice-success"><p><?php echo esc_html($message); ?></p></div><?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('sts_import', 'sts_import_nonce'); ?>
            <p><button class="button button-primary" type="submit">Importér / synkronisér</button></p>
        </form>
        <hr>
        <h2>Hent indhold fra de nuværende servicesider</h2>
        <p>Læser hero, „Sådan arbejder vi“-bokse, fordele og billede fra temaets <code>page-&lt;slug&gt;.php</code> og lægger dem ind i serviceeditoren. Overskriver de nuværende feltværdier.</p>
        <form method="post" onsubmit="return confirm('Overskriv servicefelterne med indholdet fra temaets sider?');">
            <?php wp_nonce_field('sts_seed', 'sts_seed_nonce'); ?>
            <p><button class="button" type="submit">Hent indhold fra siderne</button></p>
        </form>
    </div>
    <?php
}
