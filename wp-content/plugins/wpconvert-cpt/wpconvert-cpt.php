<?php
/**
 * Plugin Name: WPConvert CPT
 * Plugin URI: https://wpconvert.ai
 * Description: Custom post types auto-detected from your WPConvert theme. Activate detected CPTs from the WPConvert admin notice; edit posts in the standard WordPress admin; edits appear live on the front-end via the WPConvert Editor. Detected product collections can be imported into WooCommerce (Ship 4c.6) with one click.
 * Version: 1.4.5
 * Author: WPConvert.ai
 * Author URI: https://wpconvert.ai
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpconvert-cpt
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * NOTE: We intentionally do NOT declare `Requires Plugins: wpconvert-editor`.
 * The WPConvert Editor ships as a THEME INCLUDE (loaded via require_once
 * from the converted theme's functions.php), not as a standalone plugin —
 * so the WordPress 6.5+ dependency check would refuse activation. Instead,
 * every callback in this plugin guards itself with
 * `function_exists('wpconvert_editor_is_pro')` and no-ops safely when the
 * editor isn't loaded.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WPCONVERT_CPT_VERSION' ) ) {
    define( 'WPCONVERT_CPT_VERSION', '1.4.5' );
}

/**
 * WPConvert CPT Runtime  (Ship 2 of the CPT support feature — EC-CPT-002)
 *
 * Ship 4c.0 — this file moved from `theme/inc/wpconvert-cpt.php` (theme-bundled
 * require_once) to a standalone WordPress plugin installed under
 * `wp-content/plugins/wpconvert-cpt/wpconvert-cpt.php`. The behavior surface
 * (Ship 1-4b) is byte-identical; only the packaging changed.
 *
 * Companion to inc/wpconvert-editor.php (still theme-bundled). Registers
 * custom post types that the Ship-1 detector found at build time and that
 * an administrator has since opted in to via wp_options['wpconvert_cpts'].
 *
 * Bundled inside every WPConvert theme as `plugins/wpconvert-cpt.zip`.
 *
 * Safety guarantees (verified by tests/ec-cpt-002-runtime-cpt.test.js):
 *
 *   1. Tier gate. Every public callback short-circuits to `return` unless
 *      `wpconvert_editor_is_pro()` is defined AND returns true. Starter
 *      conversions therefore never register a single post type, even if
 *      the file is loaded.
 *
 *   2. Inert-until-opt-in. Registration reads `wp_options['wpconvert_cpts']`.
 *      The option is seeded to `[]` (autoload=no) by `add_option`, so the
 *      registration loop iterates nothing until an admin populates it
 *      (Ship 3 will add the activation UI; for now this happens via
 *      WP-CLI or direct DB write).
 *
 *   3. Reversibility. Removing the file, or setting
 *      `wp_options['wpconvert_cpts']` back to `[]`, reverts to pre-Ship-2
 *      behavior. No data migration, no DB schema change.
 *
 *   4. Defensive failure. Every public callback is wrapped in
 *      try { ... } catch ( \Throwable $e ) { error_log(...); return; }.
 *      A malformed JSON manifest, a thrown `register_post_type` (e.g.
 *      because of a plugin filter), or a missing core function all fail
 *      closed, not open.
 *
 *   5. Collision-safe. `post_type_exists($slug)` is checked before every
 *      `register_post_type` call. If another plugin already owns the
 *      slug, we skip — we do NOT clobber.
 *
 *   6. Native-blog override (resolves TODO[EC-CPT-002] from Ship 1).
 *      When `WPCONVERT_NATIVE_BLOG === true` (set by generator for
 *      Pro/Agency themes whose source already had a blog), any candidate
 *      with a `BLOG_POSTS`-shaped field set is skipped — WP's built-in
 *      `post` type owns that content.
 *
 *   7. Minimum-version floor. PHP 7.4 / WP 5.9 are required. Older
 *      environments get an admin notice and zero registrations.
 *
 *   8. Slug hardening. Slugs are normalized to lowercase alphanumeric +
 *      underscores, capped at 20 chars (WP's post-type-name limit), and
 *      checked against a reserved list (`post`, `page`, `attachment`,
 *      `revision`, `nav_menu_item`, etc.). Invalid slugs are silently
 *      skipped rather than letting WP throw.
 *
 * @package WPConvert
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─────────────────────────────────────────────
 * 0. CONSTANTS
 * ───────────────────────────────────────────── */

if ( ! defined( 'WPCONVERT_CPT_MIN_PHP' ) ) {
    define( 'WPCONVERT_CPT_MIN_PHP', '7.4' );
}
if ( ! defined( 'WPCONVERT_CPT_MIN_WP' ) ) {
    define( 'WPCONVERT_CPT_MIN_WP', '5.9' );
}
if ( ! defined( 'WPCONVERT_CPT_OPTION_KEY' ) ) {
    define( 'WPCONVERT_CPT_OPTION_KEY', 'wpconvert_cpts' );
}
if ( ! defined( 'WPCONVERT_CPT_MANIFEST_REL' ) ) {
    define( 'WPCONVERT_CPT_MANIFEST_REL', '/assets/data/cpt-candidates.json' );
}
// Ship 4c.6 / A2 — v2 adds OPTIONAL candidate keys (`intent`) and item
// keys (`gallery`, `custom`). All reads are additive: absent keys mean
// the v1 CPT path, so v1 manifests migrate with no transformation.
if ( ! defined( 'WPCONVERT_CPT_SCHEMA_VERSION' ) ) {
    define( 'WPCONVERT_CPT_SCHEMA_VERSION', 2 );
}

// Standard WP constants used by A2 caching. WP defines these in
// wp-includes/default-constants.php; we self-define so the plugin
// stays robust under stripped-down test harnesses.
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) define( 'MINUTE_IN_SECONDS', 60 );
if ( ! defined( 'HOUR_IN_SECONDS' ) ) define( 'HOUR_IN_SECONDS', 3600 );
if ( ! defined( 'DAY_IN_SECONDS' ) ) define( 'DAY_IN_SECONDS', 86400 );

/**
 * Built-in WordPress post types and reserved internals. A user-chosen
 * slug that matches any of these is rejected with no registration.
 *
 * Source: WP core register_post_type() docs +
 * https://developer.wordpress.org/reference/functions/register_post_type/
 */
if ( ! defined( 'WPCONVERT_CPT_RESERVED_SLUGS' ) ) {
    define( 'WPCONVERT_CPT_RESERVED_SLUGS', serialize( array(
        'post', 'page', 'attachment', 'revision', 'nav_menu_item',
        'custom_css', 'customize_changeset', 'oembed_cache',
        'user_request', 'wp_block', 'wp_template', 'wp_template_part',
        'wp_global_styles', 'wp_navigation', 'wp_font_family',
        'wp_font_face', 'action', 'author', 'order', 'theme', 'type',
        // Ship 4c.6 / A1 — WooCommerce core post types. A user-forced
        // activation must never silently override WC's own types.
        'product', 'product_variation', 'shop_order', 'shop_coupon',
        'shop_order_refund', 'shop_order_placehold', 'product_visibility',
    ) ) );
}

/**
 * Field keys that, when present together in a candidate, indicate the
 * candidate is a clone of WP's native `post` shape (title, slug,
 * excerpt, content/body, author/byline, date/event_date).
 *
 * Three or more matches + `WPCONVERT_NATIVE_BLOG === true` triggers the
 * Ship-1 EC-CPT-002 override: don't register a CPT that duplicates the
 * native blog.
 */
if ( ! defined( 'WPCONVERT_CPT_BLOG_KEYS' ) ) {
    define( 'WPCONVERT_CPT_BLOG_KEYS', serialize( array(
        'heading',       // title -> heading (remapped)
        'custom_slug',   // slug  -> custom_slug (remapped)
        'summary',       // excerpt -> summary (remapped)
        'body',          // content -> body (remapped)
        'byline',        // author -> byline (remapped)
        'event_date',    // date -> event_date (remapped)
        // Plus the originals in case the candidate is older than the remap.
        'title', 'slug', 'excerpt', 'content', 'author', 'date',
    ) ) );
}

/* ─────────────────────────────────────────────
 * 1. CAPABILITY PROBES
 * ───────────────────────────────────────────── */

/**
 * True iff the current request is allowed to act on CPTs at all.
 *
 * This is the FIRST line of every public callback. If it returns false
 * for any reason — tier, missing function, version too old — the
 * callback exits with no side effects.
 *
 * @return bool
 */
function wpconvert_cpt_should_run() {
    if ( ! function_exists( 'wpconvert_editor_is_pro' ) ) {
        return false;
    }
    if ( ! wpconvert_editor_is_pro() ) {
        return false;
    }
    if ( ! wpconvert_cpt_meets_php_version() ) {
        return false;
    }
    if ( ! wpconvert_cpt_meets_wp_version() ) {
        return false;
    }
    return true;
}

/**
 * @return bool
 */
function wpconvert_cpt_meets_php_version() {
    return version_compare( PHP_VERSION, WPCONVERT_CPT_MIN_PHP, '>=' );
}

/**
 * @return bool
 */
function wpconvert_cpt_meets_wp_version() {
    if ( ! function_exists( 'get_bloginfo' ) ) {
        // Outside of WordPress (unit test, etc.) — let upstream code handle.
        return true;
    }
    $wp_version = get_bloginfo( 'version' );
    if ( ! is_string( $wp_version ) || $wp_version === '' ) {
        return true; // Best-effort: don't block on a missing version string.
    }
    return version_compare( $wp_version, WPCONVERT_CPT_MIN_WP, '>=' );
}

/* ─────────────────────────────────────────────
 * 2. STORAGE
 * ───────────────────────────────────────────── */

/**
 * Seed `wp_options['wpconvert_cpts']` to `[]` with autoload=no on first
 * load. Idempotent — `add_option` no-ops if the key already exists.
 *
 * autoload=no is important: the option will eventually carry user-
 * provided CPT configs (field maps, labels, etc.) and we don't want to
 * pay the memory tax of loading it on every request. WP only reads it
 * when the registration callback explicitly calls `get_option`.
 */
function wpconvert_cpt_seed_options() {
    if ( ! wpconvert_cpt_should_run() ) {
        return;
    }
    try {
        if ( function_exists( 'add_option' ) ) {
            add_option( WPCONVERT_CPT_OPTION_KEY, array(), '', 'no' );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt seed_options: ' . $e->getMessage() );
        }
    }
}

/* ─────────────────────────────────────────────
 * 2.1 PLUGIN LIFECYCLE HOOKS  (Ship 4c.6 / A0)
 *
 * Currently the plugin has implicit lifecycle (relies on after_setup_theme
 * + admin_init heals). Standard WordPress plugins declare activation,
 * deactivation, and uninstall hooks so that:
 *
 *   - On activation we record an installed_at timestamp (drives the
 *     C8 first-run welcome notice) and mark rewrite rules dirty.
 *   - On deactivation we flush rewrite rules so URLs don't keep
 *     pointing at our (now-unregistered) CPTs, and we clear the
 *     manifest cache so a fresh install of a different theme isn't
 *     served stale data.
 *   - On uninstall (a separate uninstall.php per WP convention) we
 *     respect a `wpconvert_cpts_purge_on_uninstall` flag; default is
 *     to PRESERVE user data (option entries + posts) so the user can
 *     reinstall and pick up where they left off.
 *
 * Critical safety invariant (never-activate user): NONE of these hooks
 * activate a CPT or modify a CPT post. They only touch plumbing
 * (timestamps, transients, rewrite rules). A user who installs the
 * plugin and never clicks Activate still has byte-identical front-end
 * output before and after these hooks run.
 *
 * Option keys introduced here (all autoload='no'):
 *
 *   wpconvert_cpts_installed_at    int — unix timestamp of first
 *                                       activation. Drives C8.
 *   wpconvert_cpts_wizard_dismissed int — 0 or 1. Set by user clicking
 *                                       "Dismiss" on the welcome notice.
 *   wpconvert_cpts_purge_on_uninstall bool — opt-in flag for uninstall.php.
 *   wpconvert_cpts_needs_flush     bool — set true on activation so
 *                                       wpconvert_cpt_maybe_flush_rewrite_rules
 *                                       picks it up on the next init.
 * ───────────────────────────────────────────── */

/**
 * Ship 4c.6 / A0 — plugin activation hook callback.
 *
 * Idempotent and safe to call multiple times. Reactivating after a
 * deactivation preserves the original installed_at and any existing CPT
 * registrations in wp_options['wpconvert_cpts'] — we never auto-activate
 * a CPT and never reset wizard dismissal.
 *
 * Bypasses the wpconvert_cpt_should_run() tier gate intentionally: a
 * Starter user might activate the plugin to evaluate, then upgrade to
 * Pro and find the timestamp already recorded. The other functions in
 * this plugin still guard themselves with should_run(), so no side
 * effects materialize on Starter.
 */
function wpconvert_cpt_on_activation() {
    try {
        if ( ! function_exists( 'add_option' ) ) return;
        // Idempotent: only sets if missing.
        add_option( 'wpconvert_cpts_installed_at',     time(), '', 'no' );
        add_option( 'wpconvert_cpts_wizard_dismissed', 0,      '', 'no' );
        add_option( 'wpconvert_cpts_purge_on_uninstall', 0,    '', 'no' );
        // Always flag rewrite flush — next init picks it up.
        if ( function_exists( 'update_option' ) ) {
            update_option( 'wpconvert_cpts_needs_flush', 1, 'no' );
        }
        // Seed the main option in case the theme hook ordering means
        // after_setup_theme hasn't run yet on this particular activation.
        add_option( WPCONVERT_CPT_OPTION_KEY, array(), '', 'no' );
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt on_activation: ' . $e->getMessage() );
        }
    }
}

/**
 * Ship 4c.6 / A0 — plugin deactivation hook callback.
 *
 * Reversible: removing the file or deactivating the plugin returns the
 * site to pre-Ship-2 behavior with no data loss. We DO NOT delete the
 * main CPT config option, any posts, or any post meta. We DO:
 *
 *   - Flush rewrite rules with empty input so the rewriter forgets our
 *     CPT permalink patterns (URLs would otherwise 404 cleanly, but
 *     this is cleaner — WP doesn't keep dead rewrite rules in memory).
 *   - Delete the manifest cache transient (A2) so a re-activation
 *     reads fresh data.
 *   - Delete any in-progress import lock transients.
 *
 * Safe to call even when the plugin isn't fully loaded (no calls to
 * should_run, every WP function probed via function_exists).
 */
function wpconvert_cpt_on_deactivation() {
    try {
        if ( function_exists( 'flush_rewrite_rules' ) ) {
            flush_rewrite_rules( false );
        }
        if ( function_exists( 'delete_transient' ) ) {
            // A2 manifest cache + any future import-lock transients.
            // Globbing isn't possible at the WP API level; we delete by
            // known key prefix. The exact A2 key includes a hash of the
            // manifest path + mtime, but we also keep a "currently known
            // key" pointer in an option to make this O(1).
            $known = get_option( 'wpconvert_cpts_manifest_cache_key', '' );
            if ( is_string( $known ) && $known !== '' ) {
                delete_transient( $known );
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt on_deactivation: ' . $e->getMessage() );
        }
    }
}

/**
 * Ship 4c.6 / A0 — shared uninstall worker. Called from uninstall.php
 * (which WordPress invokes at uninstall time, only if it exists at the
 * plugin root). Exists as a function so we can test it under the
 * scenario runner.
 *
 * Behavior:
 *   - If wpconvert_cpts_purge_on_uninstall option is truthy, deletes
 *     ALL plugin options + transients we own. Does NOT trash CPT posts
 *     even on purge — they're user content; deleting them silently on
 *     uninstall would be a data-loss surprise.
 *   - If the option is falsy (the default), no-ops. The user can
 *     reinstall and pick up where they left off.
 */
function wpconvert_cpt_run_uninstall_purge() {
    try {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'delete_option' ) ) return;
        $purge = (int) get_option( 'wpconvert_cpts_purge_on_uninstall', 0 );
        if ( ! $purge ) return;

        // Capture the manifest cache transient key BEFORE we delete the
        // option that holds it, otherwise the lookup would come back empty.
        $known_transient_key = '';
        if ( function_exists( 'delete_transient' ) ) {
            $maybe = get_option( 'wpconvert_cpts_manifest_cache_key', '' );
            if ( is_string( $maybe ) && $maybe !== '' ) {
                $known_transient_key = $maybe;
            }
        }

        $option_keys = array(
            WPCONVERT_CPT_OPTION_KEY,
            'wpconvert_cpts_installed_at',
            'wpconvert_cpts_wizard_dismissed',
            'wpconvert_cpts_purge_on_uninstall',
            'wpconvert_cpts_needs_flush',
            'wpconvert_cpt_imported_state',
            'wpconvert_cpts_manifest_cache_key',
            'wpconvert_cpts_diagnostics_log',
        );
        foreach ( $option_keys as $k ) {
            delete_option( $k );
        }

        if ( $known_transient_key !== '' && function_exists( 'delete_transient' ) ) {
            delete_transient( $known_transient_key );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt run_uninstall_purge: ' . $e->getMessage() );
        }
    }
}

/**
 * Read and return the active-CPT config map. Always returns an array;
 * never null. Malformed values are coerced to `[]`.
 *
 * Shape (each entry):
 *
 *   '<section_key>' => array(
 *       'enabled'      => true,
 *       'post_type'    => 'pancake_menu_item',  // <= 20 chars, [a-z0-9_]
 *       'singular'     => 'Menu Item',
 *       'plural'       => 'Menu Items',
 *       'menu_icon'    => 'dashicons-list-view',
 *       'public'       => true,
 *       'has_archive'  => true,
 *       'show_in_rest' => true,
 *       'rewrite_slug' => 'menu-item',
 *       'supports'     => array('title','editor','thumbnail','custom-fields'),
 *   )
 *
 * @return array
 */
function wpconvert_cpt_get_active_cpts() {
    if ( ! wpconvert_cpt_should_run() ) {
        return array();
    }
    try {
        if ( ! function_exists( 'get_option' ) ) {
            return array();
        }
        $val = get_option( WPCONVERT_CPT_OPTION_KEY, array() );
        if ( ! is_array( $val ) ) {
            return array();
        }
        return $val;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt get_active_cpts: ' . $e->getMessage() );
        }
        return array();
    }
}

/**
 * Read assets/data/cpt-candidates.json and return its `candidates`
 * array (always an array, never null). Defensive against missing file,
 * empty file, malformed JSON, and unexpected schema_version.
 *
 * Ship 4c.6 / A2 — adds per-request static + cross-request transient
 * caching so repeat callers within the same render (admin dashboard
 * tabs, loop swap, heal passes) don't all hit the disk.
 *
 * Ship 4c.6 / A1 — when schema_version is older than the plugin's,
 * runs the migration ladder before returning. When newer, logs a
 * diagnostics entry and returns empty (plugin is older than manifest;
 * user must update).
 *
 * @return array  array of candidate dicts (possibly empty)
 */
function wpconvert_cpt_get_candidates_manifest() {
    if ( ! wpconvert_cpt_should_run() ) {
        return array();
    }

    // A2 — per-request static (eliminates repeat disk reads within one
    // pageload). The cache key INCLUDES the manifest path+mtime so
    // theme switches mid-request (rare but possible during tests)
    // still pick up the right manifest.
    static $req_cache = array();

    try {
        if ( ! function_exists( 'get_template_directory' ) ) {
            return array();
        }
        $path = get_template_directory() . WPCONVERT_CPT_MANIFEST_REL;
        if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
            return array();
        }

        $mtime = @filemtime( $path );
        $mtime = $mtime === false ? 0 : (int) $mtime;
        $req_key = md5( $path . '|' . $mtime );

        if ( isset( $req_cache[ $req_key ] ) ) {
            return $req_cache[ $req_key ];
        }

        // A2 — cross-request transient. Key includes filemtime() so any
        // theme/plugin update naturally cache-busts.
        $transient_key = 'wpconvert_cpts_manifest_cache_' . $req_key;
        if ( function_exists( 'get_transient' ) ) {
            $cached = get_transient( $transient_key );
            if ( is_array( $cached ) ) {
                $req_cache[ $req_key ] = $cached;
                return $cached;
            }
        }

        $raw = @file_get_contents( $path );
        if ( ! is_string( $raw ) || $raw === '' ) {
            $req_cache[ $req_key ] = array();
            return array();
        }
        $decoded = json_decode( $raw, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $req_cache[ $req_key ] = array();
            return array();
        }
        if ( ! is_array( $decoded ) || empty( $decoded['candidates'] ) ) {
            $req_cache[ $req_key ] = array();
            return array();
        }

        // A1 — run migration ladder before extracting candidates.
        $migrated = wpconvert_cpt_migrate_manifest( $decoded );
        if ( ! is_array( $migrated ) || empty( $migrated['candidates'] ) ) {
            $req_cache[ $req_key ] = array();
            return array();
        }

        $candidates = is_array( $migrated['candidates'] ) ? $migrated['candidates'] : array();
        $req_cache[ $req_key ] = $candidates;

        // A2 — cache for 1 hour. filemtime in the key invalidates
        // automatically on theme update; we also track the active
        // cache key in an option so the deactivation hook + the
        // uninstall.php purge can clear it without globbing.
        if ( function_exists( 'set_transient' ) ) {
            set_transient( $transient_key, $candidates, HOUR_IN_SECONDS );
        }
        if ( function_exists( 'update_option' ) ) {
            $prev = get_option( 'wpconvert_cpts_manifest_cache_key', '' );
            if ( $prev !== $transient_key ) {
                // When the key changes (manifest mtime advanced), bust
                // the previous transient so we don't leave orphans.
                if ( is_string( $prev ) && $prev !== '' && function_exists( 'delete_transient' ) ) {
                    delete_transient( $prev );
                }
                update_option( 'wpconvert_cpts_manifest_cache_key', $transient_key, 'no' );
            }
        }
        return $candidates;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt get_candidates_manifest: ' . $e->getMessage() );
        }
        return array();
    }
}

/**
 * Ship 4c.6 / A1 — manifest schema migration ladder.
 *
 * The previous behavior was "return empty when schema_version !=
 * WPCONVERT_CPT_SCHEMA_VERSION", which would silently break every
 * existing theme the day we bump the constant. This function walks
 * the ladder: older manifests are upgraded in-memory; newer manifests
 * are refused with a diagnostic log entry.
 *
 * In-memory only: we don't rewrite the manifest file (theme files are
 * read-only at runtime). The migrations are pure functions, called
 * once per (request × file mtime) thanks to the A2 cache.
 *
 * @param array $decoded  Decoded manifest JSON (must have at least
 *                        schema_version + candidates keys)
 * @return array|null     Migrated manifest, or empty on failure
 */
function wpconvert_cpt_migrate_manifest( $decoded ) {
    if ( ! is_array( $decoded ) ) return array();

    $sv      = isset( $decoded['schema_version'] ) ? (int) $decoded['schema_version'] : 0;
    $current = (int) WPCONVERT_CPT_SCHEMA_VERSION;

    if ( $sv === $current ) {
        return $decoded;
    }

    if ( $sv > $current ) {
        // Manifest is newer than plugin → can't safely use it. Log to
        // diagnostics so the support tab can surface this.
        wpconvert_cpt_log_diagnostic(
            'manifest_schema_too_new',
            sprintf(
                'Manifest schema_version=%d but plugin understands up to %d. Update the plugin.',
                $sv, $current
            )
        );
        return array();
    }

    // sv < current → walk the ladder.
    $migrators = array(
        // Future entries take the form:
        //   3 => 'wpconvert_cpt_migrate_v2_to_v3',
        // Each migrator takes the FULL decoded manifest and returns
        // it shaped for the target version. Pure functions.
        2 => 'wpconvert_cpt_migrate_v1_to_v2',
    );

    try {
        $cur = $decoded;
        for ( $target = $sv + 1; $target <= $current; $target++ ) {
            if ( ! isset( $migrators[ $target ] ) ) {
                wpconvert_cpt_log_diagnostic(
                    'manifest_migration_missing',
                    sprintf(
                        'No migrator registered for schema v%d → v%d. Skipping.',
                        $target - 1, $target
                    )
                );
                return array();
            }
            $fn = $migrators[ $target ];
            if ( ! is_callable( $fn ) ) {
                wpconvert_cpt_log_diagnostic(
                    'manifest_migration_failed',
                    sprintf( 'Migrator %s for v%d not callable.', (string) $fn, $target )
                );
                return array();
            }
            $cur = call_user_func( $fn, $cur );
            if ( ! is_array( $cur ) ) {
                wpconvert_cpt_log_diagnostic(
                    'manifest_migration_failed',
                    sprintf( 'Migrator for v%d returned non-array.', $target )
                );
                return array();
            }
            $cur['schema_version'] = $target;
        }
        wpconvert_cpt_log_diagnostic(
            'manifest_migration_ok',
            sprintf( 'Migrated manifest from schema v%d to v%d.', $sv, $current )
        );
        return $cur;
    } catch ( \Throwable $e ) {
        wpconvert_cpt_log_diagnostic( 'manifest_migration_threw', $e->getMessage() );
        return array();
    }
}

/**
 * Ship 4c.6 / A2 — v1 → v2 manifest migrator.
 *
 * v2 only ADDS optional keys (candidate `intent`, item `gallery` /
 * `custom`); a v1 manifest is a valid v2 manifest whose candidates all
 * take the CPT path. Identity transform.
 *
 * @param array $decoded
 * @return array
 */
function wpconvert_cpt_migrate_v1_to_v2( $decoded ) {
    return $decoded;
}

/**
 * Ship 4c.6 — append a diagnostics entry to wp_options['wpconvert_cpts_diagnostics_log'].
 *
 * Capped at the last 100 entries to avoid unbounded growth. autoload=no
 * because most pages never need this. Drives the C5 Diagnostics tab.
 *
 * Idempotent on duplicate code+detail within the same minute (avoid
 * noisy spam from repeated `get_candidates_manifest` calls within one
 * pageload — though the A2 cache should make this rare anyway).
 *
 * @param string $code   Short machine-readable code (e.g. 'manifest_schema_too_new')
 * @param string $detail Human-readable message
 */
function wpconvert_cpt_log_diagnostic( $code, $detail = '' ) {
    if ( ! function_exists( 'get_option' ) || ! function_exists( 'update_option' ) ) return;
    try {
        $log = get_option( 'wpconvert_cpts_diagnostics_log', array() );
        if ( ! is_array( $log ) ) $log = array();
        $now = time();
        // Dedup within 60s for same code+detail.
        $last = end( $log );
        if ( is_array( $last )
            && ( $last['code'] ?? '' ) === $code
            && ( $last['detail'] ?? '' ) === $detail
            && abs( $now - (int) ( $last['ts'] ?? 0 ) ) < 60
        ) {
            return;
        }
        $log[] = array( 'ts' => $now, 'code' => (string) $code, 'detail' => (string) $detail );
        if ( count( $log ) > 100 ) {
            $log = array_slice( $log, -100 );
        }
        update_option( 'wpconvert_cpts_diagnostics_log', $log, 'no' );
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt log_diagnostic: ' . $e->getMessage() );
        }
    }
}

/**
 * Ship 4c.2 — orphan-candidate guard helper.
 *
 * Scan the theme's PHP files (root + `inc/`, `template-parts/`) for the
 * set of `data-wpc-cpt-candidate="<sha1>"` attributes that actually
 * landed during the build. Candidates whose section_key is absent here
 * are "orphans" — the detector found a content-shaped array in source
 * but the stamper couldn't match the rendered HTML, so the FE has no
 * template to clone. Activating an orphan is a silent dead-end: posts
 * get created in wp-admin, edits save fine, but the rendered page never
 * updates. The activation UI uses this set to hide those candidates.
 *
 * Result is cached for the duration of the request (no I/O re-cost on
 * repeated calls).
 *
 * Safety caps:
 *   - Max 5 MB per file (skips bundled JS blobs that occasionally land
 *     in theme dirs).
 *   - Recurse only into `inc/` and `template-parts/` (the conventional
 *     WP locations); deeper traversal would be a perf footgun on large
 *     themes with vendor/ or build artefacts.
 *
 * @return array<string,true>  Map of section_key => true
 */
/**
 * Ship 4c.3 — return the set of field names stamped on the rendered
 * theme for a given section_key.
 *
 * Real-world example (Smile Dental, May 2026): the doctor source array
 * declares 5 fields (name, credentials, bio, href, imgKey) but the
 * About.tsx card template only renders 3 of them (name, credentials,
 * imgKey). The loop swap can only substitute fields that have a
 * `data-wpc-cpt-field="<key>"` stamp inside the template, so editing
 * `bio` in the WP editor produces no visible change — confusing the
 * user into believing the plugin is broken.
 *
 * This helper underpins `wpconvert_cpt_get_editable_fields_for_cpt`
 * which filters the WP editor + ACF field group to only the fields
 * the user can ACTUALLY edit via the front-end loop swap. (Image
 * fields are preserved separately because they also drive the
 * featured-image flow which is independent of DOM stamping.)
 *
 * The scanner walks the same directories as the orphan guard
 * (`wpconvert_cpt_get_stamped_section_keys`): theme root + `inc/` +
 * `template-parts/`. For each PHP file, it finds every candidate
 * stamp and captures the `data-wpc-cpt-field` values inside a bounded
 * window (~16 KB) that follows the stamp. The window heuristic is
 * deliberately generous to cover deeply nested cards, but bounded to
 * keep large templates fast.
 *
 * @param string $section_key 40-char hex SHA-1 of the source array
 * @return string[] Field names found in stamps (no order guarantee)
 */
function wpconvert_cpt_get_stamped_fields_for_section_key( $section_key ) {
    $map = wpconvert_cpt_get_stamped_fields_map();
    $section_key = (string) $section_key;
    if ( $section_key === '' ) return array();
    return isset( $map[ $section_key ] ) ? array_keys( $map[ $section_key ] ) : array();
}

/**
 * Internal: build (and cache for one request) the full
 *   section_key  →  array<field_name, true>
 * map by scanning theme PHP files for stamps.
 *
 * Cached statically because both the meta box renderer and the save
 * handler may call this many times per request; rescanning the theme
 * dir on every call would be wasteful.
 *
 * @return array<string, array<string, bool>>
 */
function wpconvert_cpt_get_stamped_fields_map() {
    static $cache = null;
    if ( $cache !== null ) return $cache;
    $cache = array();

    if ( ! function_exists( 'get_template_directory' ) ) return $cache;
    $td = (string) get_template_directory();
    if ( $td === '' || ! is_dir( $td ) ) return $cache;

    $dirs = array( $td );
    foreach ( array( 'inc', 'template-parts' ) as $sub ) {
        $cd = $td . '/' . $sub;
        if ( is_dir( $cd ) ) $dirs[] = $cd;
    }

    $size_cap   = 5 * 1024 * 1024;
    $window_len = 16 * 1024;

    try {
        foreach ( $dirs as $dir ) {
            $files = @scandir( $dir );
            if ( ! is_array( $files ) ) continue;
            foreach ( $files as $fname ) {
                if ( substr( $fname, -4 ) !== '.php' ) continue;
                $full = $dir . '/' . $fname;
                if ( ! is_file( $full ) ) continue;
                $size = @filesize( $full );
                if ( $size === false || $size > $size_cap ) continue;
                $contents = @file_get_contents( $full );
                if ( ! is_string( $contents ) || $contents === '' ) continue;

                // Collect every candidate stamp in this file FIRST, so we can
                // bound each section's field-scan window by the next stamp's
                // position. Without bounding, two sections within 16 KB of
                // each other bled their field stamps into one another — and
                // on real landing pages multiple sections per page is the
                // norm, not the exception.
                $stamp_re = '/data-wpc-cpt-candidate="([a-f0-9]{40})"/';
                if ( ! preg_match_all( $stamp_re, $contents, $sm, PREG_OFFSET_CAPTURE ) ) continue;

                $positions = array();
                foreach ( $sm[0] as $i => $hit ) {
                    $positions[] = array(
                        'sk'  => (string) $sm[1][ $i ][0],
                        'pos' => (int) $hit[1],
                    );
                }
                $n_pos = count( $positions );
                for ( $i = 0; $i < $n_pos; $i++ ) {
                    $sk         = $positions[ $i ]['sk'];
                    $start      = $positions[ $i ]['pos'];
                    $next_start = ( $i + 1 < $n_pos )
                        ? $positions[ $i + 1 ]['pos']
                        : strlen( $contents );
                    $bounded    = min( $window_len, max( 0, $next_start - $start ) );
                    if ( $bounded <= 0 ) continue;

                    $window = substr( $contents, $start, $bounded );
                    if ( ! is_string( $window ) || $window === '' ) continue;
                    if ( preg_match_all(
                        '/data-wpc-cpt-field="([^"]+)"/',
                        $window, $fm
                    ) ) {
                        if ( ! isset( $cache[ $sk ] ) ) $cache[ $sk ] = array();
                        foreach ( $fm[1] as $fname_stamped ) {
                            $cache[ $sk ][ (string) $fname_stamped ] = true;
                        }
                    }
                }
            }
        }
    } catch ( \Throwable $e ) {
        // Best-effort: return whatever was collected so far.
    }
    return $cache;
}

/**
 * Ship 4c.3 — return the subset of `cfg.fields` that is safe to
 * surface in the WP editor / ACF field group.
 *
 * Filter rules:
 *
 *   1. Image-typed fields are ALWAYS kept, regardless of DOM stamping.
 *      Rationale: the importer uses image fields to set _thumbnail_id
 *      (visible via REST, sitemap, get_the_post_thumbnail() in theme
 *      templates) — that flow is independent of the loop-swap DOM
 *      substitution, so we'd orphan the featured-image control if we
 *      hid the image field on pages where it lacks a DOM stamp.
 *
 *   2. Non-image fields are kept ONLY when their `key` (or
 *      `remapped_to` storage key) appears in at least one
 *      `data-wpc-cpt-field` stamp anywhere in the theme.
 *
 *   3. Fail-safe: if NO fields are stamped for this section_key
 *      (legacy themes pre-dating Ship 4b's field stamping, or build
 *      pipelines where stamping failed entirely), return the full
 *      schema unchanged. An empty editor would be a worse UX than a
 *      possibly-misleading editor — we treat absence-of-data as
 *      "we don't know, show everything".
 *
 * IMPORTANT: this filter is read-only at the editor layer. The
 * underlying `cfg.fields` schema in `wp_options['wpconvert_cpts']`
 * is NOT modified, and the save_post handler still accepts POSTs
 * for any schema-defined field (so REST/CLI workflows that bypass
 * the editor — e.g. seeding bio text from a CSV — still work).
 *
 * @param array  $cfg          Activation config from wp_options
 * @param string $section_key  Manifest section_key
 * @return array  Filtered fields list in the same shape as
 *                wpconvert_cpt_get_fields_for_cpt()
 */
function wpconvert_cpt_get_editable_fields_for_cpt( $cfg, $section_key = '' ) {
    $all = wpconvert_cpt_get_fields_for_cpt( $cfg, $section_key );
    if ( empty( $all ) ) return $all;

    $stamped = wpconvert_cpt_get_stamped_fields_for_section_key( $section_key );

    // Fail-safe: no stamps known for this section → return full schema.
    if ( empty( $stamped ) ) return $all;

    $stamped_set = array_flip( $stamped );
    $out = array();
    foreach ( $all as $f ) {
        $type = isset( $f['type'] ) ? (string) $f['type'] : '';
        if ( $type === 'image' ) {
            $out[] = $f;
            continue;
        }
        $key         = isset( $f['key'] ) ? (string) $f['key'] : '';
        $storage_key = isset( $f['remapped_to'] ) && $f['remapped_to'] !== ''
            ? (string) $f['remapped_to']
            : $key;
        if ( isset( $stamped_set[ $key ] ) || isset( $stamped_set[ $storage_key ] ) ) {
            $out[] = $f;
        }
    }
    return $out;
}

/**
 * Ship 4c.4 — true if the candidate is a social-proof / brand-story /
 * marketing-copy array that shouldn't be surfaced as an editable CPT.
 *
 * This is a PHP MIRROR of the JS detector's `isDisplayContentArray`
 * (lib/theme/cpt-candidate-detector.js). The JS detector is the
 * primary defense — new manifests don't include these candidates at
 * all. This plugin-side mirror is defense-in-depth for users who
 * installed a NEW plugin zip against an OLD theme manifest: it hides
 * the same candidates from the admin notice so they don't have to
 * reconvert the theme.
 *
 * KEEP IN SYNC with `isDisplayContentArray` in the detector. Tests
 * in ec-cpt-003 cover the parity contract.
 *
 * @param string $array_name  Source array name from the manifest
 * @param string $source_file Source file path relative to project root
 * @return bool
 */
function wpconvert_cpt_is_display_content_array( $array_name, $source_file = '' ) {
    static $strong = null, $weak = null;
    if ( $strong === null ) {
        // Ship 4c.5 — kept in sync with STRONG_DISPLAY_TOKENS in
        // cpt-candidate-detector.js. EC-CPT-003 display-parity tests
        // pin the JS↔PHP agreement at the canonical examples.
        $strong = array_flip( array(
            // Social proof (Ship 4c.4)
            'testimonial', 'testimonials',
            'review', 'reviews',
            'quote', 'quotes',
            // Brand story (Ship 4c.4)
            'milestone', 'milestones',
            'mission',
            // Marketing cards — promoted from WEAK in Ship 4c.5.
            'feature', 'features',
            'benefit', 'benefits',
            'highlight', 'highlights',
            'card', 'cards',
            // SaaS explainer patterns (Ship 4c.5).
            'usecase', 'usecases',
            'challenge', 'challenges',
            'problem', 'problems',
            'threat', 'threats',
            'principle', 'principles',
            'pillar', 'pillars',
            'bestpractice', 'bestpractices',
            'demo',
            'comparison', 'comparisons',
            'compliance',
        ) );
        $weak = array_flip( array(
            'value', 'values',
            'featured',
            'stat', 'stats',
            'vision',
            // Ship 4c.6 / B3 — layout primitives. Only the sole-token
            // form is filtered; compounds like `pricing_columns` or
            // `wizardSteps` are content-shaped and survive.
            'column', 'columns',
            'row', 'rows',
            'step', 'steps',
            'group', 'groups',
        ) );
    }

    $tokens = wpconvert_cpt_split_name_to_tokens( (string) $array_name );

    foreach ( $tokens as $t ) {
        if ( isset( $strong[ $t ] ) ) return true;
    }

    // Ship 4c.5 — adjacent-pair check. Catches camelCase compounds
    // like `useCases` (tokens use+cases → joined 'usecases' is STRONG)
    // and `bestPractices` (best+practices → 'bestpractices').
    $n = count( $tokens );
    for ( $i = 0; $i + 1 < $n; $i++ ) {
        if ( isset( $strong[ $tokens[ $i ] . $tokens[ $i + 1 ] ] ) ) return true;
    }

    if ( $n === 1 && isset( $weak[ $tokens[0] ] ) ) return true;

    if ( $n === 2 ) {
        list( $a, $b ) = $tokens;
        if ( $a === 'value' && ( $b === 'prop' || $b === 'props' ) ) return true;
        if ( $a === 'platform' && ( $b === 'stat' || $b === 'stats' ) ) return true;
    }

    // Ship 4c.5 — onboarding/ paths signal UI mock data (Step* wizards
    // in SaaS landing pages declare arrays like DEMO_ITEMS / VAULT_ITEMS
    // that look like content but exist only for the hero animation).
    // Ship 4c.6 / B1 — adds Navbar / Navigation / MainNav / TopNav / SiteNav
    // patterns so mega-menu data arrays inside Navbar.tsx (LogMeOnce's
    // howItWorks, products, resources, solutions) are filtered out.
    if ( is_string( $source_file ) && preg_match(
        '/(?:\b(?:Testimonial|Review)|[\/\\\\]onboarding|^onboarding|(?:^|[\/\\\\])(?:Navbar|Navigation|MainNav|TopNav|SiteNav|navbar|navigation)(?:[\/\\\\.]|$))/i',
        $source_file
    ) ) {
        return true;
    }

    return false;
}

/**
 * Internal: split an array name (camelCase / snake_case / SCREAMING_SNAKE
 * / kebab-case) into lowercased tokens. Used by the display-content
 * filter to do token-boundary matching (not substring) so e.g.
 * "previewer" doesn't get treated as a "review"-shaped array.
 *
 * @param string $name
 * @return string[]
 */
function wpconvert_cpt_split_name_to_tokens( $name ) {
    if ( ! is_string( $name ) || $name === '' ) return array();
    $s = preg_replace( '/([a-z\d])([A-Z])/', '$1_$2', $name );
    $s = preg_replace( '/([A-Z]+)([A-Z][a-z])/', '$1_$2', $s );
    $s = strtolower( $s );
    $parts = preg_split( '/[_\-\s]+/', $s );
    if ( ! is_array( $parts ) ) return array();
    $out = array();
    foreach ( $parts as $p ) {
        if ( $p !== '' ) $out[] = $p;
    }
    return $out;
}

/**
 * Ship 4c.7 / B8 — single source of truth for "candidates the user can
 * meaningfully activate right now". Replaces ad-hoc filter loops that
 * had been copy-pasted into the admin notice and the dashboard's
 * Pending tab. Pre-B8 those two surfaces drifted: the notice applied
 * orphan + native-blog guards that the dashboard did not, so a single
 * conversion could show 2 candidates in the notice and 4 in the
 * Pending tab — confusing to the user, and dangerous because the
 * dashboard would let them activate orphan candidates (e.g. chart
 * arrays) whose front-end loop swap can never fire.
 *
 * Returns a two-tuple:
 *   [0] $activatable — array of candidate entries ready to render
 *   [1] $hidden_orphans — candidates dropped *only* because they have
 *       no DOM stamp (return value is empty unless $return_orphans=true,
 *       which keeps callers paying for the extra allocation only when
 *       they actually intend to surface a "X hidden — see Diagnostics"
 *       footer).
 *
 * Filters applied (in order, each documented at its rule site):
 *   - already-active CPTs (defensive, also enforced at activation time)
 *   - native-blog-shape candidates (wpconvert_cpt_is_native_blog_shape)
 *   - orphan candidates (no `data-wpc-cpt-candidate` stamp in any PHP
 *     template — activating one creates posts the FE can never render)
 *   - display-content arrays (testimonials / reviews / valueProps /
 *     milestones — see wpconvert_cpt_is_display_content_array)
 *
 * Detection-time JS filters in cpt-candidate-detector.js cover most
 * of these too; this PHP-side guard exists for back-compat with manifests
 * generated by older converters (so users don't have to reconvert).
 *
 * @param bool $return_orphans  If true, also return the candidates that
 *   were dropped *only* by the orphan guard so callers can surface them
 *   to the Diagnostics tab.
 * @return array  [ $activatable, $hidden_orphans ]
 */
function wpconvert_cpt_get_activatable_pending_candidates( $return_orphans = false ) {
    $manifest = wpconvert_cpt_get_candidates_manifest();
    $active = wpconvert_cpt_get_active_cpts();
    $active_keys = is_array( $active ) ? array_keys( $active ) : array();
    $stamped = wpconvert_cpt_get_stamped_section_keys();

    $activatable = array();
    $hidden_orphans = array();
    if ( ! is_array( $manifest ) ) return array( $activatable, $hidden_orphans );

    foreach ( $manifest as $c ) {
        if ( ! is_array( $c ) ) continue;
        $sk = isset( $c['section_key'] ) ? (string) $c['section_key'] : '';
        if ( $sk === '' ) continue;
        if ( in_array( $sk, $active_keys, true ) ) continue;

        // Ship 4c.6 / A3 — WC-intent candidates are NEVER offered as
        // CPTs; the WooCommerce notice owns them (mutual exclusion).
        if ( isset( $c['intent'] ) && $c['intent'] === 'woocommerce-product' ) continue;

        // Native-blog shape: already imported via the legacy post path.
        if ( wpconvert_cpt_is_native_blog_shape( $c ) ) continue;

        $name = isset( $c['source_array'] ) ? (string) $c['source_array'] : '';
        $file = isset( $c['source_file'] )  ? (string) $c['source_file']  : '';

        // Display-content (testimonials, reviews, …) — not editable.
        if ( $name !== '' && wpconvert_cpt_is_display_content_array( $name, $file ) ) {
            continue;
        }

        // Orphan guard — no DOM stamp means the loop swap has nothing
        // to clone, so activating would create posts the front-end can
        // never render. Surface separately so a diagnostics view CAN
        // show "X candidates hidden — see Diagnostics for details".
        if ( ! isset( $stamped[ $sk ] ) ) {
            if ( $return_orphans ) $hidden_orphans[] = $c;
            continue;
        }

        $activatable[] = $c;
    }
    return array( $activatable, $hidden_orphans );
}

function wpconvert_cpt_get_stamped_section_keys() {
    static $cache = null;
    if ( $cache !== null ) return $cache;
    $cache = array();

    if ( ! function_exists( 'get_template_directory' ) ) return $cache;
    $td = (string) get_template_directory();
    if ( $td === '' || ! is_dir( $td ) ) return $cache;

    $dirs = array( $td );
    foreach ( array( 'inc', 'template-parts' ) as $sub ) {
        $candidate_dir = $td . '/' . $sub;
        if ( is_dir( $candidate_dir ) ) $dirs[] = $candidate_dir;
    }

    $size_cap = 5 * 1024 * 1024;
    try {
        foreach ( $dirs as $dir ) {
            $files = @scandir( $dir );
            if ( ! is_array( $files ) ) continue;
            foreach ( $files as $fname ) {
                if ( substr( $fname, -4 ) !== '.php' ) continue;
                $full = $dir . '/' . $fname;
                if ( ! is_file( $full ) ) continue;
                $size = @filesize( $full );
                if ( $size === false || $size > $size_cap ) continue;
                $contents = @file_get_contents( $full );
                if ( ! is_string( $contents ) ) continue;
                if ( preg_match_all(
                    '/data-wpc-cpt-candidate="([a-f0-9]{40})"/',
                    $contents, $matches
                ) ) {
                    foreach ( $matches[1] as $sk ) {
                        $cache[ $sk ] = true;
                    }
                }
            }
        }
    } catch ( \Throwable $e ) {
        // Best-effort — return whatever we've found so far.
    }
    return $cache;
}

/* ─────────────────────────────────────────────
 * 3. SLUG VALIDATION & RESERVED-NAMES GUARDS
 * ───────────────────────────────────────────── */

/**
 * Normalize a user-provided slug to WP's post-type-name character set:
 *   - lowercase
 *   - [a-z0-9_] only (other chars become `_`)
 *   - collapse runs of `_`
 *   - strip leading/trailing `_`
 *
 * IMPORTANT: This intentionally does NOT auto-truncate to 20 chars. WP's
 * post-type-name limit is 20 characters, but silently truncating a long
 * slug like `this_slug_is_way_too_long_to_be_valid` to `this_slug_is_way_too`
 * can produce mid-word collisions between two distinct user inputs. Instead,
 * `wpconvert_cpt_is_valid_slug` rejects any normalized slug >20 chars and
 * the registration loop skips it. The admin UI (Ship 3) will surface a
 * "please shorten this slug" message in that case rather than silently
 * picking a truncated value the user didn't choose.
 *
 * @param string $raw
 * @return string  may be empty if input was unusable
 */
function wpconvert_cpt_normalize_post_type_slug( $raw ) {
    if ( ! is_string( $raw ) ) {
        return '';
    }
    $s = trim( $raw );
    // Split camelCase / PascalCase / initialism boundaries BEFORE lowercasing
    // so `menuItems` → `menu_items`, `URLPath` → `url_path`, `XMLHttpRequest`
    // → `xml_http_request`. Without this, "menuItems" was producing the
    // unreadable "menuitems" slug (Issue 1, Ship 3 user feedback).
    $s = preg_replace( '/([A-Z]+)([A-Z][a-z])/', '$1_$2', $s );
    $s = preg_replace( '/([a-z\d])([A-Z])/', '$1_$2', $s );
    $s = strtolower( $s );
    $s = preg_replace( '/[^a-z0-9_]+/', '_', $s );
    $s = preg_replace( '/_+/', '_', $s );
    $s = trim( $s, '_' );
    return $s;
}

/**
 * Suggest singular + plural human-readable labels and a singular-form
 * post-type slug from a raw source-array name. The source array (e.g.
 * `menuItems`, `BLOG_POSTS`, `doctors`) is almost always already plural,
 * so we de-pluralize to get the singular form and use it for both the
 * Singular label and the slug.
 *
 * Returns: [ 'slug' => 'menu_item', 'singular' => 'Menu Item', 'plural' => 'Menu Items' ]
 *
 * @param string $source_array
 * @return array{slug:string,singular:string,plural:string}
 */
function wpconvert_cpt_suggest_labels( $source_array ) {
    if ( ! is_string( $source_array ) || $source_array === '' ) {
        return array( 'slug' => 'content_item', 'singular' => 'Content Item', 'plural' => 'Content Items' );
    }
    // Plural words/tokens from the source array, in snake_case.
    $plural_words_slug = wpconvert_cpt_normalize_post_type_slug( $source_array );
    if ( $plural_words_slug === '' ) {
        $plural_words_slug = 'content_item';
    }

    // De-pluralize ONLY the last word. English-pragmatic rules:
    //   - `*ies` (3+ chars before): drop "ies" → "y"          (categories → category)
    //   - `*sses` (4+ chars before): drop "es" → "ss"          (classes → class)
    //   - `*xes`, `*zes`, `*ches`, `*shes`: drop "es"          (boxes → box)
    //   - `*s` (and not `*ss`): drop "s"                       (items → item)
    //   - else: leave as-is                                    (data → data, fish → fish)
    $parts = explode( '_', $plural_words_slug );
    $last = array_pop( $parts );
    $singular_last = $last;
    if ( preg_match( '/^(.{3,})ies$/', $last, $m ) ) {
        $singular_last = $m[1] . 'y';
    } elseif ( preg_match( '/^(.{2,})(sses)$/', $last, $m ) ) {
        $singular_last = $m[1] . 'ss';
    } elseif ( preg_match( '/^(.+?)(xes|zes|ches|shes)$/', $last, $m ) ) {
        $singular_last = $m[1] . substr( $m[2], 0, strlen( $m[2] ) - 2 );
    } elseif ( strlen( $last ) >= 2
        && substr( $last, -1 ) === 's'
        && substr( $last, -2 ) !== 'ss'
        && substr( $last, -2 ) !== 'us' ) {
        $singular_last = substr( $last, 0, -1 );
    }
    $parts[] = $singular_last;
    $singular_slug = implode( '_', $parts );

    // Title-case for the human labels.
    $singular_label = ucwords( str_replace( '_', ' ', $singular_slug ) );
    $plural_label   = ucwords( str_replace( '_', ' ', $plural_words_slug ) );

    // If de-pluralization didn't change anything (e.g. source was already
    // singular like "service"), produce a Plural via the inverse rule:
    if ( $singular_slug === $plural_words_slug ) {
        $plural_label = wpconvert_cpt_pluralize_label( $singular_label );
    }

    return array(
        'slug'     => $singular_slug,
        'singular' => $singular_label,
        'plural'   => $plural_label,
    );
}

/**
 * Build a short, slug-safe token from a source-file path. Used to
 * disambiguate suggested slugs when the same source_array name occurs in
 * multiple files (e.g. `doctors` in About.tsx, Doctors.tsx, OurTeam.tsx,
 * HomepageDoctors.tsx). Returns the file's basename minus extension,
 * normalized to snake_case.
 *
 * Examples:
 *   src/pages/About.tsx              → "about"
 *   src/components/HomepageDoctors.tsx → "homepage_doctors"
 *   src/pages/OurTeam.tsx            → "our_team"
 *
 * @param string $source_file
 * @return string
 */
function wpconvert_cpt_file_context_token( $source_file ) {
    if ( ! is_string( $source_file ) || $source_file === '' ) return 'x';
    $base = basename( $source_file );
    $base = preg_replace( '/\.[^.]+$/', '', $base );
    $tok = wpconvert_cpt_normalize_post_type_slug( $base );
    return $tok !== '' ? $tok : 'x';
}

/**
 * Heuristic: does this candidate look like a blog post collection?
 *
 * Detection looks at ORIGINAL field keys (pre-remap) for blog hallmarks:
 * `title`, `slug`, `content`, `excerpt`, `category`, `author`,
 * `authorSlug`, `publishDate`, `date`, `readTime`, `tags`, `meta`,
 * `metaDescription`. Threshold: 4+ matches.
 *
 * This is INFORMATIONAL only — it doesn't block activation. It just
 * surfaces a hint in the admin notice so the user knows the
 * WPCONVERT_NATIVE_BLOG override exists. The blocking-import shape
 * check (`wpconvert_cpt_is_native_blog_shape`) uses REMAPPED keys and
 * a different threshold; both can be true at once for blog candidates.
 *
 * @param array $candidate
 * @return bool
 */
function wpconvert_cpt_looks_like_blog( $candidate ) {
    if ( ! is_array( $candidate ) || empty( $candidate['fields'] ) ) return false;
    $signal_keys = array(
        'title', 'slug', 'content', 'excerpt', 'category', 'categories',
        'author', 'authorslug', 'publishdate', 'date', 'readtime',
        'tags', 'meta', 'metadescription',
    );
    $found = 0;
    foreach ( $candidate['fields'] as $f ) {
        $k = isset( $f['key'] ) ? strtolower( $f['key'] ) : '';
        if ( in_array( $k, $signal_keys, true ) ) {
            $found++;
            if ( $found >= 4 ) return true;
        }
    }
    return false;
}

/**
 * Inverse of the de-pluralization rule used in suggest_labels. Used only
 * when the source array name appears to be singular.
 *
 * @param string $singular_label
 * @return string
 */
function wpconvert_cpt_pluralize_label( $singular_label ) {
    if ( ! is_string( $singular_label ) || $singular_label === '' ) return '';
    $parts = explode( ' ', $singular_label );
    $last = array_pop( $parts );
    // Words ending in -ed / -ing / -ous / -ish are participles, gerunds, or
    // adjectives — not naturally pluralizable. "Family Focused" → "Family
    // Focused" (NOT "Family Focuseds"). Caller can edit the form if they
    // really want a different plural.
    if ( preg_match( '/(ed|ing|ous|ish)$/i', $last ) ) {
        $parts[] = $last;
        return implode( ' ', $parts );
    }
    if ( preg_match( '/(.+)y$/i', $last, $m ) && ! preg_match( '/[aeiou]y$/i', $last ) ) {
        // category → categories
        $last = $m[1] . 'ies';
    } elseif ( preg_match( '/(s|x|z|ch|sh)$/i', $last ) ) {
        // box → boxes, glass → glasses
        $last = $last . 'es';
    } else {
        $last = $last . 's';
    }
    $parts[] = $last;
    return implode( ' ', $parts );
}

/**
 * @param string $slug  Already-normalized post-type slug.
 * @return bool
 */
function wpconvert_cpt_is_valid_slug( $slug ) {
    if ( ! is_string( $slug ) || $slug === '' ) {
        return false;
    }
    if ( strlen( $slug ) > 20 ) {
        return false;
    }
    if ( ! preg_match( '/^[a-z][a-z0-9_]{0,19}$/', $slug ) ) {
        return false;
    }
    return ! wpconvert_cpt_is_reserved_slug( $slug );
}

/**
 * @param string $slug
 * @return bool
 */
function wpconvert_cpt_is_reserved_slug( $slug ) {
    if ( ! is_string( $slug ) ) {
        return true;
    }
    $reserved = @unserialize( WPCONVERT_CPT_RESERVED_SLUGS );
    if ( ! is_array( $reserved ) ) {
        return false;
    }
    return in_array( $slug, $reserved, true );
}

/* ─────────────────────────────────────────────
 * 4. NATIVE-BLOG OVERRIDE  (resolves TODO[EC-CPT-002])
 * ───────────────────────────────────────────── */

/**
 * True if the candidate's field shape looks like the native WP `post`
 * shape AND the generator marked this theme as having a native blog
 * (`WPCONVERT_NATIVE_BLOG === true`). In that case the candidate must
 * not be registered as a CPT — WP's `post` type already owns this
 * content.
 *
 * Heuristic: 3+ overlapping keys from the blog-shape set.
 *
 * Detection signal (a) from Ship 1's deferred TODO:
 *   "the array shape has 3+ of the five reserved keys"
 *
 * Detection signal (b) — `WP_Query(['post_type' => 'post'])` markup
 * present in front-page.php — is implicit: the generator only sets
 * `WPCONVERT_NATIVE_BLOG === true` when it emits that markup. So
 * checking the constant is equivalent to checking the DOM signal.
 *
 * @param array $candidate  A single entry from cpt-candidates.json.
 * @return bool
 */
function wpconvert_cpt_is_native_blog_shape( $candidate ) {
    if ( ! defined( 'WPCONVERT_NATIVE_BLOG' ) || WPCONVERT_NATIVE_BLOG !== true ) {
        return false;
    }
    if ( ! is_array( $candidate ) || empty( $candidate['fields'] ) ) {
        return false;
    }
    $blog_keys = @unserialize( WPCONVERT_CPT_BLOG_KEYS );
    if ( ! is_array( $blog_keys ) ) {
        return false;
    }

    // `fields` is an array of { key, type, ... } dicts (Ship 1 schema).
    $field_keys = array();
    foreach ( $candidate['fields'] as $f ) {
        if ( is_array( $f ) && isset( $f['key'] ) && is_string( $f['key'] ) ) {
            $field_keys[] = strtolower( $f['key'] );
        }
    }
    if ( empty( $field_keys ) ) {
        return false;
    }

    $overlap = 0;
    foreach ( $blog_keys as $bk ) {
        if ( in_array( strtolower( $bk ), $field_keys, true ) ) {
            $overlap++;
            if ( $overlap >= 3 ) {
                return true;
            }
        }
    }
    return false;
}

/* ─────────────────────────────────────────────
 * 5. SUPPORTS ARRAY HELPERS
 * ───────────────────────────────────────────── */

/**
 * Resolve the WP `supports` argument for a given config. Defaults to
 * a conservative set that's safe for all content shapes; user config
 * can override or extend.
 *
 * @param array $cfg  One entry from wp_options['wpconvert_cpts'].
 * @return array
 */
function wpconvert_cpt_get_supports_array( $cfg ) {
    // EC-CPT-ORDER-001: 'page-attributes' is always included so every CPT
    // is orderable by menu_order — the loop swap renders posts ordered by
    // `menu_order ASC` (see the front-end query), and the drag-and-drop
    // reorder UI on the post-list screen persists menu_order. Without it the
    // list/loop falls back to ID order and there's no way to reorder rooms.
    $defaults = array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' );
    if ( ! is_array( $cfg ) || empty( $cfg['supports'] ) ) {
        return $defaults;
    }
    if ( ! is_array( $cfg['supports'] ) ) {
        return $defaults;
    }
    // Whitelist against WP's recognised supports values (anything else WP
    // will silently ignore, but we strip it to keep options data clean).
    $allowed = array(
        'title', 'editor', 'comments', 'revisions', 'trackbacks',
        'author', 'excerpt', 'page-attributes', 'thumbnail',
        'custom-fields', 'post-formats',
    );
    $out = array();
    foreach ( $cfg['supports'] as $s ) {
        if ( is_string( $s ) && in_array( $s, $allowed, true ) ) {
            $out[] = $s;
        }
    }
    // EC-CPT-ORDER-001: guarantee page-attributes even when cfg overrides supports.
    if ( ! in_array( 'page-attributes', $out, true ) ) {
        $out[] = 'page-attributes';
    }
    return empty( $out ) ? $defaults : array_values( array_unique( $out ) );
}

/* ─────────────────────────────────────────────
 * 6. REGISTRATION
 * ───────────────────────────────────────────── */

/**
 * Build the args array passed to `register_post_type`.
 *
 * @param string $slug  Normalised post-type slug.
 * @param array  $cfg   Config from wp_options['wpconvert_cpts'][...].
 * @return array
 */
function wpconvert_cpt_build_register_args( $slug, $cfg ) {
    $singular = isset( $cfg['singular'] ) && is_string( $cfg['singular'] ) && $cfg['singular'] !== ''
        ? $cfg['singular']
        : ucwords( str_replace( '_', ' ', $slug ) );
    $plural = isset( $cfg['plural'] ) && is_string( $cfg['plural'] ) && $cfg['plural'] !== ''
        ? $cfg['plural']
        : $singular . 's';

    $labels = array(
        'name'                  => $plural,
        'singular_name'         => $singular,
        'menu_name'             => $plural,
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New ' . $singular,
        'edit_item'             => 'Edit ' . $singular,
        'new_item'              => 'New ' . $singular,
        'view_item'             => 'View ' . $singular,
        'view_items'            => 'View ' . $plural,
        'search_items'          => 'Search ' . $plural,
        'not_found'             => 'No ' . strtolower( $plural ) . ' found',
        'not_found_in_trash'    => 'No ' . strtolower( $plural ) . ' found in trash',
        'all_items'             => 'All ' . $plural,
        'archives'              => $singular . ' Archives',
        'attributes'            => $singular . ' Attributes',
        'insert_into_item'      => 'Insert into ' . strtolower( $singular ),
        'uploaded_to_this_item' => 'Uploaded to this ' . strtolower( $singular ),
    );

    $rewrite_slug = isset( $cfg['rewrite_slug'] ) && is_string( $cfg['rewrite_slug'] ) && $cfg['rewrite_slug'] !== ''
        ? sanitize_title( $cfg['rewrite_slug'] )
        : sanitize_title( str_replace( '_', '-', $slug ) );

    $args = array(
        'labels'              => $labels,
        'public'              => ! isset( $cfg['public'] ) || (bool) $cfg['public'],
        'has_archive'         => isset( $cfg['has_archive'] ) ? (bool) $cfg['has_archive'] : true,
        'show_in_rest'        => isset( $cfg['show_in_rest'] ) ? (bool) $cfg['show_in_rest'] : true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => isset( $cfg['menu_position'] ) ? (int) $cfg['menu_position'] : 20,
        'menu_icon'           => isset( $cfg['menu_icon'] ) && is_string( $cfg['menu_icon'] )
            ? $cfg['menu_icon']
            : 'dashicons-admin-post',
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'hierarchical'        => false,
        'supports'            => wpconvert_cpt_get_supports_array( $cfg ),
        'rewrite'             => array(
            'slug'       => $rewrite_slug,
            'with_front' => false,
        ),
        'query_var'           => true,
    );
    return $args;
}

/**
 * Iterate the active-CPT config, register each post type that:
 *   - has `enabled === true`
 *   - has a valid, non-reserved slug
 *   - is not already registered by another plugin/theme
 *   - is not a native-blog shape (when WPCONVERT_NATIVE_BLOG is true)
 *
 * Hooked to `init` at priority 9 so registration completes before any
 * default-priority callbacks (e.g. menu setup) try to enumerate post
 * types.
 *
 * Idempotent: safe to call multiple times in the same request.
 */
function wpconvert_cpt_register_active_post_types() {
    if ( ! wpconvert_cpt_should_run() ) {
        return;
    }
    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( empty( $active ) || ! is_array( $active ) ) {
            return;
        }
        $manifest = wpconvert_cpt_get_candidates_manifest();
        $by_key = array();
        foreach ( $manifest as $c ) {
            if ( is_array( $c ) && isset( $c['section_key'] ) && is_string( $c['section_key'] ) ) {
                $by_key[ $c['section_key'] ] = $c;
            }
        }

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) {
                continue;
            }
            if ( empty( $cfg['enabled'] ) ) {
                continue;
            }

            // Native-blog override — skip even if the user opted in. They can
            // un-set WPCONVERT_NATIVE_BLOG in functions.php if they truly
            // want a parallel CPT.
            if ( isset( $by_key[ $section_key ] )
                && wpconvert_cpt_is_native_blog_shape( $by_key[ $section_key ] ) ) {
                continue;
            }

            $raw_slug = isset( $cfg['post_type'] ) ? $cfg['post_type'] : '';
            $slug = wpconvert_cpt_normalize_post_type_slug( $raw_slug );
            if ( ! wpconvert_cpt_is_valid_slug( $slug ) ) {
                continue;
            }

            if ( function_exists( 'post_type_exists' ) && post_type_exists( $slug ) ) {
                // A plugin / mu-plugin / theme registered this slug first.
                // We do NOT clobber.
                continue;
            }

            if ( ! function_exists( 'register_post_type' ) ) {
                return;
            }

            try {
                $args = wpconvert_cpt_build_register_args( $slug, $cfg );
                register_post_type( $slug, $args );
            } catch ( \Throwable $inner ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'wpconvert_cpt register_post_type(' . $slug . '): ' . $inner->getMessage() );
                }
                continue;
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt register_active_post_types: ' . $e->getMessage() );
        }
        return;
    }
}

/* ─────────────────────────────────────────────
 * 7. ADMIN NOTICES
 * ───────────────────────────────────────────── */

/**
 * Surface a one-line notice on admin pages when this file is loaded
 * on an environment that's below the minimum PHP or WP version. The
 * registration callback is already silently skipping registration in
 * that case; this notice is purely informational so a Pro user knows
 * why CPTs aren't showing up.
 */
function wpconvert_cpt_admin_notice_version() {
    if ( ! function_exists( 'wpconvert_editor_is_pro' ) || ! wpconvert_editor_is_pro() ) {
        return;
    }
    if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    try {
        $php_ok = wpconvert_cpt_meets_php_version();
        $wp_ok = wpconvert_cpt_meets_wp_version();
        if ( $php_ok && $wp_ok ) {
            return;
        }
        $msg = '<strong>WPConvert CPT:</strong> requires PHP &ge; '
            . esc_html( WPCONVERT_CPT_MIN_PHP )
            . ' and WordPress &ge; '
            . esc_html( WPCONVERT_CPT_MIN_WP )
            . '. Custom post types from this theme will not be registered until your environment is updated.';
        echo '<div class="notice notice-warning"><p>' . $msg . '</p></div>';
    } catch ( \Throwable $e ) {
        // Notices are best-effort — swallow.
        return;
    }
}

/* ─────────────────────────────────────────────
 * 8. FIELD-VALUE SANITIZATION  (Ship 3)
 * ───────────────────────────────────────────── */

/**
 * Allowed URL schemes when a field type is `url`. Anything else is dropped.
 */
if ( ! defined( 'WPCONVERT_CPT_URL_SCHEMES' ) ) {
    define( 'WPCONVERT_CPT_URL_SCHEMES', serialize( array( 'http', 'https', 'mailto', 'tel' ) ) );
}

/**
 * Sanitize one field value per its detected type. Each branch is defensive:
 * a value that can't be coerced to its type becomes `null` (so the importer
 * stores nothing rather than garbage). Never throws.
 *
 * @param mixed       $value
 * @param string      $type  One of: image, url, date, number, text-short,
 *                           text-long, select, unknown.
 * @param array|null  $enum  Allowed values for `select`.
 * @return mixed
 */
function wpconvert_cpt_sanitize_field_value( $value, $type, $enum = null ) {
    try {
        if ( $value === null || $value === '' ) {
            return '';
        }
        switch ( $type ) {
            case 'image':
                // Image values are RESOLVED to attachment IDs by
                // wpconvert_cpt_resolve_image_to_attachment_id before storage.
                // Here we just trim the raw token.
                return is_string( $value ) ? sanitize_text_field( $value ) : '';

            case 'url':
                if ( ! is_string( $value ) ) return '';
                $schemes = @unserialize( WPCONVERT_CPT_URL_SCHEMES );
                $parts = parse_url( $value );
                if ( ! is_array( $parts ) || empty( $parts['scheme'] ) ) {
                    return '';
                }
                if ( ! in_array( strtolower( $parts['scheme'] ), $schemes, true ) ) {
                    return '';
                }
                return function_exists( 'esc_url_raw' )
                    ? esc_url_raw( $value, $schemes )
                    : $value;

            case 'date':
                if ( ! is_string( $value ) ) return '';
                // Accept ISO-8601 dates (YYYY-MM-DD) and RFC-3339 datetimes.
                // PHP's createFromFormat is LENIENT by default — it'll happily
                // roll forward an invalid date like "2026-13-45" into a real
                // one. We reject those by requiring a round-trip match: the
                // formatted output must equal the input head.
                $head = substr( $value, 0, 10 );
                if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $head ) ) return '';
                $dt = \DateTime::createFromFormat( '!Y-m-d', $head );
                if ( ! $dt || $dt->format( 'Y-m-d' ) !== $head ) return '';
                return $dt->format( 'Y-m-d' );

            case 'number':
                if ( is_numeric( $value ) ) {
                    $n = (float) $value;
                    return is_finite( $n ) ? $n : '';
                }
                return '';

            case 'text-short':
                if ( ! is_string( $value ) ) {
                    $value = is_scalar( $value ) ? (string) $value : '';
                }
                return function_exists( 'sanitize_text_field' )
                    ? sanitize_text_field( $value )
                    : strip_tags( $value );

            case 'text-long':
                if ( ! is_string( $value ) ) {
                    $value = is_scalar( $value ) ? (string) $value : '';
                }
                // wp_kses_post strips <script>, <iframe>, on*= handlers, etc.
                return function_exists( 'wp_kses_post' )
                    ? wp_kses_post( $value )
                    : strip_tags( $value, '<p><a><strong><em><br><ul><ol><li><h1><h2><h3><h4><blockquote><img>' );

            case 'select':
                if ( ! is_string( $value ) ) return '';
                if ( ! is_array( $enum ) || empty( $enum ) ) {
                    return function_exists( 'sanitize_text_field' )
                        ? sanitize_text_field( $value )
                        : strip_tags( $value );
                }
                // Strict whitelist by enum.
                return in_array( $value, $enum, true ) ? $value : '';

            case 'unknown':
            default:
                if ( ! is_scalar( $value ) ) return '';
                return function_exists( 'sanitize_text_field' )
                    ? sanitize_text_field( (string) $value )
                    : strip_tags( (string) $value );
        }
    } catch ( \Throwable $e ) {
        return '';
    }
}

/**
 * Build the wp_postmeta key for a (possibly long) field key. WP allows
 * up to 255 chars but performance is sensitive to long keys. We cap at
 * `_wpc_field_` + 32 chars of safe identifier. Anything longer gets
 * truncated + 6-char hash suffix so two distinct keys can't collide.
 *
 * @param string $field_key  Already-remapped key.
 * @return string
 */
function wpconvert_cpt_meta_key_for_field( $field_key ) {
    if ( ! is_string( $field_key ) || $field_key === '' ) {
        return '_wpc_field_unknown';
    }
    $safe = strtolower( preg_replace( '/[^a-zA-Z0-9_]+/', '_', $field_key ) );
    $safe = preg_replace( '/_+/', '_', $safe );
    $safe = trim( $safe, '_' );
    if ( $safe === '' ) {
        return '_wpc_field_unknown';
    }
    if ( strlen( $safe ) <= 32 ) {
        return '_wpc_field_' . $safe;
    }
    $hash = substr( sha1( $field_key ), 0, 6 );
    return '_wpc_field_' . substr( $safe, 0, 24 ) . '_' . $hash;
}

/* ─────────────────────────────────────────────
 * 8.5  ACF INTEROPERABILITY  (Ship 4c.1)
 *
 * When the user has ACF (free or Pro) installed and opts in per-CPT via
 * the activation modal, WPConvert:
 *   1. Generates an ACF field group (DB-stored, editable in the ACF UI)
 *      with one field per wpconvert field, namespaced by post_type so
 *      multiple CPTs can coexist.
 *   2. Sets `cfg.acf_managed = true` in wp_options so other code paths
 *      (meta box, loop swap) know to defer to ACF.
 *   3. Suppresses our auto meta box for that CPT (defensive: if ACF is
 *      ever deactivated, our box reappears so editing still works).
 *   4. The loop swap reads field values via `get_field()` when ACF is
 *      loaded, falling back to `_wpc_field_*` postmeta otherwise.
 *
 * Failure modes covered:
 *   - ACF deactivated after opt-in → meta box reappears + loop swap falls
 *     back. No data loss; if the user had ACF values, they remain in the
 *     ACF-shape meta keys for re-activation.
 *   - User re-runs activation → idempotent: existing group is detected
 *     by deterministic key and left alone.
 *   - User passes acf_managed=1 without ACF loaded → flag is rejected
 *     silently; cfg.acf_managed stays false.
 * ───────────────────────────────────────────── */

/**
 * True when ACF (free or Pro) is loaded in the current request. Used to
 * gate every ACF-specific code path so the plugin gracefully degrades.
 */
function wpconvert_cpt_acf_available() {
    return function_exists( 'acf' ) || function_exists( 'get_field' );
}

/**
 * Map a wpconvert field type to its closest ACF equivalent. Unknown types
 * default to `text` (safe — every wpconvert text-shaped value renders OK
 * in an ACF text field).
 *
 * @param string $type
 * @return string
 */
function wpconvert_cpt_field_to_acf_type( $type ) {
    switch ( (string) $type ) {
        case 'text-short': return 'text';
        case 'text-long':  return 'textarea';
        case 'image':      return 'image';
        case 'number':     return 'number';
        case 'select':     return 'select';
        case 'boolean':    return 'true_false';
        case 'date':       return 'date_picker';
        case 'url':        return 'url';
        default:           return 'text';
    }
}

/**
 * Stable key namespacing helpers — IMPORTANT: same post_type ALWAYS yields
 * the same keys. This guarantees idempotency: a second activation finds
 * the existing group by key and no-ops instead of creating a duplicate.
 */
function wpconvert_cpt_acf_group_key_for_post_type( $post_type ) {
    return 'group_wpconvert_' . preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $post_type ) );
}
function wpconvert_cpt_acf_field_key( $post_type, $field_key ) {
    $pt = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $post_type ) );
    $fk = preg_replace( '/[^a-z0-9_]/', '_', strtolower( (string) $field_key ) );
    $fk = preg_replace( '/_+/', '_', $fk );
    $fk = trim( $fk, '_' );
    return 'field_wpconvert_' . $pt . '_' . $fk;
}

/**
 * Build the ACF field-group array for a wpconvert CPT cfg. Pure — no
 * I/O, suitable for unit tests.
 *
 * Schema mirrors the structure that the ACF UI exports via its
 * "Tools → Export Field Groups → PHP" flow.
 *
 * @param array $cfg
 * @param string $section_key
 * @return array
 */
function wpconvert_cpt_make_acf_field_group( $cfg, $section_key ) {
    $post_type = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
    $singular  = isset( $cfg['singular'] ) ? (string) $cfg['singular'] : ucwords( str_replace( '_', ' ', $post_type ) );
    return array(
        'key'                   => wpconvert_cpt_acf_group_key_for_post_type( $post_type ),
        'title'                 => $singular . ' Fields',
        'fields'                => array(),
        'location'              => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => $post_type,
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => array(),
        'active'                => true,
        'description'           => 'Auto-generated by WPConvert for the ' . $post_type . ' post type. Safe to extend or rearrange.',
    );
}

/**
 * Build the ACF field array (one entry per wpconvert field) for a cfg.
 * Pure — no I/O.
 *
 * @param array $cfg
 * @param string $section_key
 * @return array[]
 */
function wpconvert_cpt_make_acf_fields( $cfg, $section_key ) {
    if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) return array();
    $post_type  = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
    $group_key  = wpconvert_cpt_acf_group_key_for_post_type( $post_type );

    // Ship 4c.3 — mirror the WP editor: only generate ACF fields for
    // keys that actually substitute on the front-end (plus image fields,
    // which drive the featured-image flow). Filtering here keeps the
    // ACF group and the built-in meta box in sync — a CPT switched
    // from one editor to the other shows the same set of fields,
    // matching what the loop swap can render.
    if ( function_exists( 'wpconvert_cpt_get_editable_fields_for_cpt' ) ) {
        $editable = wpconvert_cpt_get_editable_fields_for_cpt( $cfg, (string) $section_key );
        // Rebuild a cfg shape limited to the editable fields, falling
        // back to the original cfg if filtering returned nothing (the
        // helper's own fail-safe makes that "no stamps known" already).
        if ( ! empty( $editable ) ) {
            $cfg = array_merge( $cfg, array( 'fields' => $editable ) );
        }
    }

    $out = array();
    $menu_order = 0;
    foreach ( $cfg['fields'] as $f ) {
        if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
        $field_key = (string) $f['key'];
        // Prefer the remapped (storage) key for the ACF field name so it
        // lines up with what the loop swap looks for at runtime.
        $name = isset( $f['remapped_to'] ) && $f['remapped_to'] !== ''
            ? (string) $f['remapped_to']
            : $field_key;
        $type = wpconvert_cpt_field_to_acf_type( $f['type'] ?? 'text-short' );

        $entry = array(
            'key'               => wpconvert_cpt_acf_field_key( $post_type, $name ),
            'label'             => ucwords( str_replace( array( '_', '-' ), ' ', $name ) ),
            'name'              => $name,
            'type'              => $type,
            'parent'            => $group_key,
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper'           => array( 'width' => '', 'class' => '', 'id' => '' ),
            'menu_order'        => $menu_order++,
        );

        if ( $type === 'select' ) {
            $choices = array();
            if ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) {
                foreach ( $f['enum'] as $val ) {
                    $key = (string) $val;
                    $choices[ $key ] = $key;
                }
            }
            $entry['choices']       = $choices;
            $entry['default_value'] = '';
            $entry['allow_null']    = 1;
            $entry['multiple']      = 0;
            $entry['ui']            = 0;
            $entry['return_format'] = 'value';
        } elseif ( $type === 'image' ) {
            // Match the wpconvert loop swap's image-substitution semantics:
            // it expects an attachment ID and resolves via wp_get_attachment_url.
            $entry['return_format'] = 'id';
            $entry['preview_size']  = 'medium';
            $entry['library']       = 'all';
        } elseif ( $type === 'number' ) {
            $entry['min'] = '';
            $entry['max'] = '';
            $entry['step'] = '';
        } elseif ( $type === 'textarea' ) {
            $entry['rows'] = 4;
            $entry['new_lines'] = '';
        }

        $out[] = $entry;
    }
    return $out;
}

/**
 * Create (or no-op if already exists) an ACF field group + its fields for
 * this CPT. Idempotent: identifies an existing group by deterministic key
 * via `acf_get_field_group()` and skips creation if one is found.
 *
 * @param array  $cfg
 * @param string $section_key
 * @return bool  true on success (created OR already existed), false on hard failure
 */
function wpconvert_cpt_create_acf_group_for_cpt( $cfg, $section_key ) {
    if ( ! wpconvert_cpt_acf_available() ) return false;
    if ( ! function_exists( 'acf_update_field_group' ) ) return false;
    if ( ! function_exists( 'acf_update_field' ) ) return false;

    $post_type = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
    if ( $post_type === '' ) return false;

    $group_key = wpconvert_cpt_acf_group_key_for_post_type( $post_type );

    // Idempotency check: if a group with this exact key already exists,
    // treat that as success and don't overwrite (preserves any edits the
    // user made via the ACF UI). Note: this returns true even if the
    // existing group has 0 fields — that's intentional, the heal hook
    // (wpconvert_cpt_heal_acf_managed_cpts) will populate them.
    if ( function_exists( 'acf_get_field_group' ) ) {
        $existing = acf_get_field_group( $group_key );
        if ( is_array( $existing ) && ! empty( $existing ) ) {
            return true;
        }
    }

    $group = wpconvert_cpt_make_acf_field_group( $cfg, $section_key );
    $fields = wpconvert_cpt_make_acf_fields( $cfg, $section_key );

    try {
        // CRITICAL: acf_update_field_group() persists the group as an
        // `acf-field-group` post and returns the group array with the
        // assigned post ID. We MUST use that integer ID as each field's
        // `parent`, NOT the string key. Passing a string key here would
        // make ACF try to resolve the parent via get_posts() on the
        // same request — which can fail silently if WP's object cache
        // hasn't indexed the just-saved post yet, leaving the fields
        // as orphans (parent=0) and invisible in the ACF UI. (Observed
        // in Ship 4c.1 hotfix #1: group created, 0 fields linked.)
        $saved_group = acf_update_field_group( $group );
        $group_id = ( is_array( $saved_group ) && ! empty( $saved_group['ID'] ) )
            ? (int) $saved_group['ID']
            : 0;

        foreach ( $fields as $field ) {
            if ( $group_id > 0 ) {
                $field['parent'] = $group_id;
            }
            acf_update_field( $field );
        }
        return true;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_create_acf_group_for_cpt: ' . $e->getMessage() );
        }
        return false;
    }
}

/**
 * Self-heal hook for Ship 4c.1: when an ACF-managed CPT has its group
 * created but 0 fields linked (the pre-hotfix-#1 broken state, or any
 * future state where field creation partially failed), repopulate the
 * fields. Idempotent — only runs when the group has 0 fields, so it
 * NEVER overwrites user-customized field groups.
 *
 * Bound to `admin_init` so the user only needs to hit any wp-admin page
 * once for the heal to fire. Runs at most once per request via a static
 * guard so repeat admin_init invocations don't re-scan.
 */
function wpconvert_cpt_heal_acf_managed_cpts() {
    static $ran = false;
    if ( $ran ) return; $ran = true;

    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! wpconvert_cpt_acf_available() ) return;
    if ( ! function_exists( 'acf_get_field_group' ) ) return;
    if ( ! function_exists( 'acf_get_fields' ) ) return;
    if ( ! function_exists( 'acf_update_field' ) ) return;

    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( empty( $cfg['acf_managed'] ) ) continue;
            if ( empty( $cfg['post_type'] ) ) continue;

            $group_key = wpconvert_cpt_acf_group_key_for_post_type( $cfg['post_type'] );
            $group = acf_get_field_group( $group_key );
            if ( ! is_array( $group ) || empty( $group['ID'] ) ) continue;

            $existing_fields = acf_get_fields( $group );
            if ( ! empty( $existing_fields ) ) {
                // Group already has fields — never touch (preserves user edits).
                continue;
            }

            // Group exists but has 0 fields linked. Repopulate using the
            // group's actual post ID — guaranteed correct since the group
            // is already saved + indexed.
            $fields = wpconvert_cpt_make_acf_fields( $cfg, (string) $section_key );
            if ( empty( $fields ) ) continue;

            $group_id = (int) $group['ID'];
            foreach ( $fields as $field ) {
                $field['parent'] = $group_id;
                acf_update_field( $field );
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_heal_acf_managed_cpts: ' . $e->getMessage() );
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'admin_init', 'wpconvert_cpt_heal_acf_managed_cpts' );
}

/**
 * Resolve the ACF "field name" (the meta key ACF reads/writes for a
 * given wpconvert field). Uses the field's `remapped_to` from the cfg
 * if present (matches what `wpconvert_cpt_make_acf_fields()` emits as
 * the field's `name`), else falls back to the bare key.
 *
 * @param string $field_key
 * @param array  $cfg
 * @return string
 */
function wpconvert_cpt_acf_field_name_for_key( $field_key, $cfg ) {
    if ( ! empty( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) {
        foreach ( $cfg['fields'] as $f ) {
            if ( is_array( $f ) && ( $f['key'] ?? '' ) === $field_key ) {
                $name = $f['remapped_to'] ?? $field_key;
                return $name !== '' ? (string) $name : (string) $field_key;
            }
        }
    }
    return (string) $field_key;
}

/**
 * Resolve the `type` of a field from the cfg. Default is `text-short`
 * (matches the importer's fallback).
 */
function wpconvert_cpt_field_type_for_key( $field_key, $cfg ) {
    if ( ! empty( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) {
        foreach ( $cfg['fields'] as $f ) {
            if ( is_array( $f ) && ( $f['key'] ?? '' ) === $field_key ) {
                return isset( $f['type'] ) ? (string) $f['type'] : 'text-short';
            }
        }
    }
    return 'text-short';
}

/**
 * Ship 4c.1 hotfix #2 — dual-write helper.
 *
 * The importer (and any code writing to a CPT post) calls this AFTER
 * writing to `_wpc_field_*`. If the cfg is ACF-managed AND ACF is
 * actually loaded, it ALSO writes the value to the ACF-shape meta
 * keys so ACF's UI + `get_field()` see the data.
 *
 * ACF storage convention (matches what the ACF UI writes):
 *   - meta_key `<name>`  → the value (or attachment ID for images)
 *   - meta_key `_<name>` → the ACF field key string (so ACF can resolve
 *                          the field definition for proper formatting)
 *
 * For image fields, the raw value (a token like "pancakeBacon" or a
 * URL) is resolved to an attachment ID via the existing image-resolver
 * before storage — ACF needs an integer ID because we declared the
 * field with `return_format: id`.
 *
 * Idempotent: writing the same value twice is a no-op (update_post_meta
 * dedupes). Safe to call even when ACF isn't available — it bails early.
 *
 * @param int    $post_id
 * @param string $field_key   the wpconvert field key (matches `_wpc_field_*` namespace)
 * @param mixed  $raw_value   the original (pre-sanitization) value (needed for image resolution)
 * @param mixed  $sanitized   the sanitized value the importer just wrote to `_wpc_field_*`
 * @param string $type        the field type (text-short, image, number, select, etc.)
 * @param array  $cfg         the CPT cfg (must have `acf_managed`, `post_type`, `fields`)
 * @return bool true if we wrote (or attempted to write), false if we bailed early
 */
function wpconvert_cpt_dual_write_acf_meta( $post_id, $field_key, $raw_value, $sanitized, $type, $cfg ) {
    if ( empty( $cfg['acf_managed'] ) ) return false;
    if ( ! wpconvert_cpt_acf_available() ) return false;
    if ( ! function_exists( 'update_post_meta' ) ) return false;
    if ( empty( $cfg['post_type'] ) ) return false;
    $post_id = (int) $post_id;
    if ( $post_id <= 0 ) return false;

    $acf_name = wpconvert_cpt_acf_field_name_for_key( $field_key, $cfg );
    if ( $acf_name === '' ) return false;
    $acf_field_key = wpconvert_cpt_acf_field_key( (string) $cfg['post_type'], $acf_name );

    // Images: resolve the raw token to an attachment ID. ACF Image fields
    // configured with `return_format: id` expect an integer.
    $store_value = $sanitized;
    if ( $type === 'image' ) {
        $att_id = function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' )
            ? wpconvert_cpt_resolve_image_to_attachment_id( $raw_value )
            : 0;
        $store_value = $att_id ? (int) $att_id : '';
    }

    update_post_meta( $post_id, $acf_name, $store_value );
    update_post_meta( $post_id, '_' . $acf_name, $acf_field_key );
    return true;
}

/**
 * Ship 4c.1 hotfix #2 — backfill heal.
 *
 * Pre-hotfix-#2 imports wrote field values ONLY to `_wpc_field_*`. When
 * the user opted into ACF mode, ACF's UI looked at the bare meta keys
 * and found nothing → blank fields, "No image selected".
 *
 * This heal:
 *   1. Iterates active ACF-managed CPTs.
 *   2. For each post of that CPT, checks each field's ACF meta key.
 *   3. If the ACF key is empty BUT `_wpc_field_*` has a value, copies
 *      the value across (images get re-resolved to an attachment ID).
 *   4. NEVER overwrites an existing non-empty ACF value — that protects
 *      anything the user has edited via the ACF UI since opt-in.
 *
 * Bound to `admin_init` so the user just needs to hit any wp-admin page
 * once. Static guard caps it at one run per request.
 */
function wpconvert_cpt_heal_acf_post_meta() {
    static $ran = false;
    if ( $ran ) return; $ran = true;

    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! wpconvert_cpt_acf_available() ) return;
    if ( ! function_exists( 'get_posts' ) ) return;
    if ( ! function_exists( 'update_post_meta' ) ) return;
    if ( ! function_exists( 'get_post_meta' ) ) return;

    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( empty( $cfg['acf_managed'] ) ) continue;
            if ( empty( $cfg['post_type'] ) ) continue;
            if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) continue;

            $posts = get_posts( array(
                'post_type'      => (string) $cfg['post_type'],
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ) );
            if ( empty( $posts ) ) continue;

            foreach ( $posts as $post_id ) {
                $post_id = (int) $post_id;
                if ( $post_id <= 0 ) continue;

                foreach ( $cfg['fields'] as $f ) {
                    if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
                    $field_key = (string) $f['key'];
                    $type      = isset( $f['type'] ) ? (string) $f['type'] : 'text-short';
                    $acf_name  = wpconvert_cpt_acf_field_name_for_key( $field_key, $cfg );
                    if ( $acf_name === '' ) continue;

                    // Skip if ACF already has a value (user may have edited).
                    $existing = get_post_meta( $post_id, $acf_name, true );
                    if ( $existing !== '' && $existing !== null && $existing !== false ) continue;

                    // Pull from the legacy `_wpc_field_*` key.
                    $legacy_key = wpconvert_cpt_meta_key_for_field( $field_key );
                    $legacy_val = get_post_meta( $post_id, $legacy_key, true );
                    $has_legacy = ( $legacy_val !== '' && $legacy_val !== null && $legacy_val !== false );

                    $write_val = null;
                    if ( $type === 'image' ) {
                        // For images, prefer resolving the legacy token (a
                        // var name like "pancakeBacon") to an attachment ID.
                        // If that fails OR the legacy meta is empty, fall
                        // back to the post's _thumbnail_id — which the
                        // importer sets to the resolved ID for the first
                        // image field, so it's a reliable last-ditch source.
                        $att_id = 0;
                        if ( $has_legacy && function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' ) ) {
                            $att_id = (int) wpconvert_cpt_resolve_image_to_attachment_id( $legacy_val );
                        }
                        if ( $att_id <= 0 ) {
                            $thumb = (int) get_post_meta( $post_id, '_thumbnail_id', true );
                            if ( $thumb > 0 ) $att_id = $thumb;
                        }
                        if ( $att_id > 0 ) $write_val = $att_id;
                    } elseif ( $has_legacy ) {
                        $write_val = $legacy_val;
                    }

                    if ( $write_val === null ) continue;

                    $acf_field_key = wpconvert_cpt_acf_field_key( (string) $cfg['post_type'], $acf_name );
                    update_post_meta( $post_id, $acf_name, $write_val );
                    update_post_meta( $post_id, '_' . $acf_name, $acf_field_key );
                }
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_heal_acf_post_meta: ' . $e->getMessage() );
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'admin_init', 'wpconvert_cpt_heal_acf_post_meta' );
}

/**
 * Heal pass — backfill `_thumbnail_id` on CPT posts that were imported
 * BEFORE the kebab-case identifier fix (Ship 4c.2 hotfix #1).
 *
 * The bug: `wpconvert_cpt_resolve_image_to_attachment_id`'s Path-1 regex
 * only accepted JS identifier characters (no hyphens). Real-world
 * themes (Smile Dental) emit imgKey="laith-yahya" — kebab-case from the
 * start. The resolver returned 0, so the importer never set the
 * featured image. The Path-1 regex is now fixed for new imports, but
 * already-imported posts still have no featured image until the user
 * either re-imports OR this heal pass runs.
 *
 * Heal contract:
 *   • Idempotent — never overwrites an existing _thumbnail_id.
 *   • Safe no-op — runs only on admin_init, only for CPTs known to the
 *     plugin (no foreign-post enumeration).
 *   • Bounded — `wpconvert_cpt_resolve_image_to_attachment_id` is the
 *     same function the importer uses, so an SSRF-flagged URL stays
 *     blocked, a missing local file returns 0, and the post stays
 *     un-thumbed (rather than getting a corrupt attachment).
 *
 * @return void
 */
function wpconvert_cpt_heal_missing_featured_images() {
    static $ran = false;
    if ( $ran ) return; $ran = true;

    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! function_exists( 'get_posts' ) ) return;
    if ( ! function_exists( 'get_post_meta' ) ) return;
    if ( ! function_exists( 'update_post_meta' ) ) return;

    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( empty( $cfg['post_type'] ) ) continue;
            if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) continue;

            // Collect image-typed field keys. There's usually exactly one
            // (e.g. "imgKey"), but we iterate to be defensive.
            $image_keys = array();
            foreach ( $cfg['fields'] as $f ) {
                if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
                if ( ( $f['type'] ?? '' ) !== 'image' ) continue;
                $image_keys[] = (string) $f['key'];
            }
            if ( empty( $image_keys ) ) continue;

            $posts = get_posts( array(
                'post_type'      => (string) $cfg['post_type'],
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ) );
            if ( empty( $posts ) ) continue;

            foreach ( $posts as $post_id ) {
                $post_id = (int) $post_id;
                if ( $post_id <= 0 ) continue;

                // Already has a thumbnail? Skip — heal is strictly
                // additive, never destructive.
                $existing_thumb = (int) get_post_meta( $post_id, '_thumbnail_id', true );
                if ( $existing_thumb > 0 ) continue;

                // Walk image fields in declared order; first resolvable
                // one wins. Try BOTH the legacy `_wpc_field_*` key and
                // the ACF-style bare key, since the post may have been
                // imported under either schema.
                foreach ( $image_keys as $img_key ) {
                    $value = '';

                    $legacy_key = wpconvert_cpt_meta_key_for_field( $img_key );
                    $legacy_val = get_post_meta( $post_id, $legacy_key, true );
                    if ( is_string( $legacy_val ) && $legacy_val !== '' ) {
                        $value = $legacy_val;
                    }

                    if ( $value === '' && ! empty( $cfg['acf_managed'] ) && wpconvert_cpt_acf_available() ) {
                        $acf_name = wpconvert_cpt_acf_field_name_for_key( $img_key, $cfg );
                        if ( $acf_name !== '' ) {
                            $acf_val = get_post_meta( $post_id, $acf_name, true );
                            // ACF stores attachment IDs for images; if the
                            // value is already an int, set the thumb
                            // directly without re-resolving.
                            if ( is_numeric( $acf_val ) && (int) $acf_val > 0 ) {
                                update_post_meta( $post_id, '_thumbnail_id', (int) $acf_val );
                                continue 2;
                            }
                            if ( is_string( $acf_val ) && $acf_val !== '' ) {
                                $value = $acf_val;
                            }
                        }
                    }

                    if ( $value === '' ) continue;

                    $att_id = (int) wpconvert_cpt_resolve_image_to_attachment_id( $value );
                    if ( $att_id > 0 ) {
                        update_post_meta( $post_id, '_thumbnail_id', $att_id );
                        continue 2;
                    }
                }
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_heal_missing_featured_images: ' . $e->getMessage() );
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'admin_init', 'wpconvert_cpt_heal_missing_featured_images' );
}

/**
 * Read a single field value for a post, preferring ACF's `get_field()`
 * when the CPT is `acf_managed` AND ACF is currently loaded; otherwise
 * fall back to our `_wpc_field_*` postmeta. The fallback also kicks in
 * when ACF returns null/empty so that legacy posts (created before the
 * opt-in) still render via our meta.
 *
 * @param object $post   WP_Post (or stdClass with ID)
 * @param string $key    field key (data-wpc-cpt-field attribute)
 * @param string $meta_key  pre-computed _wpc_field_* key
 * @param array  $cfg    CPT cfg (looked up by section_key)
 * @return mixed         string|int|null
 */
function wpconvert_cpt_read_field_value( $post, $key, $meta_key, $cfg ) {
    // EC-CPT-008 — Meta Box dual-read. `$key` is already the bare storage key
    // (the same value the importer dual-write stored under), so we read it
    // directly via rwmb_meta(). Checked first when opted in: it's the explicit
    // per-CPT choice and (with mutual exclusion) can't coexist with ACF.
    if ( ! empty( $cfg['metabox_managed'] )
        && wpconvert_cpt_metabox_available()
        && function_exists( 'rwmb_meta' ) ) {
        $val = rwmb_meta( $key, array(), $post->ID );
        if ( $val !== null && $val !== false && $val !== '' && $val !== array() ) {
            return $val;
        }
        // Meta Box returned empty — fall through so the front-end still renders.
    }
    if ( ! empty( $cfg['acf_managed'] )
        && wpconvert_cpt_acf_available()
        && function_exists( 'get_field' ) ) {
        $val = get_field( $key, $post->ID );
        if ( $val !== null && $val !== false && $val !== '' ) {
            return $val;
        }
        // ACF returned empty — fall through to legacy meta so the
        // front-end has something to render.
    }
    return get_post_meta( $post->ID, $meta_key, true );
}

/* ─────────────────────────────────────────────
 * 8.6  META BOX INTEROPERABILITY  (EC-CPT-008)
 *
 * Mirror of the ACF interop (8.5). When MB Builder is detected
 * (`defined('MBB_VER')`) and the user opts in per-CPT, WPConvert:
 *   1. Generates a DB-stored Meta Box field group editable in MB Builder's
 *      "Custom Fields" UI — created via MB Builder's OWN parser
 *      (`\MBBParser\Unparsers\MetaBox`) when present (version-safe), else a
 *      runtime-only `meta_box` post-meta fallback.
 *   2. Sets `cfg.metabox_managed = true` so other code paths (meta box, loop
 *      swap) defer to Meta Box.
 *   3. Suppresses our auto meta box for that CPT (defensive: if Meta Box is
 *      ever deactivated, our box reappears).
 *   4. Dual-writes/reads via the BARE field id (Meta Box uses the field id
 *      directly as the post-meta key — no ACF-style key/name split).
 *
 * Strict gate: bare Meta Box core (no Builder) or no Meta Box at all → the
 * interop never engages and behavior is unchanged. ACF and Meta Box are
 * mutually exclusive per CPT; ACF wins if both flags somehow arrive.
 * ───────────────────────────────────────────── */

/**
 * True when MB Builder is loaded in the current request. Gates every Meta
 * Box-specific code path so the plugin gracefully degrades. We require BOTH
 * MB Builder (`MBB_VER`) and Meta Box core (`rwmb_meta`) — the interop only
 * makes sense when the user can edit the generated group in the Builder UI.
 */
function wpconvert_cpt_metabox_available() {
    return defined( 'MBB_VER' ) && function_exists( 'rwmb_meta' );
}

/**
 * Map a wpconvert field type to its closest Meta Box equivalent. Unknown
 * types default to `text` (safe — every wpconvert text-shaped value renders
 * OK in a Meta Box text field).
 *
 * @param string $type
 * @return string
 */
function wpconvert_cpt_field_to_metabox_type( $type ) {
    switch ( (string) $type ) {
        case 'text-short': return 'text';
        case 'text-long':  return 'textarea';
        case 'image':      return 'single_image';
        case 'number':     return 'number';
        case 'select':     return 'select';
        case 'boolean':    return 'switch';
        case 'date':       return 'date';
        case 'url':        return 'url';
        default:           return 'text';
    }
}

/**
 * Stable group identifier for a post type. Same post_type ALWAYS yields the
 * same id, guaranteeing idempotency. Doubles as the meta-box post's
 * `post_name` (MB Builder skips any group post with an empty post_name).
 */
function wpconvert_cpt_metabox_group_id_for_post_type( $post_type ) {
    return 'wpconvert-mb-' . preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $post_type ) );
}

/**
 * Build the Meta Box field array (one entry per wpconvert field) for a cfg.
 * Pure — no I/O. Field `id` is the BARE storage key (= post-meta key); Meta
 * Box uses it directly, with no ACF-style `field_xxx` hashing.
 *
 * @param array  $cfg
 * @param string $section_key
 * @return array[]
 */
function wpconvert_cpt_make_metabox_fields( $cfg, $section_key ) {
    if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) return array();

    // Mirror the ACF path: only generate fields for keys that actually
    // substitute on the front-end (plus image fields). Keeps the Meta Box
    // group, the ACF group, and the built-in meta box in sync.
    if ( function_exists( 'wpconvert_cpt_get_editable_fields_for_cpt' ) ) {
        $editable = wpconvert_cpt_get_editable_fields_for_cpt( $cfg, (string) $section_key );
        if ( ! empty( $editable ) ) {
            $cfg = array_merge( $cfg, array( 'fields' => $editable ) );
        }
    }

    $out = array();
    foreach ( $cfg['fields'] as $f ) {
        if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
        $field_key = (string) $f['key'];
        // Prefer the remapped (storage) key so it lines up with what the
        // importer wrote and the loop swap reads.
        $name = isset( $f['remapped_to'] ) && $f['remapped_to'] !== ''
            ? (string) $f['remapped_to']
            : $field_key;
        $type = wpconvert_cpt_field_to_metabox_type( $f['type'] ?? 'text-short' );

        $entry = array(
            'id'   => $name, // BARE id = meta key (no hashing, no namespacing)
            'name' => ucwords( str_replace( array( '_', '-' ), ' ', $name ) ),
            'type' => $type,
        );

        if ( $type === 'select' ) {
            $options = array();
            if ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) {
                foreach ( $f['enum'] as $val ) {
                    $key = (string) $val;
                    $options[ $key ] = $key;
                }
            }
            $entry['options']     = $options;
            $entry['placeholder'] = '';
        } elseif ( $type === 'single_image' ) {
            // single_image stores a single attachment ID — matches the loop
            // swap's image-substitution semantics (resolve ID → URL).
            $entry['max_file_uploads'] = 1;
        } elseif ( $type === 'number' ) {
            $entry['step'] = 'any';
        } elseif ( $type === 'textarea' ) {
            $entry['rows'] = 4;
        }

        $out[] = $entry;
    }
    return $out;
}

/**
 * Build the Meta Box field-group array for a cfg, in the documented,
 * stable `rwmb_meta_boxes` shape (schemas.metabox.io/field-group.json).
 * Pure — no I/O.
 *
 * @param array  $cfg
 * @param string $section_key
 * @return array
 */
function wpconvert_cpt_make_metabox_group( $cfg, $section_key ) {
    $post_type = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
    $singular  = isset( $cfg['singular'] ) && $cfg['singular'] !== ''
        ? (string) $cfg['singular']
        : ucwords( str_replace( '_', ' ', $post_type ) );
    return array(
        'id'         => wpconvert_cpt_metabox_group_id_for_post_type( $post_type ),
        'title'      => $singular . ' Fields',
        'post_types' => array( $post_type ),
        'context'    => 'normal',
        'priority'   => 'high',
        'style'      => 'seamless',
        'fields'     => wpconvert_cpt_make_metabox_fields( $cfg, $section_key ),
    );
}

/**
 * Find the existing `meta-box` group post id for this post type, or 0.
 * Used for idempotency + heal. Looks up by deterministic post_name.
 *
 * @param string $post_type
 * @return int
 */
function wpconvert_cpt_find_metabox_group_post_id( $post_type ) {
    if ( ! function_exists( 'get_posts' ) ) return 0;
    $name = wpconvert_cpt_metabox_group_id_for_post_type( $post_type );
    $found = get_posts( array(
        'post_type'      => 'meta-box',
        'name'           => $name,
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'suppress_filters' => true,
    ) );
    if ( empty( $found ) || ! is_array( $found ) ) return 0;
    $id = $found[0];
    return is_object( $id ) ? (int) ( $id->ID ?? 0 ) : (int) $id;
}

/**
 * Create (or no-op if it already exists) a DB-stored Meta Box field group for
 * this CPT. Idempotent: an existing group (matched by deterministic
 * post_name) is left untouched, preserving any edits made in MB Builder.
 *
 * Storage strategy (verified against the public meta-box-builder + mbb-parser
 * source):
 *   - We always write the documented `meta_box` post meta (the
 *     `rwmb_meta_boxes` array). MB Builder's Register injects this verbatim
 *     into the filter, so runtime field registration works regardless.
 *   - When MB Builder's own parser (`\MBBParser\Unparsers\MetaBox`) is
 *     present, we additionally run the clean group through it and store the
 *     normalized `meta_box`/`settings`/`fields` working copies — the exact
 *     path MB Builder's import UI uses, making the group fully editable in the
 *     Builder UI and absorbing version differences. Wrapped in try/catch so a
 *     parser-shape surprise can never break activation (we keep the basic
 *     `meta_box` we already wrote).
 *
 * @param array  $cfg
 * @param string $section_key
 * @return bool true on success (created OR already existed), false on hard failure
 */
function wpconvert_cpt_create_metabox_group_for_cpt( $cfg, $section_key ) {
    if ( ! wpconvert_cpt_metabox_available() ) return false;
    if ( ! function_exists( 'wp_insert_post' ) ) return false;

    $post_type = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
    if ( $post_type === '' ) return false;

    // Idempotency: if a group post with this exact name already exists, treat
    // as success and don't overwrite (preserves user edits).
    if ( wpconvert_cpt_find_metabox_group_post_id( $post_type ) > 0 ) {
        return true;
    }

    $group = wpconvert_cpt_make_metabox_group( $cfg, $section_key );
    $name  = wpconvert_cpt_metabox_group_id_for_post_type( $post_type );

    try {
        $post_id = wp_insert_post( array(
            'post_type'   => 'meta-box',
            'post_status' => 'publish',
            'post_title'  => isset( $group['title'] ) ? (string) $group['title'] : ( $name ),
            // post_name is MANDATORY — MB Builder's loader skips groups with
            // an empty post_name.
            'post_name'   => $name,
        ), true );

        if ( function_exists( 'is_wp_error' ) && is_wp_error( $post_id ) ) return false;
        $post_id = (int) $post_id;
        if ( $post_id <= 0 ) return false;

        // Always write the runtime registration payload.
        if ( function_exists( 'update_post_meta' ) ) {
            update_post_meta( $post_id, 'meta_box', $group );
        }

        // Enrich for Builder-UI editing when MB Builder's parser is present.
        if ( class_exists( 'MBBParser\\Unparsers\\MetaBox' ) && function_exists( 'update_post_meta' ) ) {
            try {
                $unparser_class = 'MBBParser\\Unparsers\\MetaBox';
                $unparser = new $unparser_class( $group );
                if ( method_exists( $unparser, 'unparse' ) ) {
                    $unparser->unparse();
                }
                $full = method_exists( $unparser, 'get_settings' ) ? $unparser->get_settings() : null;
                if ( is_array( $full ) ) {
                    if ( ! empty( $full['meta_box'] ) ) update_post_meta( $post_id, 'meta_box', $full['meta_box'] );
                    if ( isset( $full['settings'] ) )   update_post_meta( $post_id, 'settings', $full['settings'] );
                    if ( isset( $full['fields'] ) )     update_post_meta( $post_id, 'fields', $full['fields'] );
                }
            } catch ( \Throwable $e ) {
                // Keep the basic meta_box we already wrote — runtime
                // registration still works even without the Builder enrichment.
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'wpconvert_cpt_create_metabox_group_for_cpt (enrich): ' . $e->getMessage() );
                }
            }
        }
        return true;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_create_metabox_group_for_cpt: ' . $e->getMessage() );
        }
        return false;
    }
}

/**
 * Heal hook — recreate a missing Meta Box group post for any active
 * `metabox_managed` CPT. Runs on admin_init. Idempotent + non-destructive:
 * an existing group (matched by post_name) is never touched, preserving user
 * customizations made in MB Builder. Mirrors wpconvert_cpt_heal_acf_managed_cpts.
 */
function wpconvert_cpt_heal_metabox_managed_cpts() {
    static $ran = false;
    if ( $ran ) return; $ran = true;

    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! wpconvert_cpt_metabox_available() ) return;

    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( empty( $cfg['metabox_managed'] ) ) continue;
            if ( empty( $cfg['post_type'] ) ) continue;

            // Existing group → leave it alone (create handles idempotency
            // too, but checking here avoids unnecessary work).
            if ( wpconvert_cpt_find_metabox_group_post_id( (string) $cfg['post_type'] ) > 0 ) {
                continue;
            }
            wpconvert_cpt_create_metabox_group_for_cpt( $cfg, (string) $section_key );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_heal_metabox_managed_cpts: ' . $e->getMessage() );
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'admin_init', 'wpconvert_cpt_heal_metabox_managed_cpts' );
}

/**
 * Dual-write at import: when the CPT is Meta-Box-managed, write each field's
 * value to the BARE Meta Box field id (= post-meta key) in addition to the
 * legacy `_wpc_field_*` key, so MB Builder's editor + rwmb_meta() see the
 * imported data on existing posts.
 *
 * Uses direct update_post_meta() (NOT rwmb_set_meta) on purpose: the importer
 * runs in the same request that creates the group post, but MB Builder only
 * registers DB groups on `init` (already fired) — so rwmb_set_meta() could
 * skip the still-unregistered field ids. Meta Box stores single-value fields
 * as plain post meta keyed by the field id, so a direct write is byte-identical
 * and timing-independent. All our types are single-value.
 *
 * @param int    $post_id
 * @param string $field_key  the BARE storage key (already remapped)
 * @param mixed  $raw_value
 * @param mixed  $sanitized
 * @param string $type
 * @param array  $cfg
 * @return bool true if we wrote, false if we bailed early
 */
function wpconvert_cpt_dual_write_metabox_meta( $post_id, $field_key, $raw_value, $sanitized, $type, $cfg ) {
    if ( empty( $cfg['metabox_managed'] ) ) return false;
    if ( ! wpconvert_cpt_metabox_available() ) return false;
    if ( ! function_exists( 'update_post_meta' ) ) return false;
    if ( empty( $cfg['post_type'] ) ) return false;
    $post_id = (int) $post_id;
    if ( $post_id <= 0 ) return false;

    $field_id = (string) $field_key;
    if ( $field_id === '' ) return false;

    // Images: resolve the raw token to an attachment ID. Meta Box's
    // single_image stores the attachment ID.
    $store_value = $sanitized;
    if ( $type === 'image' ) {
        $att_id = function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' )
            ? wpconvert_cpt_resolve_image_to_attachment_id( $raw_value )
            : 0;
        $store_value = $att_id ? (int) $att_id : '';
    }

    update_post_meta( $post_id, $field_id, $store_value );
    return true;
}

/**
 * Backfill heal — copy legacy `_wpc_field_*` values into the bare Meta Box
 * field ids on existing posts for any active `metabox_managed` CPT. Runs on
 * admin_init. Never overwrites an existing non-empty Meta Box value (protects
 * MB Builder edits). Mirrors wpconvert_cpt_heal_acf_post_meta.
 */
function wpconvert_cpt_heal_metabox_post_meta() {
    static $ran = false;
    if ( $ran ) return; $ran = true;

    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! wpconvert_cpt_metabox_available() ) return;
    if ( ! function_exists( 'get_posts' ) ) return;
    if ( ! function_exists( 'update_post_meta' ) ) return;
    if ( ! function_exists( 'get_post_meta' ) ) return;

    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( empty( $cfg['metabox_managed'] ) ) continue;
            if ( empty( $cfg['post_type'] ) ) continue;
            if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) continue;

            $posts = get_posts( array(
                'post_type'      => (string) $cfg['post_type'],
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ) );
            if ( empty( $posts ) ) continue;

            foreach ( $posts as $post_id ) {
                $post_id = (int) $post_id;
                if ( $post_id <= 0 ) continue;

                foreach ( $cfg['fields'] as $f ) {
                    if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
                    $field_key = (string) $f['key'];
                    $type      = isset( $f['type'] ) ? (string) $f['type'] : 'text-short';
                    // Bare Meta Box id = remapped storage key (same as dual-write).
                    $mb_id = wpconvert_cpt_acf_field_name_for_key( $field_key, $cfg );
                    if ( $mb_id === '' ) continue;

                    // Skip if Meta Box already has a value (user may have edited).
                    $existing = get_post_meta( $post_id, $mb_id, true );
                    if ( $existing !== '' && $existing !== null && $existing !== false ) continue;

                    $legacy_key = wpconvert_cpt_meta_key_for_field( $field_key );
                    $legacy_val = get_post_meta( $post_id, $legacy_key, true );
                    $has_legacy = ( $legacy_val !== '' && $legacy_val !== null && $legacy_val !== false );

                    $write_val = null;
                    if ( $type === 'image' ) {
                        $att_id = 0;
                        if ( $has_legacy && function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' ) ) {
                            $att_id = (int) wpconvert_cpt_resolve_image_to_attachment_id( $legacy_val );
                        }
                        if ( $att_id <= 0 ) {
                            $thumb = (int) get_post_meta( $post_id, '_thumbnail_id', true );
                            if ( $thumb > 0 ) $att_id = $thumb;
                        }
                        if ( $att_id > 0 ) $write_val = $att_id;
                    } elseif ( $has_legacy ) {
                        $write_val = $legacy_val;
                    }

                    if ( $write_val === null ) continue;
                    update_post_meta( $post_id, $mb_id, $write_val );
                }
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_heal_metabox_post_meta: ' . $e->getMessage() );
        }
    }
}

if ( function_exists( 'add_action' ) ) {
    add_action( 'admin_init', 'wpconvert_cpt_heal_metabox_post_meta' );
}

/* ─────────────────────────────────────────────
 * 9. IMAGE RESOLUTION  (Ship 3)
 * ───────────────────────────────────────────── */

/**
 * Allowed URL schemes for external image sideload. Anything else (file://,
 * ftp://, gopher://, data:, etc.) is rejected with no HTTP request made.
 */
if ( ! defined( 'WPCONVERT_CPT_IMAGE_MAX_BYTES' ) ) {
    // 10 MB cap per image. Sized to fit a high-res photo without allowing
    // megabyte-scale denial-of-service vectors.
    define( 'WPCONVERT_CPT_IMAGE_MAX_BYTES', 10 * 1024 * 1024 );
}

/**
 * True if a URL would resolve to a private / loopback / link-local /
 * carrier-grade-NAT address. SSRF guard for the sideload path.
 *
 * @param string $host
 * @return bool
 */
function wpconvert_cpt_host_is_private( $host ) {
    if ( ! is_string( $host ) || $host === '' ) return true;
    $host = strtolower( $host );

    // Explicit hostname blocks.
    if ( $host === 'localhost' || $host === '0.0.0.0' || $host === 'broadcasthost' ) {
        return true;
    }
    if ( substr( $host, -6 ) === '.local' ) {
        return true;
    }
    if ( $host === 'metadata' || $host === 'metadata.google.internal' ) {
        return true;
    }

    // If it's an IP literal, test the ranges directly. Otherwise we trust
    // wp_http_validate_url (which gethostbynames the target server-side).
    if ( filter_var( $host, FILTER_VALIDATE_IP ) === false ) {
        return false;
    }

    if ( filter_var(
        $host,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) === false ) {
        return true;
    }

    // Cloud metadata endpoints (AWS, GCP, Azure all use 169.254.169.254).
    if ( $host === '169.254.169.254' ) return true;

    return false;
}

/**
 * Validate an external image URL. Returns true only when the URL has a
 * permitted scheme, a non-private host, and a registered file extension
 * that maps to an image MIME type. Does NOT touch the network.
 *
 * @param string $url
 * @return bool
 */
function wpconvert_cpt_is_safe_image_url( $url ) {
    if ( ! is_string( $url ) || $url === '' ) return false;
    $parts = @parse_url( $url );
    if ( ! is_array( $parts ) ) return false;
    $scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : '';
    if ( $scheme !== 'http' && $scheme !== 'https' ) return false;
    if ( empty( $parts['host'] ) ) return false;
    if ( wpconvert_cpt_host_is_private( $parts['host'] ) ) return false;

    if ( function_exists( 'wp_http_validate_url' ) ) {
        if ( ! wp_http_validate_url( $url ) ) return false;
    }

    // EC-CPT-015 — extension rule: when the path's last segment HAS an
    // extension it must be an image one (rejects .exe/.php/.html). A path
    // with NO extension at all is allowed through: image CDNs serve
    // extensionless URLs (images.unsplash.com/photo-15872…?w=800) and the
    // sideloader verifies the real MIME type from the downloaded bytes
    // before anything enters the media library.
    $path = isset( $parts['path'] ) ? strtolower( $parts['path'] ) : '';
    $last_segment = $path === '' ? '' : basename( $path );
    if ( strpos( $last_segment, '.' ) !== false
        && ! preg_match( '/\.(jpe?g|png|gif|webp|svg|avif)$/i', $path ) ) {
        return false;
    }
    return true;
}

/**
 * Resolve a manifest `image` value to a WordPress attachment ID.
 *
 * Resolution order (each step is short-circuited as soon as it succeeds):
 *
 *   1. Bare identifier (e.g. `pancakeClassic`)
 *      → search `theme/assets/images/` for a file whose basename starts
 *        with kebab-case-of-identifier. If found, attach pointing to the
 *        existing on-disk file (no copy, no media library bloat).
 *
 *   2. Relative path starting with `/assets/` or `/wp-content/themes/`
 *      → resolve to the theme directory + look up an existing attachment.
 *
 *   3. Absolute URL (http/https only)
 *      → validate via `wpconvert_cpt_is_safe_image_url`, then sideload
 *        through `media_sideload_image` (which gives WP all its own
 *        size + MIME validation).
 *
 * Any failure returns 0 — the importer treats 0 as "no featured image"
 * and creates the post without one.
 *
 * @param string $value  The raw image value from the manifest.
 * @return int  Attachment ID, or 0 on failure.
 */
function wpconvert_cpt_resolve_image_to_attachment_id( $value ) {
    if ( ! is_string( $value ) || $value === '' ) return 0;
    try {
        /*
         * ── Path 0: heal raw-PHP image values ──────────────────────────
         * EC-CPT-IMG-001: manifests produced before the converter fix stored
         * image fields as a raw PHP echo of get_template_directory_uri()
         * followed by "/assets/images/x.jpg". Raw PHP can't be resolved (none
         * of the paths below match it), so imported posts had no featured
         * image until a manual re-save. Strip any embedded PHP block, leaving
         * the theme-relative path the helper would have produced, so existing
         * sites self-heal without a reconvert.
         */
        if ( strpos( $value, '<?' ) !== false ) {
            $value = trim( preg_replace( '/<\?(?:php|=)?.*?\?>/s', '', $value ) );
            if ( $value === '' ) return 0;
        }

        // ── Path 1: bare identifier (camelCase, snake_case, OR kebab-case)
        //
        // Ship 4c.2 hotfix #1 — extended the regex to accept hyphens.
        // Real-world source arrays emit identifiers in any of these shapes:
        //   • camelCase  ("laithYahya")  ← Pancake (Pixie)
        //   • kebab-case ("laith-yahya") ← Smile Dental (May 2026)
        //   • snake_case ("laith_yahya") ← rare but valid
        // The downstream `wpconvert_cpt_resolve_identifier_to_attachment_id`
        // kebab-cases the input before scanning theme/assets/images, so
        // already-kebab inputs flow through unchanged and match correctly.
        if ( preg_match( '/^[A-Za-z_$][A-Za-z0-9_$\-]*$/', $value ) ) {
            return wpconvert_cpt_resolve_identifier_to_attachment_id( $value );
        }

        // ── Path 2: theme-relative path ────────────────────────────────
        if ( strpos( $value, '/' ) === 0 && function_exists( 'get_template_directory' ) ) {
            return wpconvert_cpt_resolve_local_path_to_attachment_id( $value );
        }
        if ( strpos( $value, 'assets/' ) === 0 ) {
            return wpconvert_cpt_resolve_local_path_to_attachment_id( '/' . $value );
        }

        // ── Path 3: absolute URL with sideload ─────────────────────────
        if ( wpconvert_cpt_is_safe_image_url( $value ) ) {
            return wpconvert_cpt_sideload_external_image( $value );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt resolve_image: ' . $e->getMessage() );
        }
    }
    return 0;
}

/**
 * Look up a bare JS identifier (e.g. `pancakeClassic`) in the theme's
 * `assets/images/` directory and attach to the on-disk file.
 *
 * Strategy: kebab-case the identifier (`pancakeClassic` → `pancake-classic`)
 * and pick the first file whose basename starts with that token. Real
 * Vite output names files like `pancake-classic-gy7DF7aO.jpg` (asset hash
 * suffix), so a `startsWith` match is the right heuristic.
 *
 * @param string $identifier
 * @return int  Attachment ID or 0.
 */
function wpconvert_cpt_resolve_identifier_to_attachment_id( $identifier ) {
    if ( ! function_exists( 'get_template_directory' ) ) return 0;
    if ( ! function_exists( 'wp_insert_attachment' ) ) return 0;

    $kebab = preg_replace_callback(
        '/[A-Z]/',
        function ( $m ) { return '-' . strtolower( $m[0] ); },
        $identifier
    );
    $kebab = ltrim( $kebab, '-' );

    $dir = get_template_directory() . '/assets/images';
    if ( ! is_dir( $dir ) ) return 0;

    $files = @scandir( $dir );
    if ( ! is_array( $files ) ) return 0;
    foreach ( $files as $fname ) {
        if ( strpos( $fname, '.' ) === 0 ) continue;
        // Skip the public- prefix variants emitted by some build pipelines.
        if ( strpos( $fname, 'public-' ) === 0 ) continue;
        // Match on identifier-name-prefix.
        $stem = preg_replace( '/(-[A-Za-z0-9_-]{6,12})?\.[a-z]+$/i', '', $fname );
        if ( $stem !== $kebab ) continue;
        return wpconvert_cpt_attach_existing_file( $dir . '/' . $fname );
    }
    return 0;
}

/**
 * Resolve a path like `/assets/images/foo-abc123.jpg` to an attachment
 * pointing at the on-disk theme file. Idempotent — re-importing reuses
 * any attachment we previously created for the same path.
 *
 * @param string $rel_path
 * @return int  Attachment ID or 0.
 */
function wpconvert_cpt_resolve_local_path_to_attachment_id( $rel_path ) {
    if ( ! function_exists( 'get_template_directory' ) ) return 0;
    $rel_path = '/' . ltrim( $rel_path, '/' );
    // Only accept theme-internal /assets/* paths. Anything else is treated
    // as a regular URL by the caller.
    if ( strpos( $rel_path, '/assets/' ) !== 0 ) return 0;
    $full = get_template_directory() . $rel_path;
    if ( ! file_exists( $full ) ) {
        // EC-CPT-015 — Vite fingerprinting: the build renames
        // /assets/images/iphone-15-pro-max.jpg to
        // iphone-15-pro-max-DyltOSRb.jpg, but source arrays keep the
        // un-hashed path. Scan the target directory for the
        // "<stem>-<hash>.<ext>" variant before giving up (neon-elite
        // regression: every imported product lost its featured image).
        $full = wpconvert_cpt_find_hashed_theme_file( $full );
        if ( $full === '' ) return 0;
    }
    return wpconvert_cpt_attach_existing_file( $full );
}

/**
 * EC-CPT-015 — given an absolute theme-file path that does NOT exist,
 * look for a Vite-fingerprinted sibling: same stem and extension with a
 * 6–12 char base64ish hash suffix (`foo.jpg` → `foo-DyltOSRb.jpg`).
 *
 * @param string $absolute_path
 * @return string  The on-disk path, or '' when no variant matches.
 */
function wpconvert_cpt_find_hashed_theme_file( $absolute_path ) {
    $dir = dirname( (string) $absolute_path );
    if ( $dir === '' || ! is_dir( $dir ) ) return '';
    $base = basename( (string) $absolute_path );
    $dot  = strrpos( $base, '.' );
    if ( $dot === false || $dot === 0 ) return '';
    $stem = substr( $base, 0, $dot );
    $ext  = substr( $base, $dot + 1 );
    if ( $stem === '' || $ext === '' ) return '';
    $files = @scandir( $dir );
    if ( ! is_array( $files ) ) return '';
    $re = '/^' . preg_quote( $stem, '/' ) . '-[A-Za-z0-9_-]{6,12}\.' . preg_quote( $ext, '/' ) . '$/i';
    foreach ( $files as $fname ) {
        if ( strpos( $fname, '.' ) === 0 ) continue;
        if ( ! preg_match( $re, $fname ) ) continue;
        return $dir . '/' . $fname;
    }
    return '';
}

/**
 * Create (or reuse) a wp_posts attachment row that points at an existing
 * file inside the theme directory. We do NOT copy the file into uploads
 * — that would duplicate every image (10x larger media library on a
 * busy site).
 *
 * @param string $absolute_path
 * @return int  Attachment ID or 0.
 */
function wpconvert_cpt_attach_existing_file( $absolute_path ) {
    if ( ! is_string( $absolute_path ) || ! file_exists( $absolute_path ) ) return 0;

    // Check if we already attached this file in a previous import.
    if ( function_exists( 'get_posts' ) ) {
        $existing = get_posts( array(
            'post_type'      => 'attachment',
            'posts_per_page' => 1,
            'meta_key'       => '_wpc_attachment_path',
            'meta_value'     => $absolute_path,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'suppress_filters' => false,
        ) );
        if ( is_array( $existing ) && ! empty( $existing ) ) {
            return (int) $existing[0];
        }
    }

    if ( ! function_exists( 'wp_insert_attachment' ) ) return 0;

    $filetype = function_exists( 'wp_check_filetype' )
        ? wp_check_filetype( basename( $absolute_path ) )
        : array( 'type' => 'image/jpeg' );
    if ( empty( $filetype['type'] ) || strpos( $filetype['type'], 'image/' ) !== 0 ) {
        return 0;
    }

    $upload_url = function_exists( 'content_url' )
        ? str_replace(
            function_exists( 'get_template_directory' ) ? get_template_directory() : '',
            function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '',
            $absolute_path
        )
        : $absolute_path;

    $attachment = array(
        'guid'           => $upload_url,
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $absolute_path ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment( $attachment, $absolute_path );
    if ( is_wp_error( $attach_id ) || ! $attach_id ) return 0;

    if ( function_exists( 'update_post_meta' ) ) {
        update_post_meta( $attach_id, '_wpc_attachment_path', $absolute_path );
    }

    // Generate metadata (image sizes, thumbnails). This is what makes the
    // image visible in the media library.
    if ( function_exists( 'wp_generate_attachment_metadata' ) && function_exists( 'wp_update_attachment_metadata' ) ) {
        $meta = wp_generate_attachment_metadata( $attach_id, $absolute_path );
        if ( is_array( $meta ) ) {
            wp_update_attachment_metadata( $attach_id, $meta );
        }
    }

    return (int) $attach_id;
}

/**
 * Resolve an attachment ID to a usable URL, with two safety nets WP's
 * stock `wp_get_attachment_url()` doesn't provide:
 *
 *   1. THEME-ATTACHED files first. `wpconvert_cpt_attach_existing_file()`
 *      creates attachments whose underlying file lives in the theme
 *      directory (NOT `/wp-content/uploads/`). For those, WP's stock
 *      resolver produces a malformed URL like
 *      `…/wp-content/uploads//wp-content/themes/foo/assets/img.jpg`
 *      because it treats `_wp_attached_file` as relative to the uploads
 *      baseurl when the path isn't inside the uploads dir. We sidestep
 *      that by re-mapping the absolute theme path → theme URI via the
 *      `_wpc_attachment_path` meta we wrote at import time.
 *
 *   2. SANITISE WP's URL — even for uploads-resident attachments, we
 *      reject the `…/uploads//…` shape (a known mis-config) and return
 *      empty so the caller falls back to its template src.
 *
 * Ship 4c.1 hotfix #4 — necessary for any environment where the theme
 * lives outside the canonical uploads tree (Playground, multisite with
 * symlinked theme dirs, custom WP_CONTENT_DIR setups).
 *
 * @param int $att_id
 * @return string  URL or empty string.
 */
function wpconvert_cpt_get_attachment_url_safe( $att_id ) {
    $att_id = (int) $att_id;
    if ( $att_id <= 0 ) return '';

    // ── 1. Prefer our own theme-attached path mapping ──────────────────
    if ( function_exists( 'get_post_meta' )
        && function_exists( 'get_template_directory' )
        && function_exists( 'get_template_directory_uri' ) ) {
        $path = get_post_meta( $att_id, '_wpc_attachment_path', true );
        if ( is_string( $path ) && $path !== '' ) {
            $td = (string) get_template_directory();
            $tu = (string) get_template_directory_uri();
            if ( $td !== '' && $tu !== '' && strpos( $path, $td ) === 0 ) {
                return $tu . substr( $path, strlen( $td ) );
            }
        }
    }

    // ── 2. Fall back to WP's resolver (with sanity check) ──────────────
    if ( function_exists( 'wp_get_attachment_url' ) ) {
        $u = wp_get_attachment_url( $att_id );
        if ( is_string( $u ) && $u !== '' ) {
            // Reject the known malformed shape produced when
            // _wp_attached_file is an absolute path NOT inside uploads.
            if ( strpos( $u, '/uploads//' ) === false ) {
                return $u;
            }
        }
    }
    return '';
}

/**
 * EC-CPT-020 — theme URI for one of OUR theme-bundled attachments
 * (created by wpconvert_cpt_attach_existing_file: the file lives in the
 * theme dir, NOT /wp-content/uploads/). Returns '' for anything else.
 *
 * Computed ONLY from the `_wpc_attachment_path` meta — it MUST NOT call
 * wp_get_attachment_url(), because the URL filter below runs inside that
 * function and would recurse.
 *
 * @param int $att_id
 * @return string
 */
function wpconvert_cpt_theme_attachment_uri( $att_id ) {
    $att_id = (int) $att_id;
    if ( $att_id <= 0 ) return '';
    if ( ! function_exists( 'get_post_meta' )
        || ! function_exists( 'get_template_directory' )
        || ! function_exists( 'get_template_directory_uri' ) ) {
        return '';
    }
    $path = get_post_meta( $att_id, '_wpc_attachment_path', true );
    if ( ! is_string( $path ) || $path === '' ) return '';
    $td = (string) get_template_directory();
    $tu = (string) get_template_directory_uri();
    if ( $td === '' || $tu === '' || strpos( $path, $td ) !== 0 ) return '';
    return $tu . substr( $path, strlen( $td ) );
}

/**
 * EC-CPT-020 — make WordPress/WooCommerce CORE render our theme-bundled
 * featured images correctly.
 *
 * attach_existing_file() deliberately does NOT copy images into /uploads
 * (avoids a 10x-larger media library), so the attachment's
 * `_wp_attached_file` is an absolute theme path. WP's stock resolver then
 * emits a 404 URL shaped like `…/uploads//Users/…/theme/assets/img.jpg`.
 * Our own CPT loop swap dodged this via wpconvert_cpt_get_attachment_url_safe(),
 * but WooCommerce's single-product / shop image markup goes through core
 * wp_get_attachment_image() / _src(), which doesn't — so every product
 * whose featured image was a LOCAL theme file showed "could not load"
 * (neon-elite: the iPhone & Electric Go Kart products). These filters
 * remap the URL to the theme URI for OUR attachments only; real uploads
 * and sideloaded CDN images pass through untouched.
 */
function wpconvert_cpt_filter_attachment_url( $url, $att_id ) {
    $uri = wpconvert_cpt_theme_attachment_uri( $att_id );
    return $uri !== '' ? $uri : $url;
}

/**
 * @param array|false $image  [ url, width, height, is_intermediate ] | false
 * @return array|false
 */
function wpconvert_cpt_filter_attachment_image_src( $image, $attachment_id, $size = 'thumbnail', $icon = false ) {
    $uri = wpconvert_cpt_theme_attachment_uri( $attachment_id );
    if ( $uri === '' ) return $image;
    $w = 0;
    $h = 0;
    if ( function_exists( 'get_post_meta' ) ) {
        $path = get_post_meta( $attachment_id, '_wpc_attachment_path', true );
        if ( is_string( $path ) && $path !== '' && function_exists( 'getimagesize' ) ) {
            $dim = @getimagesize( $path );
            if ( is_array( $dim ) && isset( $dim[0], $dim[1] ) ) {
                $w = (int) $dim[0];
                $h = (int) $dim[1];
            }
        }
    }
    return array( $uri, $w, $h, false );
}

/**
 * @param array  $attr
 * @param object $attachment  WP_Post
 * @return array
 */
function wpconvert_cpt_filter_attachment_image_attributes( $attr, $attachment ) {
    $att_id = ( is_object( $attachment ) && isset( $attachment->ID ) ) ? (int) $attachment->ID : 0;
    $uri = wpconvert_cpt_theme_attachment_uri( $att_id );
    if ( $uri === '' ) return is_array( $attr ) ? $attr : array();
    if ( ! is_array( $attr ) ) $attr = array();
    // Theme-bundled files have no valid intermediate sizes, so a srcset
    // built from absolute theme paths 404s — force the single theme src.
    unset( $attr['srcset'], $attr['sizes'] );
    $attr['src'] = $uri;
    return $attr;
}

/**
 * Sideload an external image URL via WP's media_sideload_image. Wraps
 * the call in SSRF + size guards and returns 0 on any failure.
 *
 * @param string $url
 * @return int  Attachment ID or 0.
 */
function wpconvert_cpt_sideload_external_image( $url ) {
    if ( ! wpconvert_cpt_is_safe_image_url( $url ) ) return 0;
    if ( ! function_exists( 'media_sideload_image' ) ) {
        // Older WP without the upload helper. Require admin context to
        // load it explicitly rather than bringing in the file every time.
        $abspath = defined( 'ABSPATH' ) ? ABSPATH : '';
        if ( $abspath && file_exists( $abspath . 'wp-admin/includes/media.php' ) ) {
            require_once $abspath . 'wp-admin/includes/media.php';
            require_once $abspath . 'wp-admin/includes/file.php';
            require_once $abspath . 'wp-admin/includes/image.php';
        }
        if ( ! function_exists( 'media_sideload_image' ) ) return 0;
    }

    // HEAD request to enforce the size cap BEFORE we download.
    if ( function_exists( 'wp_safe_remote_head' ) ) {
        $head = wp_safe_remote_head( $url, array( 'timeout' => 10 ) );
        if ( ! is_wp_error( $head ) && function_exists( 'wp_remote_retrieve_header' ) ) {
            $len = (int) wp_remote_retrieve_header( $head, 'content-length' );
            if ( $len > 0 && $len > WPCONVERT_CPT_IMAGE_MAX_BYTES ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'wpconvert_cpt sideload skipped — Content-Length ' . $len . ' exceeds cap' );
                }
                return 0;
            }
        }
    }

    // EC-CPT-015 — media_sideload_image() hard-rejects URLs whose PATH has
    // no image extension ("Invalid image URL"), which covers every modern
    // image CDN (Unsplash, Cloudinary, imgix: /photo-15872…?w=800). Those
    // go through the download-then-sniff path instead.
    $url_path = (string) @parse_url( $url, PHP_URL_PATH );
    if ( ! preg_match( '/\.(jpe?g|png|gif|webp|avif)$/i', $url_path ) ) {
        return wpconvert_cpt_sideload_extensionless_image( $url );
    }

    $id = media_sideload_image( $url, 0, null, 'id' );
    if ( is_wp_error( $id ) || ! $id ) return 0;
    return (int) $id;
}

/**
 * EC-CPT-015 — sideload an image whose URL path lacks an extension.
 *
 * download_url() fetches to a temp file, wp_get_image_mime() sniffs the
 * REAL type from the bytes (never trusting the URL), and
 * media_handle_sideload() runs WP's full upload validation with a
 * synthesized "<basename>.<ext>" filename. Size cap enforced after
 * download (the HEAD-based cap in the caller already handled servers
 * that send Content-Length).
 *
 * @param string $url
 * @return int  Attachment ID, or 0 on any failure.
 */
function wpconvert_cpt_sideload_extensionless_image( $url ) {
    if ( ! function_exists( 'download_url' ) || ! function_exists( 'media_handle_sideload' ) ) {
        $abspath = defined( 'ABSPATH' ) ? ABSPATH : '';
        if ( $abspath && file_exists( $abspath . 'wp-admin/includes/media.php' ) ) {
            require_once $abspath . 'wp-admin/includes/media.php';
            require_once $abspath . 'wp-admin/includes/file.php';
            require_once $abspath . 'wp-admin/includes/image.php';
        }
        if ( ! function_exists( 'download_url' ) || ! function_exists( 'media_handle_sideload' ) ) return 0;
    }

    $tmp = download_url( $url, 30 );
    if ( is_wp_error( $tmp ) || ! is_string( $tmp ) || $tmp === '' ) return 0;

    $fail = function () use ( $tmp ) {
        if ( file_exists( $tmp ) ) @unlink( $tmp );
        return 0;
    };

    $size = @filesize( $tmp );
    if ( $size === false || $size <= 0 || $size > WPCONVERT_CPT_IMAGE_MAX_BYTES ) {
        return $fail();
    }

    $mime = function_exists( 'wp_get_image_mime' ) ? wp_get_image_mime( $tmp ) : false;
    $ext_by_mime = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
    );
    if ( ! is_string( $mime ) || ! isset( $ext_by_mime[ $mime ] ) ) {
        return $fail();
    }

    $base = basename( (string) @parse_url( $url, PHP_URL_PATH ) );
    $base = (string) preg_replace( '/[^A-Za-z0-9_-]/', '-', $base );
    $base = trim( $base, '-' );
    if ( $base === '' ) $base = 'wpc-image';

    $file_array = array(
        'name'     => $base . '.' . $ext_by_mime[ $mime ],
        'tmp_name' => $tmp,
    );
    $id = media_handle_sideload( $file_array, 0 );
    if ( is_wp_error( $id ) || ! $id ) {
        return $fail();
    }
    return (int) $id;
}

/* ─────────────────────────────────────────────
 * 10. IMPORTER CORE  (Ship 3)
 * ───────────────────────────────────────────── */

/**
 * Acquire a transient lock for an in-progress import. Returns false if
 * another request is already importing the same section_key, true if
 * the lock was acquired by this call.
 *
 * TTL: 5 minutes (long enough for any reasonable batch + sideload, short
 * enough that a crashed import won't permanently block restarts).
 *
 * @param string $section_key
 * @return bool
 */
function wpconvert_cpt_acquire_import_lock( $section_key ) {
    if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
        return true; // No transients available — best-effort proceed.
    }
    $lock_key = 'wpconvert_cpt_importing_' . substr( sha1( (string) $section_key ), 0, 16 );
    if ( get_transient( $lock_key ) ) {
        return false;
    }
    set_transient( $lock_key, 1, 5 * 60 );
    return true;
}

/**
 * Release the import lock. Idempotent.
 *
 * @param string $section_key
 */
function wpconvert_cpt_release_import_lock( $section_key ) {
    if ( ! function_exists( 'delete_transient' ) ) return;
    $lock_key = 'wpconvert_cpt_importing_' . substr( sha1( (string) $section_key ), 0, 16 );
    delete_transient( $lock_key );
}

/**
 * Find an already-imported post for a given (section_key, item_index)
 * pair. Returns 0 if none exists.
 *
 * @param string $section_key
 * @param int    $item_index
 * @return int  Post ID or 0.
 */
function wpconvert_cpt_find_imported_post_id( $section_key, $item_index ) {
    if ( ! function_exists( 'get_posts' ) ) return 0;
    $found = get_posts( array(
        'post_type'      => 'any',
        'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_wpc_imported_section_key',
                'value'   => (string) $section_key,
                'compare' => '=',
            ),
            array(
                'key'     => '_wpc_imported_item_index',
                'value'   => (int) $item_index,
                'compare' => '=',
            ),
        ),
    ) );
    return ( is_array( $found ) && ! empty( $found ) ) ? (int) $found[0] : 0;
}

/**
 * Import a single item (one row from `candidate.items`) as a new post,
 * or update the existing one if (section_key, item_index) matches.
 *
 * @param string $section_key
 * @param int    $item_index
 * @param array  $item            Decoded item dict (remapped keys).
 * @param array  $candidate       The candidate entry from the manifest.
 * @param array  $cpt_config      The config entry from wp_options['wpconvert_cpts'].
 * @param array  $args            { dry_run: bool }
 * @return array  { status: 'created'|'updated'|'failed', post_id: int, error?: string }
 */
function wpconvert_cpt_import_item( $section_key, $item_index, $item, $candidate, $cpt_config, $args = array() ) {
    $dry_run = ! empty( $args['dry_run'] );

    try {
        if ( ! is_array( $item ) || empty( $item ) ) {
            return array( 'status' => 'failed', 'post_id' => 0, 'error' => 'empty-item' );
        }
        $post_type = isset( $cpt_config['post_type'] ) ? $cpt_config['post_type'] : '';
        if ( $post_type === '' ) {
            return array( 'status' => 'failed', 'post_id' => 0, 'error' => 'no-post-type' );
        }

        // Determine the canonical fields. The candidate's `anchor_field` is
        // the title source. The first text-long field becomes post_content.
        $anchor_remapped = null;
        $first_text_long_key = null;
        $first_image_key = null;
        $first_description_key = null;
        $field_types_by_key = array();
        $field_enums_by_key = array();
        foreach ( $candidate['fields'] as $f ) {
            $k = isset( $f['remapped_to'] ) ? $f['remapped_to'] : ( $f['key'] ?? '' );
            if ( $k === '' ) continue;
            $field_types_by_key[ $k ] = isset( $f['type'] ) ? $f['type'] : 'unknown';
            if ( isset( $f['enum'] ) && is_array( $f['enum'] ) ) {
                $field_enums_by_key[ $k ] = $f['enum'];
            }
            $orig = isset( $f['key'] ) ? strtolower( $f['key'] ) : '';
            if ( $orig === $candidate['anchor_field'] ) {
                $anchor_remapped = $k;
            }
            if ( $field_types_by_key[ $k ] === 'text-long' && $first_text_long_key === null ) {
                $first_text_long_key = $k;
            }
            if ( $field_types_by_key[ $k ] === 'image' && $first_image_key === null ) {
                $first_image_key = $k;
            }
            if ( $first_description_key === null
                && in_array( $orig, array( 'description', 'excerpt', 'summary', 'desc' ), true ) ) {
                $first_description_key = $k;
            }
        }

        // Compose post fields.
        $post_title = $anchor_remapped && isset( $item[ $anchor_remapped ] )
            ? (string) $item[ $anchor_remapped ]
            : '';
        if ( $post_title === '' ) {
            // Fall back to ANY text-short field rather than a blank title.
            foreach ( $item as $k => $v ) {
                if ( is_string( $v ) && $v !== '' && isset( $field_types_by_key[ $k ] )
                    && in_array( $field_types_by_key[ $k ], array( 'text-short', 'select' ), true ) ) {
                    $post_title = $v;
                    break;
                }
            }
        }
        if ( $post_title === '' ) {
            $post_title = '(no title)';
        }

        $post_content = $first_text_long_key && isset( $item[ $first_text_long_key ] )
            ? (string) $item[ $first_text_long_key ]
            : '';
        $post_excerpt = $first_description_key && isset( $item[ $first_description_key ] )
            ? (string) $item[ $first_description_key ]
            : '';

        $existing_id = wpconvert_cpt_find_imported_post_id( $section_key, $item_index );

        if ( $dry_run ) {
            return array(
                'status'        => $existing_id ? 'would-update' : 'would-create',
                'post_id'       => $existing_id,
                'title_preview' => $post_title,
            );
        }

        if ( ! function_exists( 'wp_insert_post' ) || ! function_exists( 'wp_update_post' ) ) {
            return array( 'status' => 'failed', 'post_id' => 0, 'error' => 'wp-not-available' );
        }

        $post_data = array(
            'post_title'   => function_exists( 'sanitize_text_field' )
                ? sanitize_text_field( $post_title )
                : strip_tags( $post_title ),
            'post_content' => function_exists( 'wp_kses_post' )
                ? wp_kses_post( $post_content )
                : strip_tags( $post_content ),
            'post_excerpt' => function_exists( 'sanitize_textarea_field' )
                ? sanitize_textarea_field( $post_excerpt )
                : strip_tags( $post_excerpt ),
            'post_status'  => 'publish',
            'post_type'    => $post_type,
            'menu_order'   => (int) $item_index,
            'post_author'  => function_exists( 'get_current_user_id' )
                ? (int) get_current_user_id()
                : 0,
        );

        if ( $existing_id ) {
            $post_data['ID'] = $existing_id;
            $result = wp_update_post( $post_data, true );
            if ( is_wp_error( $result ) || ! $result ) {
                $msg = is_wp_error( $result ) ? $result->get_error_message() : 'update-failed';
                return array( 'status' => 'failed', 'post_id' => $existing_id, 'error' => $msg );
            }
            $post_id = $existing_id;
            $status_out = 'updated';
        } else {
            $result = wp_insert_post( $post_data, true );
            if ( is_wp_error( $result ) || ! $result ) {
                $msg = is_wp_error( $result ) ? $result->get_error_message() : 'insert-failed';
                return array( 'status' => 'failed', 'post_id' => 0, 'error' => $msg );
            }
            $post_id = (int) $result;
            $status_out = 'created';
        }

        // Store every field as wp_postmeta, with per-type sanitization.
        if ( function_exists( 'update_post_meta' ) ) {
            foreach ( $item as $key => $value ) {
                // EC-CPT-ICON-001 — internal build-time-captured markup (per-item
                // icon SVG under `__wpc_icon`, etc.). Store RAW: it's our own
                // trusted markup, so sanitize_text_field must NOT strip the <svg>.
                // Never surfaced as an editable field, so no ACF/Meta Box dual-write.
                if ( strpos( (string) $key, '__wpc_' ) === 0 ) {
                    update_post_meta(
                        $post_id,
                        wpconvert_cpt_meta_key_for_field( (string) $key ),
                        is_string( $value ) ? $value : ''
                    );
                    continue;
                }
                $type = isset( $field_types_by_key[ $key ] ) ? $field_types_by_key[ $key ] : 'unknown';
                $enum = isset( $field_enums_by_key[ $key ] ) ? $field_enums_by_key[ $key ] : null;
                $sanitized = wpconvert_cpt_sanitize_field_value( $value, $type, $enum );
                $meta_key = wpconvert_cpt_meta_key_for_field( $key );
                update_post_meta( $post_id, $meta_key, $sanitized );

                // Ship 4c.1 hotfix #2 — when the CPT is ACF-managed, ALSO
                // write the value at the ACF-shape meta keys so the ACF
                // UI (and `get_field()`) see the imported data. Without
                // this, every imported post would render blank in the
                // ACF editor and the loop swap would have to fall back
                // to our `_wpc_field_*` meta — losing the ACF advantage.
                wpconvert_cpt_dual_write_acf_meta(
                    $post_id, (string) $key, $value, $sanitized, (string) $type, $cpt_config
                );

                // EC-CPT-008 — same idea for Meta Box: write the value at the
                // bare Meta Box field id so MB Builder's editor + rwmb_meta()
                // see imported data. No-op unless cfg.metabox_managed + Meta
                // Box loaded. (Mutually exclusive with ACF per CPT.)
                wpconvert_cpt_dual_write_metabox_meta(
                    $post_id, (string) $key, $value, $sanitized, (string) $type, $cpt_config
                );
            }

            update_post_meta( $post_id, '_wpc_imported_section_key', (string) $section_key );
            update_post_meta( $post_id, '_wpc_imported_item_index', (int) $item_index );

            // Featured image — resolved from the first detected image field.
            if ( $first_image_key && isset( $item[ $first_image_key ] ) ) {
                $att_id = wpconvert_cpt_resolve_image_to_attachment_id( $item[ $first_image_key ] );
                if ( $att_id ) {
                    update_post_meta( $post_id, '_thumbnail_id', (int) $att_id );
                }
            }
        }

        return array( 'status' => $status_out, 'post_id' => $post_id );
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt import_item: ' . $e->getMessage() );
        }
        return array( 'status' => 'failed', 'post_id' => 0, 'error' => $e->getMessage() );
    }
}

/**
 * Drive an import for one CPT candidate. Reads the manifest, looks up
 * the candidate by section_key, iterates `items`, calls
 * `wpconvert_cpt_import_item` for each.
 *
 * Race-safe (transient lock), idempotent (per-item dedupe via
 * _wpc_imported_section_key meta), capped (max 1000 items per call).
 *
 * @param string $section_key
 * @param array  $args { dry_run: bool, limit: int|null, offset: int }
 * @return array
 */
function wpconvert_cpt_import_candidate( $section_key, $args = array() ) {
    $summary = array(
        'section_key' => (string) $section_key,
        'attempted'   => 0,
        'created'     => 0,
        'updated'     => 0,
        'failed'      => 0,
        'skipped'     => 0,
        'errors'      => array(),
        'dry_run'     => ! empty( $args['dry_run'] ),
        'is_complete' => false,
    );

    if ( ! wpconvert_cpt_should_run() ) {
        $summary['errors'][] = 'tier-or-version-gate';
        return $summary;
    }

    if ( ! wpconvert_cpt_acquire_import_lock( $section_key ) ) {
        $summary['errors'][] = 'lock-held';
        return $summary;
    }

    try {
        // Lookup candidate in manifest.
        $candidates = wpconvert_cpt_get_candidates_manifest();
        $candidate = null;
        foreach ( $candidates as $c ) {
            if ( isset( $c['section_key'] ) && $c['section_key'] === $section_key ) {
                $candidate = $c;
                break;
            }
        }
        if ( ! $candidate ) {
            $summary['errors'][] = 'candidate-not-found';
            return $summary;
        }

        // Lookup CPT config. Caller can pass `cpt_config` directly (used by
        // the AJAX dry-run path so the importer doesn't have to depend on a
        // pre-written option — without this, dry-run always returned
        // "0 of 0" because the option wasn't written yet).
        if ( ! empty( $args['cpt_config'] ) && is_array( $args['cpt_config'] ) ) {
            $cpt_config = $args['cpt_config'];
        } else {
            $active = wpconvert_cpt_get_active_cpts();
            if ( ! isset( $active[ $section_key ] ) || empty( $active[ $section_key ]['enabled'] ) ) {
                $summary['errors'][] = 'cpt-not-activated';
                return $summary;
            }
            $cpt_config = $active[ $section_key ];
        }

        // Blog override re-check.
        if ( wpconvert_cpt_is_native_blog_shape( $candidate ) ) {
            $summary['errors'][] = 'native-blog-override';
            return $summary;
        }

        if ( empty( $candidate['items'] ) || ! is_array( $candidate['items'] ) ) {
            $summary['errors'][] = 'manifest-missing-items';
            $summary['is_complete'] = true;
            return $summary;
        }

        $items = $candidate['items'];
        $total = count( $items );
        $offset = max( 0, (int) ( $args['offset'] ?? 0 ) );
        $limit = isset( $args['limit'] ) && $args['limit'] !== null
            ? max( 0, (int) $args['limit'] )
            : 1000;
        // Hard cap to prevent runaway.
        if ( $limit > 1000 ) $limit = 1000;

        $summary['total_items'] = $total;

        $end = min( $total, $offset + $limit );
        for ( $i = $offset; $i < $end; $i++ ) {
            $summary['attempted']++;
            $res = wpconvert_cpt_import_item( $section_key, $i, $items[ $i ], $candidate, $cpt_config, $args );
            switch ( $res['status'] ) {
                case 'created':
                case 'would-create':
                    $summary['created']++;
                    break;
                case 'updated':
                case 'would-update':
                    $summary['updated']++;
                    break;
                case 'failed':
                    $summary['failed']++;
                    if ( ! empty( $res['error'] ) ) {
                        $summary['errors'][] = '#' . $i . ': ' . $res['error'];
                    }
                    break;
                default:
                    $summary['skipped']++;
                    break;
            }
        }

        $summary['is_complete'] = ( $end >= $total );

        // Flag the section as imported (so subsequent UI can show
        // "previously imported" state).
        if ( ! $summary['dry_run'] && function_exists( 'update_option' ) ) {
            $imported_state = function_exists( 'get_option' )
                ? get_option( 'wpconvert_cpt_imported_state', array() )
                : array();
            if ( ! is_array( $imported_state ) ) $imported_state = array();
            $imported_state[ $section_key ] = array(
                'last_imported_at' => time(),
                'created'          => (int) $summary['created'],
                'updated'          => (int) $summary['updated'],
                'failed'           => (int) $summary['failed'],
                'total_items'      => (int) $total,
            );
            update_option( 'wpconvert_cpt_imported_state', $imported_state, false );
        }

        return $summary;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt import_candidate: ' . $e->getMessage() );
        }
        $summary['errors'][] = 'exception: ' . $e->getMessage();
        return $summary;
    } finally {
        wpconvert_cpt_release_import_lock( $section_key );
    }
}

/* ─────────────────────────────────────────────
 * 11. AJAX  (Ship 3 — combined activate + import endpoint)
 * ───────────────────────────────────────────── */

/**
 * One-shot AJAX endpoint that:
 *   1. Validates the requested CPT config
 *   2. Writes wp_options['wpconvert_cpts'][section_key] = {...config}
 *   3. Calls register_post_type for the new CPT (so this request can
 *      wp_insert_post into it)
 *   4. Runs the importer for the section
 *   5. Schedules a rewrite-rule flush (so pretty permalinks update)
 *   6. Returns the import summary
 *
 * Security:
 *   - tier gate (`wpconvert_cpt_should_run`)
 *   - manage_options capability
 *   - nonce: wpconvert_cpt_nonce / nonce
 *   - all inputs sanitized before use
 */
function wpconvert_cpt_ajax_activate_and_import() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }

    // IMPORTANT: keep wp_send_json_success / wp_send_json_error OUTSIDE the
    // try block. In real WP they call wp_die() which halts execution; any
    // try/catch wrapping the success path would intercept the halt and
    // could double-emit a response. We collect into local vars instead.
    $error_code = null;
    $error_status = 0;
    $result = null;

    try {
        $section_key = isset( $_POST['section_key'] )
            ? sanitize_text_field( wp_unslash( $_POST['section_key'] ) )
            : '';
        $post_type_raw = isset( $_POST['post_type'] )
            ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) )
            : '';
        $singular = isset( $_POST['singular'] )
            ? sanitize_text_field( wp_unslash( $_POST['singular'] ) )
            : '';
        $plural = isset( $_POST['plural'] )
            ? sanitize_text_field( wp_unslash( $_POST['plural'] ) )
            : '';
        $menu_icon = isset( $_POST['menu_icon'] )
            ? sanitize_text_field( wp_unslash( $_POST['menu_icon'] ) )
            : 'dashicons-admin-post';
        $do_import = ! isset( $_POST['import'] ) || (bool) $_POST['import'];
        $dry_run = ! empty( $_POST['dry_run'] );

        if ( $section_key === '' || ! preg_match( '/^[a-f0-9]{40}$/i', $section_key ) ) {
            $error_code = 'invalid-section-key';
        } else {
            $post_type = wpconvert_cpt_normalize_post_type_slug( $post_type_raw );
            // Ship 4c.7 / B1 — extended slug validation: the legacy
            // checks below already covered invalid slugs + post-type
            // collisions, but missed the page/post slug case that
            // hijacks existing WP page URLs. wpconvert_cpt_validate_slug
            // is the single source of truth used by both this AJAX
            // endpoint AND the new live slug-check endpoint.
            $slug_check = wpconvert_cpt_validate_slug( $post_type );
            if ( ! $slug_check['ok'] ) {
                $error_code = 'slug-' . $slug_check['reason'];
                // Carry the structured detail so the JS can show the
                // human-readable message + alt suggestion in the prompt.
                if ( isset( $slug_check['suggestion'] ) ) {
                    $result = array(
                        'error'      => $slug_check['reason'],
                        'message'    => $slug_check['message'],
                        'suggestion' => $slug_check['suggestion'],
                    );
                }
            } elseif ( ! wpconvert_cpt_is_valid_slug( $post_type ) ) {
                $error_code = 'invalid-post-type';
            } elseif ( function_exists( 'post_type_exists' ) && post_type_exists( $post_type )
                && ! wpconvert_cpt_is_our_registered_post_type( $post_type ) ) {
                $error_code = 'post-type-collision';
            } else {
                // Verify the section exists in the manifest. We ALSO need
                // the matching candidate object so we can persist its
                // `fields` schema into the activation cfg (Ship 4a).
                // Pre-Ship-4b.2 hotfix: `$candidate` was never captured here,
                // so `$cfg['fields']` silently dropped — and at front-end
                // render time the filter-attr validation in
                // wpconvert_cpt_process_section failed for every field,
                // disabling Ship 4b.2 meta_query filtering AND breaking the
                // meta-box renderer's enum dropdowns.
                $found = false;
                $candidate = null;
                foreach ( wpconvert_cpt_get_candidates_manifest() as $c ) {
                    if ( isset( $c['section_key'] ) && $c['section_key'] === $section_key ) {
                        $found = true;
                        $candidate = $c;
                        break;
                    }
                }
                if ( ! $found ) {
                    $error_code = 'section-not-in-manifest';
                } elseif ( ! isset( wpconvert_cpt_get_stamped_section_keys()[ $section_key ] ) ) {
                    // Ship 4c.2 — orphan guard at the activation gate.
                    // Even if a stale modal payload sneaks past the
                    // filtered notice (e.g. user opened it before a
                    // theme regen), refuse to activate a candidate
                    // whose section_key isn't stamped anywhere in the
                    // theme. Activating an orphan creates posts the FE
                    // can't render — a silent dead-end the user would
                    // hit later. Better to fail fast.
                    $error_code = 'candidate-not-renderable';
                } else {
                    // Compose the config.
                    // Ship 4c.1 — opt-in flag for ACF-managed mode. Only
                    // honored when ACF is actually loaded; an out-of-band
                    // POST with the flag but no ACF runtime is silently
                    // ignored (cfg.acf_managed stays false). This keeps the
                    // activation safe against stale modal state.
                    $acf_managed_requested = ! empty( $_POST['acf_managed'] );
                    $acf_managed = $acf_managed_requested && wpconvert_cpt_acf_available();

                    // EC-CPT-008 — Meta Box opt-in. Same defensive downgrade as
                    // ACF. ACF and Meta Box are mutually exclusive per CPT; if
                    // both flags arrive, ACF wins (back-compat) and Meta Box is
                    // dropped with a log line.
                    $metabox_managed_requested = ! empty( $_POST['metabox_managed'] );
                    $metabox_managed = $metabox_managed_requested && wpconvert_cpt_metabox_available();
                    if ( $metabox_managed && $acf_managed ) {
                        $metabox_managed = false;
                        if ( function_exists( 'error_log' ) ) {
                            error_log( 'wpconvert_cpt: both acf_managed and metabox_managed requested for "' . $post_type . '" — ACF wins, Meta Box dropped.' );
                        }
                    }

                    $cfg = array(
                        'enabled'         => true,
                        'post_type'       => $post_type,
                        'singular'        => $singular !== '' ? $singular : ucwords( str_replace( '_', ' ', $post_type ) ),
                        'plural'          => $plural !== '' ? $plural : '',
                        'menu_icon'       => $menu_icon,
                        'public'          => true,
                        'has_archive'     => true,
                        'show_in_rest'    => true,
                        'activated_at'    => time(),
                        'acf_managed'     => $acf_managed,
                        'metabox_managed' => $metabox_managed,
                    );
                    if ( $cfg['plural'] === '' ) {
                        $cfg['plural'] = $cfg['singular'] . 's';
                    }
                    // Ship 4a — persist the field schema into the activation
                    // so meta-box rendering doesn't need the manifest file at
                    // every admin pageload. Trimmed copy (key, type, remapped_to,
                    // enum) — drops samples + remap_reason to keep the option
                    // payload small.
                    if ( ! empty( $candidate['fields'] ) && is_array( $candidate['fields'] ) ) {
                        $cfg['fields'] = array();
                        foreach ( $candidate['fields'] as $f ) {
                            if ( empty( $f['key'] ) ) continue;
                            $trim = array(
                                'key'         => (string) $f['key'],
                                'type'        => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                                'remapped_to' => isset( $f['remapped_to'] ) ? (string) $f['remapped_to'] : (string) $f['key'],
                            );
                            if ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) {
                                $trim['enum'] = array_values( array_slice( $f['enum'], 0, 50 ) );
                            }
                            $cfg['fields'][] = $trim;
                        }
                    }

                    // Write the option.
                    if ( ! $dry_run ) {
                        $active = wpconvert_cpt_get_active_cpts();
                        if ( ! is_array( $active ) ) $active = array();
                        $active[ $section_key ] = $cfg;
                        if ( function_exists( 'update_option' ) ) {
                            update_option( 'wpconvert_cpts', $active, false );
                        }
                        // Register the type immediately (this request) so wp_insert_post works.
                        wpconvert_cpt_register_active_post_types();
                        // Schedule a rewrite flush on next init.
                        if ( function_exists( 'update_option' ) ) {
                            update_option( 'wpconvert_cpts_needs_flush', 1, false );
                        }

                        // Ship 4c.1 — when the user opted into ACF mode,
                        // auto-create the ACF field group + fields now,
                        // before the import runs. Idempotent: a second
                        // activation finds the existing group by key and
                        // leaves it alone (preserves user customizations).
                        if ( ! empty( $cfg['acf_managed'] ) ) {
                            wpconvert_cpt_create_acf_group_for_cpt( $cfg, $section_key );
                        }

                        // EC-CPT-008 — same for Meta Box: auto-create the DB
                        // group up-front so editing in MB Builder's UI works
                        // immediately. Idempotent.
                        if ( ! empty( $cfg['metabox_managed'] ) ) {
                            wpconvert_cpt_create_metabox_group_for_cpt( $cfg, $section_key );
                        }
                    }

                    // Run import. For dry-run we pass the in-memory config
                    // explicitly because the activation option was deliberately
                    // NOT written above (dry-run must not persist anything).
                    $summary = array();
                    if ( $do_import ) {
                        $import_args = array( 'dry_run' => $dry_run );
                        if ( $dry_run ) {
                            $import_args['cpt_config'] = $cfg;
                        }
                        $summary = wpconvert_cpt_import_candidate( $section_key, $import_args );
                    }

                    $result = array(
                        'activated' => ! $dry_run,
                        'dry_run'   => $dry_run,
                        'cpt'       => $cfg,
                        'import'    => $summary,
                    );
                }
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt ajax_activate_and_import: ' . $e->getMessage() );
        }
        $error_code = 'internal';
        $error_status = 500;
    }

    if ( $error_code !== null ) {
        // Ship 4c.7 / B1 — when the slug validator filled in $result with
        // a structured error/suggestion payload, pass that through (and
        // include the legacy error code in the payload so existing JS
        // regex-matching on r.data still works).
        if ( is_array( $result ) && isset( $result['error'] ) ) {
            $result['code'] = $error_code;
            wp_send_json_error( $result, $error_status ?: 0 );
        }
        wp_send_json_error( $error_code, $error_status ?: 0 );
    }
    wp_send_json_success( $result );
}

/**
 * Ship 4c.7 / B1 — live slug-check AJAX endpoint. Pending tab JS calls
 * this on debounced input change so the user sees inline validation
 * (red border + tooltip) before they click Activate.
 *
 * Response shape mirrors wpconvert_cpt_validate_slug():
 *   { ok: bool, reason: string|null, message: string, suggestion?: string }
 */
function wpconvert_cpt_ajax_check_slug() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    $raw = isset( $_POST['slug'] ) ? (string) wp_unslash( $_POST['slug'] ) : '';
    // Run through the suggester so the user's free-text input gets the
    // same camelCase/snake_case + sanitization treatment the activation
    // flow applies. Returning the normalized form helps the JS show the
    // ACTUAL slug WordPress will use, not the raw user input.
    $normalized = wpconvert_cpt_suggest_slug_for( $raw );
    $check = wpconvert_cpt_validate_slug( $normalized );
    $check['normalized'] = $normalized;
    $check['input']      = $raw;
    wp_send_json_success( $check );
}

/**
 * Returns true if `$slug` was registered by us (i.e. appears in the
 * active-CPT option). Used by the activate endpoint to allow
 * re-activations of an already-active CPT without flagging a collision.
 *
 * @param string $slug
 * @return bool
 */
function wpconvert_cpt_is_our_registered_post_type( $slug ) {
    foreach ( wpconvert_cpt_get_active_cpts() as $cfg ) {
        if ( is_array( $cfg ) && isset( $cfg['post_type'] ) && $cfg['post_type'] === $slug ) {
            return true;
        }
    }
    return false;
}

/**
 * Dismiss-notice AJAX endpoint. Stores a user-meta flag so the notice
 * doesn't re-appear for the calling admin.
 */
function wpconvert_cpt_ajax_dismiss_notice() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }

    $error_code = null;
    try {
        if ( function_exists( 'update_user_meta' ) && function_exists( 'get_current_user_id' ) ) {
            update_user_meta( get_current_user_id(), 'wpconvert_cpt_notice_dismissed', 1 );
        }
    } catch ( \Throwable $e ) {
        $error_code = 'internal';
    }

    if ( $error_code !== null ) {
        wp_send_json_error( $error_code, 500 );
    }
    wp_send_json_success( array( 'dismissed' => true ) );
}

/**
 * Rewrite-flush hook — runs at init priority 99 (after our priority-9
 * registration). Reads the deferred-flush flag, calls `flush_rewrite_rules`
 * once, then clears the flag. Never runs on regular pageloads.
 */
function wpconvert_cpt_maybe_flush_rewrite_rules() {
    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! function_exists( 'get_option' ) ) return;
    if ( ! get_option( 'wpconvert_cpts_needs_flush' ) ) return;
    try {
        if ( function_exists( 'flush_rewrite_rules' ) ) {
            // No .htaccess rewrite (false = soft flush).
            flush_rewrite_rules( false );
        }
        if ( function_exists( 'delete_option' ) ) {
            delete_option( 'wpconvert_cpts_needs_flush' );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt flush: ' . $e->getMessage() );
        }
    }
}

/* ─────────────────────────────────────────────
 * 11.5 ADMIN DASHBOARD PAGE  (Ship 4c.6 / C1-C7)
 *
 * Tools → WPConvert CPTs. Three tabs:
 *
 *   Active       — list activated CPTs; bulk deactivate / delete; re-sync.
 *   Pending      — list un-activated candidates; bulk activate; activate
 *                  with conflict-suggested-slug retry.
 *   Diagnostics  — manifest path/size, stamp scan, reserved slugs, recent
 *                  log entries, "purge on uninstall" flag, copy-bundle btn.
 *
 * Server-rendered tables. Bulk actions submit as standard POST (full
 * reload after action — no JS dance for the primary flow). Per-row
 * actions like "Delete" and "Re-sync" use AJAX with a confirmation
 * modal.
 *
 * Capability: manage_options (filterable via wpconvert_cpt_manage_cap).
 * Tier-gated: hidden on Starter, hidden on missing manifest.
 * ───────────────────────────────────────────── */

/**
 * Ship 4c.6 / C1 — return the capability required to view/edit
 * activated CPT configs. Filterable so agency clients can hand off
 * day-to-day CPT management to a non-admin role.
 *
 * @return string
 */
function wpconvert_cpt_dashboard_capability() {
    $cap = 'manage_options';
    if ( function_exists( 'apply_filters' ) ) {
        $cap = (string) apply_filters( 'wpconvert_cpt_manage_cap', $cap );
        if ( $cap === '' ) $cap = 'manage_options';
    }
    return $cap;
}

/**
 * Ship 4c.6 / C1 — register the Tools → WPConvert CPTs admin page.
 * Bound to `admin_menu`.
 */
function wpconvert_cpt_register_admin_page() {
    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! function_exists( 'add_management_page' ) ) return;
    $cap = wpconvert_cpt_dashboard_capability();
    add_management_page(
        'WPConvert CPTs',
        'WPConvert CPTs',
        $cap,
        'wpconvert-cpts',
        'wpconvert_cpt_render_dashboard'
    );
}

/**
 * Ship 4c.6 / C1 — main dashboard renderer. Resolves the active tab
 * from $_GET['tab'] (default = active) and delegates to a per-tab
 * renderer.
 */
function wpconvert_cpt_render_dashboard() {
    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'wpconvert-cpt' ) );
    }

    $tabs = array(
        'active'      => __( 'Active', 'wpconvert-cpt' ),
        'pending'     => __( 'Pending', 'wpconvert-cpt' ),
        'diagnostics' => __( 'Diagnostics', 'wpconvert-cpt' ),
    );
    $current = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'active';
    if ( ! isset( $tabs[ $current ] ) ) $current = 'active';

    // Handle bulk action POST (C2/C3) before rendering so the post-action
    // notice + counts are accurate.
    $bulk_result = wpconvert_cpt_handle_bulk_action_post();

    echo '<div class="wrap wpconvert-cpts-dashboard">';
    echo '<h1>' . esc_html__( 'WPConvert CPTs', 'wpconvert-cpt' ) . '</h1>';
    echo '<p class="description">' . esc_html__(
        'Manage the custom post types detected from your converted theme.',
        'wpconvert-cpt'
    ) . '</p>';

    // Bulk-action result notice (sticky across the reload).
    if ( is_array( $bulk_result ) && ! empty( $bulk_result['message'] ) ) {
        $class = ! empty( $bulk_result['error'] ) ? 'notice-error' : 'notice-success';
        echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>'
            . esc_html( $bulk_result['message'] ) . '</p></div>';
    }

    // Tabs
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $tabs as $slug => $label ) {
        $url = function_exists( 'admin_url' )
            ? admin_url( 'tools.php?page=wpconvert-cpts&tab=' . $slug )
            : '?page=wpconvert-cpts&tab=' . $slug;
        $cls = 'nav-tab' . ( $current === $slug ? ' nav-tab-active' : '' );
        echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $cls ) . '">'
            . esc_html( $label ) . '</a>';
    }
    echo '</h2>';

    switch ( $current ) {
        case 'pending':
            wpconvert_cpt_render_pending_tab();
            break;
        case 'diagnostics':
            wpconvert_cpt_render_diagnostics_tab();
            break;
        case 'active':
        default:
            wpconvert_cpt_render_active_tab();
            break;
    }

    echo '</div>';
}

/**
 * Ship 4c.6 / C1 + C2 + C3 + C4 + C7 — Active tab.
 *
 * Lists every activated CPT with bulk-action checkboxes and per-row
 * Re-sync / Deactivate / Delete buttons. Empty-state copy lives at
 * the bottom of the function (C7).
 */
function wpconvert_cpt_render_active_tab() {
    $active = wpconvert_cpt_get_active_cpts();
    $manifest = wpconvert_cpt_get_candidates_manifest();
    $by_key = array();
    foreach ( $manifest as $c ) {
        if ( ! empty( $c['section_key'] ) ) $by_key[ $c['section_key'] ] = $c;
    }
    // Count imported posts per CPT.
    $post_counts = array();
    if ( function_exists( 'get_posts' ) ) {
        foreach ( $active as $sk => $cfg ) {
            if ( empty( $cfg['post_type'] ) ) continue;
            $ids = get_posts( array(
                'post_type'   => $cfg['post_type'],
                'post_status' => 'any',
                'fields'      => 'ids',
                'numberposts' => -1,
            ) );
            $post_counts[ $sk ] = is_array( $ids ) ? count( $ids ) : 0;
        }
    }

    if ( empty( $active ) ) {
        // C7 — empty state with clear next-step.
        echo '<div class="wpc-cpt-empty-state" style="padding:2em;text-align:center;background:#f6f7f7;border:1px solid #c3c4c7;margin-top:1em;">';
        echo '<p><strong>' . esc_html__( 'No CPTs activated yet.', 'wpconvert-cpt' ) . '</strong></p>';
        echo '<p>' . esc_html__(
            'Visit the Pending tab to activate a custom post type detected in your theme.',
            'wpconvert-cpt'
        ) . '</p>';
        $pending_url = function_exists( 'admin_url' )
            ? admin_url( 'tools.php?page=wpconvert-cpts&tab=pending' )
            : '?page=wpconvert-cpts&tab=pending';
        echo '<a href="' . esc_url( $pending_url ) . '" class="button button-primary">'
            . esc_html__( 'View Pending Candidates', 'wpconvert-cpt' ) . '</a>';
        echo '</div>';
        return;
    }

    $nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_bulk' ) : '';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="wpc_cpt_bulk_nonce" value="' . esc_attr( $nonce ) . '" />';
    echo '<input type="hidden" name="wpc_cpt_bulk_tab" value="active" />';

    // Bulk action select.
    echo '<div class="tablenav top"><div class="alignleft actions bulkactions">';
    echo '<label for="bulk-action-active" class="screen-reader-text">'
        . esc_html__( 'Select bulk action', 'wpconvert-cpt' ) . '</label>';
    echo '<select name="wpc_cpt_bulk_action" id="bulk-action-active">';
    echo '<option value="">' . esc_html__( 'Bulk actions', 'wpconvert-cpt' ) . '</option>';
    echo '<option value="deactivate">' . esc_html__( 'Deactivate (keep posts)', 'wpconvert-cpt' ) . '</option>';
    echo '<option value="delete-keep-posts">' . esc_html__( 'Delete CPT (convert posts to posts)', 'wpconvert-cpt' ) . '</option>';
    echo '<option value="delete-with-posts">' . esc_html__( 'Delete CPT + trash posts', 'wpconvert-cpt' ) . '</option>';
    echo '<option value="resync">' . esc_html__( 'Re-sync from manifest', 'wpconvert-cpt' ) . '</option>';
    echo '</select>';
    echo '<input type="submit" class="button action" value="' . esc_attr__( 'Apply', 'wpconvert-cpt' ) . '" />';
    echo '</div></div>';

    echo '<table class="wp-list-table widefat fixed striped wpc-cpt-active-table">';
    echo '<thead><tr>';
    echo '<td class="manage-column column-cb check-column"><input type="checkbox" id="wpc-cpt-cb-select-all" /></td>';
    echo '<th>' . esc_html__( 'Singular / Plural', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Slug', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Posts', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Fields', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Managed editor', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'In Manifest', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Actions', 'wpconvert-cpt' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( $active as $sk => $cfg ) {
        $sk_e   = esc_attr( (string) $sk );
        $sing   = isset( $cfg['singular'] ) ? (string) $cfg['singular'] : '';
        $plur   = isset( $cfg['plural'] ) ? (string) $cfg['plural'] : '';
        $slug   = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
        $fields = isset( $cfg['fields'] ) && is_array( $cfg['fields'] ) ? count( $cfg['fields'] ) : 0;
        $acf    = ! empty( $cfg['acf_managed'] );
        $mb     = ! empty( $cfg['metabox_managed'] );
        $in_mfst = isset( $by_key[ $sk ] );
        $pcount = isset( $post_counts[ $sk ] ) ? (int) $post_counts[ $sk ] : 0;

        echo '<tr data-section-key="' . $sk_e . '">';
        echo '<th scope="row" class="check-column"><input type="checkbox" name="wpc_cpt_bulk_keys[]" value="' . $sk_e . '" /></th>';
        echo '<td><strong>' . esc_html( $sing ) . '</strong><br /><span class="description">' . esc_html( $plur ) . '</span></td>';
        echo '<td><code>' . esc_html( $slug ) . '</code></td>';
        echo '<td>' . esc_html( (string) $pcount ) . '</td>';
        echo '<td>' . esc_html( (string) $fields ) . '</td>';
        if ( $acf ) {
            echo '<td><span style="color:#00a32a;">' . esc_html__( 'ACF', 'wpconvert-cpt' ) . '</span></td>';
        } elseif ( $mb ) {
            echo '<td><span style="color:#00a32a;">' . esc_html__( 'Meta Box', 'wpconvert-cpt' ) . '</span></td>';
        } else {
            echo '<td><span style="color:#8c8f94;">' . esc_html__( 'Built-in', 'wpconvert-cpt' ) . '</span></td>';
        }
        echo '<td>' . ( $in_mfst ? '<span style="color:#00a32a;">&#10003;</span>'
                                 : '<span style="color:#d63638;" title="' . esc_attr__( 'CPT activated but no longer in manifest (orphan)', 'wpconvert-cpt' )
                                   . '">&#9888;</span>' ) . '</td>';
        echo '<td>';
        $edit_url = function_exists( 'admin_url' ) ? admin_url( 'edit.php?post_type=' . $slug ) : '#';
        echo '<a href="' . esc_url( $edit_url ) . '" class="button button-small">' . esc_html__( 'Edit Posts', 'wpconvert-cpt' ) . '</a> ';
        echo '<button type="button" class="button button-small wpc-cpt-resync-btn" data-sk="' . $sk_e . '">'
            . esc_html__( 'Re-sync', 'wpconvert-cpt' ) . '</button> ';
        echo '<button type="button" class="button button-small button-link-delete wpc-cpt-delete-btn" data-sk="' . $sk_e . '" data-singular="' . esc_attr( $sing ) . '" data-count="' . (int) $pcount . '">'
            . esc_html__( 'Delete…', 'wpconvert-cpt' ) . '</button>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</form>';

    // Per-row AJAX glue (delete confirmation + re-sync).
    $ajax_url = function_exists( 'admin_url' ) ? admin_url( 'admin-ajax.php' ) : '/wp-admin/admin-ajax.php';
    $row_nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_nonce' ) : '';
    ?>
    <script>
    (function(){
      var ajaxUrl = <?php echo wp_json_encode( $ajax_url ); ?>;
      var nonce = <?php echo wp_json_encode( $row_nonce ); ?>;
      function postForm(action, fields) {
        var fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', nonce);
        Object.keys(fields).forEach(function(k){ fd.append(k, fields[k]); });
        return fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
          .then(function(r){ return r.json(); });
      }
      var selAll = document.getElementById('wpc-cpt-cb-select-all');
      if (selAll) selAll.addEventListener('change', function(e){
        document.querySelectorAll('input[name="wpc_cpt_bulk_keys[]"]').forEach(function(cb){ cb.checked = e.target.checked; });
      });
      document.querySelectorAll('.wpc-cpt-delete-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
          var sk = btn.getAttribute('data-sk');
          var sing = btn.getAttribute('data-singular') || 'this CPT';
          var count = parseInt(btn.getAttribute('data-count')||'0', 10);
          var msg = 'Delete the CPT "' + sing + '"?\n\n';
          if (count > 0) {
            msg += 'It has ' + count + ' imported post' + (count === 1 ? '' : 's') + '.\n\n';
          }
          msg += 'Click OK to delete the CPT AND TRASH all its posts (you can restore them from Trash within 30 days).\n';
          msg += 'Click Cancel and use Bulk → "Delete CPT (convert posts to posts)" if you want to keep the posts as standard WP posts.';
          if (!window.confirm(msg)) return;
          btn.disabled = true;
          postForm('wpconvert_cpt_delete', { section_key: sk, mode: 'with-posts' })
            .then(function(r){ if (r && r.success) { window.location.reload(); }
                               else { window.alert('Delete failed: ' + (r && r.data ? JSON.stringify(r.data) : 'unknown')); btn.disabled = false; }
                             });
        });
      });
      document.querySelectorAll('.wpc-cpt-resync-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
          if (!window.confirm('Re-sync this CPT from the theme manifest? New items will be created, existing items updated. No posts will be deleted.')) return;
          var sk = btn.getAttribute('data-sk');
          btn.disabled = true;
          var old = btn.textContent;
          btn.textContent = 'Working…';
          postForm('wpconvert_cpt_resync', { section_key: sk })
            .then(function(r){
              btn.textContent = old; btn.disabled = false;
              if (r && r.success) {
                var s = r.data && r.data.summary ? r.data.summary : {};
                window.alert('Re-sync complete:\n' +
                  'Created: ' + (s.created || 0) + '\n' +
                  'Updated: ' + (s.updated || 0) + '\n' +
                  'Failed: ' + (s.failed || 0));
                window.location.reload();
              } else {
                window.alert('Re-sync failed: ' + (r && r.data ? JSON.stringify(r.data) : 'unknown'));
              }
            });
        });
      });
    })();
    </script>
    <?php
}

/**
 * Ship 4c.6 / C1 + C2 + C6 + C7 — Pending tab.
 *
 * Lists every candidate in the manifest that ISN'T currently activated.
 * Bulk activate button + per-row Activate button with conflict-aware
 * slug suggestion (C6).
 */
function wpconvert_cpt_render_pending_tab() {
    // Ship 4c.7 / B8 — single source of truth for "activatable pending
    // candidates". Both the admin notice and the dashboard's Pending tab
    // now read through the same filter so they can't drift. Pre-B8 the
    // notice had an orphan + native-blog guard that the Pending tab
    // didn't, surfacing chart-data candidates (shareData, growthData)
    // that would silently no-op on the front-end if activated. The
    // user (May 19 2026) caught the mismatch when the notice showed 2
    // candidates but the Pending tab listed 4. Caller passes
    // `include_orphan_diagnostics = true` if it wants to surface the
    // hidden-by-orphan-guard candidates as a tooltip / footer.
    list( $pending, $hidden_orphans ) = wpconvert_cpt_get_activatable_pending_candidates( true );

    if ( empty( $pending ) ) {
        // C7 — empty state. Pull the manifest just for the empty-state
        // branch so we can distinguish "nothing detected" from "all
        // detected items have been activated already".
        $manifest_for_empty = wpconvert_cpt_get_candidates_manifest();
        echo '<div class="wpc-cpt-empty-state" style="padding:2em;text-align:center;background:#f6f7f7;border:1px solid #c3c4c7;margin-top:1em;">';
        if ( empty( $manifest_for_empty ) ) {
            echo '<p><strong>' . esc_html__( 'No CPT candidates detected.', 'wpconvert-cpt' ) . '</strong></p>';
            echo '<p>' . esc_html__(
                'Reconvert your theme on WPConvert.ai to refresh the manifest, or check the Diagnostics tab for details.',
                'wpconvert-cpt'
            ) . '</p>';
        } else {
            // EC-CPT-018 — honest empty states. Pre-fix this branch always
            // claimed "All detected CPTs are already activated", which is
            // wrong (and confusing — neon-elite user report, Jun 2026) when
            // NOTHING is active yet: e.g. the manifest holds only a
            // WooCommerce-intent product catalog (handled by the WC import
            // flow, never listed here) plus orphan-hidden candidates.
            $active_cpt_count = count( wpconvert_cpt_get_active_cpts() );
            $wc_intent_count = 0;
            foreach ( $manifest_for_empty as $cand_for_empty ) {
                if ( is_array( $cand_for_empty )
                    && isset( $cand_for_empty['intent'] )
                    && $cand_for_empty['intent'] === 'woocommerce-product' ) {
                    $wc_intent_count++;
                }
            }
            if ( $active_cpt_count > 0 ) {
                echo '<p><strong>' . esc_html__( 'All detected CPTs are already activated.', 'wpconvert-cpt' ) . '</strong></p>';
                echo '<p>' . esc_html__(
                    'Visit the Active tab to manage them. If you expected a candidate that\'s missing, check the Diagnostics tab.',
                    'wpconvert-cpt'
                ) . '</p>';
            } else {
                echo '<p><strong>' . esc_html__( 'No CPT candidates are waiting for activation.', 'wpconvert-cpt' ) . '</strong></p>';
                if ( $wc_intent_count > 0 ) {
                    echo '<p>' . sprintf(
                        /* translators: %d: count of WooCommerce-intent product catalogs */
                        esc_html( _n(
                            '%d detected candidate is a product catalog — it imports into WooCommerce instead of activating as a CPT. Look for the WPConvert WooCommerce notice on the Dashboard.',
                            '%d detected candidates are product catalogs — they import into WooCommerce instead of activating as CPTs. Look for the WPConvert WooCommerce notice on the Dashboard.',
                            $wc_intent_count,
                            'wpconvert-cpt'
                        ) ),
                        (int) $wc_intent_count
                    ) . '</p>';
                }
                echo '<p>' . esc_html__(
                    'If you expected a candidate that\'s missing, check the Diagnostics tab.',
                    'wpconvert-cpt'
                ) . '</p>';
            }
        }
        // Ship 4c.7 / B8 — when there are no activatable candidates but
        // some were hidden by the orphan guard, point the user at
        // Diagnostics rather than leave them wondering "but the JSON
        // detector clearly found them — where are they?". This is the
        // case for chart-data arrays in source files like
        // src/components/charts/*.tsx — they're real arrays but the
        // theme renders them as JS-driven SVG, not loop-swappable HTML.
        if ( ! empty( $hidden_orphans ) ) {
            echo '<p style="margin-top:1em;color:#646970;font-size:0.9em;">' . sprintf(
                /* translators: %d: count of detected-but-unrenderable candidates */
                esc_html( _n(
                    '%d candidate hidden — detected in source but no front-end template, so activating would silently no-op. Visit Diagnostics for details.',
                    '%d candidates hidden — detected in source but no front-end template, so activating would silently no-op. Visit Diagnostics for details.',
                    count( $hidden_orphans ),
                    'wpconvert-cpt'
                ) ),
                (int) count( $hidden_orphans )
            ) . '</p>';
        }
        echo '</div>';
        return;
    }

    $nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_bulk' ) : '';
    // Ship 4c.7 / B7 — detect ACF once per render so the column header,
    // per-row checkbox, and JS conditional all share the same answer.
    // Parity with the original admin-notice flow which already supports
    // ACF opt-in; the dashboard's Pending tab was the missing piece the
    // user surfaced on May 19 2026.
    $acf_available = wpconvert_cpt_acf_available();
    // EC-CPT-008 — same detection for the Meta Box column.
    $metabox_available = wpconvert_cpt_metabox_available();
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="wpc_cpt_bulk_nonce" value="' . esc_attr( $nonce ) . '" />';
    echo '<input type="hidden" name="wpc_cpt_bulk_tab" value="pending" />';

    echo '<div class="tablenav top"><div class="alignleft actions bulkactions">';
    echo '<select name="wpc_cpt_bulk_action" id="bulk-action-pending">';
    echo '<option value="">' . esc_html__( 'Bulk actions', 'wpconvert-cpt' ) . '</option>';
    echo '<option value="activate">' . esc_html__( 'Activate + import', 'wpconvert-cpt' ) . '</option>';
    echo '</select>';
    echo '<input type="submit" class="button action" value="' . esc_attr__( 'Apply', 'wpconvert-cpt' ) . '" />';
    echo '</div></div>';

    echo '<table class="wp-list-table widefat fixed striped wpc-cpt-pending-table">';
    echo '<thead><tr>';
    echo '<td class="manage-column column-cb check-column"><input type="checkbox" id="wpc-cpt-pending-cb-all" /></td>';
    echo '<th>' . esc_html__( 'Detected Array', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Source File', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Items', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'Fields', 'wpconvert-cpt' ) . '</th>';
    echo '<th>' . esc_html__( 'CPT Slug (editable)', 'wpconvert-cpt' ) . '</th>';
    if ( $acf_available ) {
        echo '<th title="' . esc_attr__( 'When checked, an ACF field group is auto-created on activation and ACF\'s UI replaces the auto-generated meta box.', 'wpconvert-cpt' ) . '">'
            . esc_html__( 'ACF', 'wpconvert-cpt' ) . '</th>';
    }
    // EC-CPT-008 — parallel Meta Box column header.
    if ( $metabox_available ) {
        echo '<th title="' . esc_attr__( 'When checked, a Meta Box field group is auto-created on activation and MB Builder\'s UI replaces the auto-generated meta box.', 'wpconvert-cpt' ) . '">'
            . esc_html__( 'Meta Box', 'wpconvert-cpt' ) . '</th>';
    }
    echo '<th>' . esc_html__( 'Actions', 'wpconvert-cpt' ) . '</th>';
    echo '</tr></thead><tbody>';

    // Ship 4c.7 / B10 — Pre-compute base-slug counts so we can disambiguate
    // colliding suggestions across rows. Without this pass, a manifest
    // emitted by a site with 14 `faqs` arrays (one per page) shows 14
    // rows all suggesting slug "faq" — the first activation succeeds,
    // every subsequent one 409s with post-type-collision until the user
    // edits each row's slug by hand. Mirrors the disambiguation the
    // admin notice already runs (line ~5105) so the two surfaces agree.
    //
    // We use wpconvert_cpt_suggest_labels() here (not the simpler
    // wpconvert_cpt_suggest_slug_for) so we get the SINGULARIZED slug
    // — that matches the admin notice AND follows the WordPress
    // convention of singular CPT slugs (`book`, `event`, `staff_member`).
    // Pre-B10 these two surfaces drifted: notice suggested `faq`,
    // Pending tab suggested `faqs`. Now they agree.
    $base_slug_counts_pending = array();
    foreach ( $pending as $c ) {
        $sug = wpconvert_cpt_suggest_labels( (string) ( $c['source_array'] ?? 'content_item' ) );
        $base = $sug['slug'];
        $base_slug_counts_pending[ $base ] = ( $base_slug_counts_pending[ $base ] ?? 0 ) + 1;
    }

    foreach ( $pending as $c ) {
        $sk = (string) ( $c['section_key'] ?? '' );
        $name = (string) ( $c['source_array'] ?? '' );
        $file = (string) ( $c['source_file'] ?? '' );
        $items = (int) ( $c['item_count'] ?? 0 );
        $fields = is_array( $c['fields'] ?? null ) ? count( $c['fields'] ) : 0;
        $suggestion = wpconvert_cpt_suggest_labels( $name );
        $base_slug = $suggestion['slug'];
        $suggested = $base_slug;

        // Ship 4c.7 / B10 — disambiguate when 2+ pending candidates would
        // suggest the same base slug. Append the file-context token (the
        // page name minus extension and noise tokens) so e.g.
        // CombiliftTraining → faq_combilift, HoistTraining → faq_hoist.
        if ( ( $base_slug_counts_pending[ $base_slug ] ?? 0 ) > 1 ) {
            $file_tok = wpconvert_cpt_file_context_token( $file );
            if ( $file_tok !== '' && $file_tok !== 'x' ) {
                $suggested = $base_slug . '_' . $file_tok;
                // Honour the 20-char slug limit. If the page name is so
                // long that base_slug + _ + file_tok would overflow,
                // fall back to base + short hash of the file path —
                // ugly but unique, and the user can edit it.
                if ( strlen( $suggested ) > 20 ) {
                    $suggested = substr( $base_slug, 0, 13 ) . '_'
                        . substr( sha1( $file ), 0, 6 );
                }
            }
        }

        // Ship 4c.7 / B1 — pre-flight the suggested slug so we can show
        // a warning if the auto-pick already collides with an existing
        // page (the common services / about / contact case).
        $pre_check = wpconvert_cpt_validate_slug( $suggested );
        $initial_slug = $pre_check['ok'] ? $suggested
            : ( isset( $pre_check['suggestion'] ) && $pre_check['suggestion'] !== ''
                ? $pre_check['suggestion'] : $suggested );

        echo '<tr data-section-key="' . esc_attr( $sk ) . '">';
        echo '<th scope="row" class="check-column"><input type="checkbox" name="wpc_cpt_bulk_keys[]" value="' . esc_attr( $sk ) . '" /></th>';
        echo '<td><strong>' . esc_html( $name ) . '</strong></td>';
        echo '<td><code>' . esc_html( $file ) . '</code></td>';
        echo '<td>' . esc_html( (string) $items ) . '</td>';
        echo '<td>' . esc_html( (string) $fields ) . '</td>';
        // Editable slug input. Name shape `wpc_cpt_slug_overrides[<section_key>]`
        // so the bulk-action POST receives a section_key → user_slug map.
        echo '<td class="wpc-cpt-slug-cell">';
        echo '<input type="text" class="wpc-cpt-slug-input regular-text" '
            . 'name="wpc_cpt_slug_overrides[' . esc_attr( $sk ) . ']" '
            . 'value="' . esc_attr( $initial_slug ) . '" '
            . 'data-original-suggested="' . esc_attr( $suggested ) . '" '
            . 'data-sk="' . esc_attr( $sk ) . '" '
            . 'maxlength="20" '
            . 'pattern="[a-z][a-z0-9_]{0,19}" '
            . 'aria-describedby="wpc-cpt-slug-help-' . esc_attr( $sk ) . '" '
            . 'style="width:14em;" />';
        echo '<p class="description wpc-cpt-slug-help" id="wpc-cpt-slug-help-' . esc_attr( $sk ) . '" '
            . 'style="margin:.25em 0 0;font-size:11px;color:#646970;">';
        if ( ! $pre_check['ok'] ) {
            echo '<span class="wpc-cpt-slug-warning" style="color:#b32d2e;">'
                . esc_html__( 'Suggested slug collides — pre-filled with safe alternative.', 'wpconvert-cpt' )
                . '</span>';
        } else {
            echo esc_html__( 'Lowercase a-z, 0-9, underscore. Max 20 chars.', 'wpconvert-cpt' );
        }
        echo '</p>';
        echo '</td>';
        // Ship 4c.7 / B7 — per-row ACF opt-in column. Renders only when
        // ACF is detected. The checkbox name participates in the bulk-
        // action POST as a map keyed by section_key (parallel to the
        // slug-overrides map). The per-row "Activate" JS reads the same
        // checkbox via DOM and forwards the value over AJAX.
        if ( $acf_available ) {
            echo '<td class="wpc-cpt-acf-cell">';
            echo '<label style="display:inline-flex;align-items:center;gap:0.4em;font-weight:normal;cursor:pointer;">'
                . '<input type="checkbox" class="wpc-cpt-acf-managed-row" '
                . 'name="wpc_cpt_acf_managed[' . esc_attr( $sk ) . ']" '
                . 'data-sk="' . esc_attr( $sk ) . '" '
                . 'value="1" />'
                . '<span style="font-size:0.85em;color:#646970;">' . esc_html__( 'Manage with ACF', 'wpconvert-cpt' ) . '</span>'
                . '</label>';
            echo '</td>';
        }
        // EC-CPT-008 — parallel per-row Meta Box opt-in column.
        if ( $metabox_available ) {
            echo '<td class="wpc-cpt-metabox-cell">';
            echo '<label style="display:inline-flex;align-items:center;gap:0.4em;font-weight:normal;cursor:pointer;">'
                . '<input type="checkbox" class="wpc-cpt-metabox-managed-row" '
                . 'name="wpc_cpt_metabox_managed[' . esc_attr( $sk ) . ']" '
                . 'data-sk="' . esc_attr( $sk ) . '" '
                . 'value="1" />'
                . '<span style="font-size:0.85em;color:#646970;">' . esc_html__( 'Manage with Meta Box', 'wpconvert-cpt' ) . '</span>'
                . '</label>';
            echo '</td>';
        }
        echo '<td>';
        echo '<button type="button" class="button button-primary button-small wpc-cpt-activate-row-btn" '
            . 'data-sk="' . esc_attr( $sk ) . '" '
            . 'data-singular="' . esc_attr( ucfirst( $name ) ) . '" '
            . 'data-plural="' . esc_attr( ucfirst( $name ) ) . '">'
            . esc_html__( 'Activate', 'wpconvert-cpt' ) . '</button>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</form>';

    // Ship 4c.7 / B8 — explain why the user might see fewer rows here
    // than the JS detector emitted in their manifest. The user reported
    // (May 19 2026) confusion when the notice showed 2 but the pending
    // tab showed 4: that was actually a bug (Pending tab was missing
    // the orphan guard) but now they're consistent, AND we surface the
    // hidden orphan count so the user knows the difference between
    // "nothing was detected" and "things were detected but can't be
    // rendered by the converted theme".
    if ( ! empty( $hidden_orphans ) ) {
        echo '<p class="description wpc-cpt-pending-hidden-note" style="margin-top:1em;color:#646970;font-size:0.9em;">';
        echo sprintf(
            /* translators: %d: number of detected-but-unrenderable candidates */
            esc_html( _n(
                '%d additional candidate was detected in your source but has no front-end template — see the Diagnostics tab for the list. Chart data, decorative SVG, and JS-only rendered arrays show up here.',
                '%d additional candidates were detected in your source but have no front-end template — see the Diagnostics tab for the list. Chart data, decorative SVG, and JS-only rendered arrays show up here.',
                count( $hidden_orphans ),
                'wpconvert-cpt'
            ) ),
            (int) count( $hidden_orphans )
        );
        echo '</p>';
    }

    $ajax_url = function_exists( 'admin_url' ) ? admin_url( 'admin-ajax.php' ) : '/wp-admin/admin-ajax.php';
    $row_nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_nonce' ) : '';
    ?>
    <script>
    (function(){
      var ajaxUrl = <?php echo wp_json_encode( $ajax_url ); ?>;
      var nonce = <?php echo wp_json_encode( $row_nonce ); ?>;
      var selAll = document.getElementById('wpc-cpt-pending-cb-all');
      if (selAll) selAll.addEventListener('change', function(e){
        document.querySelectorAll('input[name="wpc_cpt_bulk_keys[]"]').forEach(function(cb){ cb.checked = e.target.checked; });
      });

      // Ship 4c.7 / B1 — read the slug from the row's editable input
      // instead of a button data-attr. This is the user-edited value
      // that the bulk-action POST also carries.
      function getSlugFromRow(btn) {
        var tr = btn.closest('tr');
        if (!tr) return '';
        var input = tr.querySelector('.wpc-cpt-slug-input');
        return input ? input.value.trim() : '';
      }

      function setHelpMessage(input, text, isError) {
        var help = document.getElementById('wpc-cpt-slug-help-' + input.getAttribute('data-sk'));
        if (!help) return;
        help.innerHTML = '';
        var span = document.createElement('span');
        if (isError) span.style.color = '#b32d2e';
        span.textContent = text;
        help.appendChild(span);
        input.style.borderColor = isError ? '#b32d2e' : '';
      }

      // Live slug check on debounced input change. Hits the
      // wp_ajax_wpconvert_cpt_check_slug endpoint and updates the help
      // text + border color so the user sees collisions BEFORE clicking
      // Activate. Critical for the page-slug-hijack case (e.g.
      // `/services` collides with page-services.php).
      var checkTimers = {};
      function liveCheck(input) {
        var sk = input.getAttribute('data-sk');
        clearTimeout(checkTimers[sk]);
        checkTimers[sk] = setTimeout(function(){
          var val = input.value.trim();
          if (!val) {
            setHelpMessage(input, 'Slug is required.', true);
            return;
          }
          var fd = new FormData();
          fd.append('action', 'wpconvert_cpt_check_slug');
          fd.append('nonce', nonce);
          fd.append('slug', val);
          fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(r){
              if (!r || !r.success || !r.data) return;
              var d = r.data;
              if (d.ok) {
                setHelpMessage(input, 'OK — will register as "' + (d.normalized || val) + '".', false);
              } else {
                var msg = (d.message || 'Slug collision.');
                if (d.suggestion) msg += ' Try "' + d.suggestion + '".';
                setHelpMessage(input, msg, true);
              }
            })
            .catch(function(){ /* swallow — UI just stays neutral */ });
        }, 250);
      }
      document.querySelectorAll('.wpc-cpt-slug-input').forEach(function(input){
        input.addEventListener('input', function(){ liveCheck(input); });
        input.addEventListener('blur', function(){ liveCheck(input); });
      });

      function getAcfManagedFromRow(btn) {
        var tr = btn.closest('tr');
        if (!tr) return '';
        var box = tr.querySelector('.wpc-cpt-acf-managed-row');
        // Ship 4c.7 / B7 — only checked + present means opt-in. Missing
        // checkbox (ACF not installed) yields '' which the AJAX endpoint
        // treats as opt-out.
        return (box && box.checked) ? '1' : '';
      }
      // EC-CPT-008 — parallel reader for the Meta Box per-row checkbox.
      function getMetaboxManagedFromRow(btn) {
        var tr = btn.closest('tr');
        if (!tr) return '';
        var box = tr.querySelector('.wpc-cpt-metabox-managed-row');
        return (box && box.checked) ? '1' : '';
      }
      // EC-CPT-008 — mutual exclusion: checking one editor unchecks the other.
      document.querySelectorAll('tr').forEach(function(tr){
        var acf = tr.querySelector('.wpc-cpt-acf-managed-row');
        var mb = tr.querySelector('.wpc-cpt-metabox-managed-row');
        if (!acf || !mb) return;
        acf.addEventListener('change', function(){ if (acf.checked) mb.checked = false; });
        mb.addEventListener('change', function(){ if (mb.checked) acf.checked = false; });
      });
      function activateOnce(sk, slug, singular, plural, retryWithPrefix, acfManaged, metaboxManaged) {
        var fd = new FormData();
        fd.append('action', 'wpconvert_cpt_activate_and_import');
        fd.append('nonce', nonce);
        fd.append('section_key', sk);
        fd.append('post_type', slug);
        fd.append('singular', singular);
        fd.append('plural', plural);
        if (acfManaged) fd.append('acf_managed', acfManaged);
        if (metaboxManaged) fd.append('metabox_managed', metaboxManaged);
        return fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
          .then(function(r){ return r.json(); })
          .then(function(r){
            if (r && r.success) return r;
            var data = r && r.data ? r.data : '';
            // Ship 4c.7 / B1 — server now returns structured payload
            // { error, message, suggestion, code } for slug conflicts.
            // Prefer the structured shape; fall back to the legacy
            // string-error path for unknown errors.
            if (data && typeof data === 'object' && data.error) {
              var altMsg = data.message || ('Slug "' + slug + '" is not available.');
              if (data.suggestion && retryWithPrefix !== false) {
                if (window.confirm(altMsg + '\n\nRetry with "' + data.suggestion + '" instead?')) {
                  return activateOnce(sk, data.suggestion, singular, plural, false, acfManaged, metaboxManaged);
                }
              } else {
                window.alert(altMsg);
              }
              return r;
            }
            if (typeof data === 'string' && /slug-(reserved|exists|conflict|page-exists|post-type-exists|invalid)/.test(data) && retryWithPrefix !== false) {
              var alt = 'wpc_' + slug;
              if (window.confirm('The slug "' + slug + '" is already in use.\n\nRetry with "' + alt + '" instead?')) {
                return activateOnce(sk, alt, singular, plural, false, acfManaged, metaboxManaged);
              }
            }
            window.alert('Activation failed: ' + (typeof data === 'string' ? data : JSON.stringify(data)));
            return r;
          });
      }
      document.querySelectorAll('.wpc-cpt-activate-row-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
          var sk = btn.getAttribute('data-sk');
          var slug = getSlugFromRow(btn);
          var sing = btn.getAttribute('data-singular') || slug;
          var plur = btn.getAttribute('data-plural') || (sing + 's');
          var acfManaged = getAcfManagedFromRow(btn);
          var metaboxManaged = getMetaboxManagedFromRow(btn);
          if (!slug) {
            window.alert('Please enter a slug before activating.');
            return;
          }
          btn.disabled = true;
          activateOnce(sk, slug, sing, plur, true, acfManaged, metaboxManaged).then(function(r){
            if (r && r.success) window.location.reload();
            else btn.disabled = false;
          });
        });
      });
    })();
    </script>
    <?php
}

/**
 * Ship 4c.6 / C1 + C5 + C6 — Diagnostics tab.
 *
 * Read-only support panel. Shows plugin/host versions, manifest path
 * + size, stamp counts, reserved-slug list, the diagnostics log, and
 * the purge-on-uninstall toggle. Copy-bundle button serializes the
 * non-PII subset for support tickets.
 */
function wpconvert_cpt_render_diagnostics_tab() {
    // Handle the purge-flag POST first.
    if ( isset( $_POST['wpc_cpt_purge_toggle'] )
        && function_exists( 'check_admin_referer' )
        && function_exists( 'current_user_can' )
        && current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        check_admin_referer( 'wpc_cpt_diag_nonce' );
        $val = ! empty( $_POST['wpc_cpt_purge'] ) ? 1 : 0;
        if ( function_exists( 'update_option' ) ) {
            update_option( 'wpconvert_cpts_purge_on_uninstall', $val, 'no' );
        }
        echo '<div class="notice notice-success is-dismissible"><p>'
            . esc_html__( 'Uninstall preferences saved.', 'wpconvert-cpt' )
            . '</p></div>';
    }

    $manifest_path = function_exists( 'get_template_directory' )
        ? get_template_directory() . WPCONVERT_CPT_MANIFEST_REL : '';
    $manifest = wpconvert_cpt_get_candidates_manifest();
    $stamped  = function_exists( 'wpconvert_cpt_get_stamped_section_keys' )
        ? wpconvert_cpt_get_stamped_section_keys() : array();
    $log      = function_exists( 'get_option' )
        ? (array) get_option( 'wpconvert_cpts_diagnostics_log', array() ) : array();
    $purge    = function_exists( 'get_option' )
        ? (int) get_option( 'wpconvert_cpts_purge_on_uninstall', 0 ) : 0;
    $installed_at = function_exists( 'get_option' )
        ? (int) get_option( 'wpconvert_cpts_installed_at', 0 ) : 0;
    $active   = wpconvert_cpt_get_active_cpts();
    $reserved = is_serialized( WPCONVERT_CPT_RESERVED_SLUGS ) ? unserialize( WPCONVERT_CPT_RESERVED_SLUGS ) : array();

    echo '<table class="form-table" role="presentation"><tbody>';
    echo '<tr><th scope="row">' . esc_html__( 'Plugin version', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( defined( 'WPCONVERT_CPT_VERSION' ) ? WPCONVERT_CPT_VERSION : 'unknown' ) . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'PHP / WP', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( PHP_VERSION ) . ' / '
        . esc_html( function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'version' ) : 'unknown' )
        . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'Installed at', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( $installed_at ? gmdate( 'Y-m-d H:i:s', $installed_at ) . ' UTC' : '—' ) . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'Manifest path', 'wpconvert-cpt' ) . '</th><td><code>'
        . esc_html( $manifest_path ) . '</code><br/>'
        . esc_html( file_exists( $manifest_path ) ? sprintf( '%d bytes, mtime %s UTC',
            (int) filesize( $manifest_path ),
            gmdate( 'Y-m-d H:i:s', (int) filemtime( $manifest_path ) ) ) : __( 'not found', 'wpconvert-cpt' ) )
        . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'Manifest schema version', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( (string) WPCONVERT_CPT_SCHEMA_VERSION ) . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'Candidates in manifest', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( (string) count( $manifest ) ) . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'Stamped sections in theme', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( (string) count( (array) $stamped ) ) . '</td></tr>';
    echo '<tr><th scope="row">' . esc_html__( 'CPTs activated', 'wpconvert-cpt' ) . '</th><td>'
        . esc_html( (string) count( (array) $active ) ) . '</td></tr>';
    echo '</tbody></table>';

    echo '<h3>' . esc_html__( 'Recent diagnostics', 'wpconvert-cpt' ) . '</h3>';
    if ( empty( $log ) ) {
        echo '<p>' . esc_html__( 'No diagnostic entries.', 'wpconvert-cpt' ) . '</p>';
    } else {
        echo '<table class="widefat striped"><thead><tr><th>'
            . esc_html__( 'Time (UTC)', 'wpconvert-cpt' ) . '</th><th>'
            . esc_html__( 'Code', 'wpconvert-cpt' ) . '</th><th>'
            . esc_html__( 'Detail', 'wpconvert-cpt' ) . '</th></tr></thead><tbody>';
        $tail = array_slice( $log, -20 );
        foreach ( array_reverse( $tail ) as $e ) {
            $ts = (int) ( $e['ts'] ?? 0 );
            $cd = (string) ( $e['code'] ?? '' );
            $dt = (string) ( $e['detail'] ?? '' );
            echo '<tr><td>' . esc_html( $ts ? gmdate( 'Y-m-d H:i:s', $ts ) : '—' )
                . '</td><td><code>' . esc_html( $cd ) . '</code></td><td>' . esc_html( $dt ) . '</td></tr>';
        }
        echo '</tbody></table>';
    }

    echo '<h3>' . esc_html__( 'Reserved slugs', 'wpconvert-cpt' ) . '</h3>';
    echo '<p class="description">' . esc_html__( 'Slugs we refuse to register because WordPress core or another well-known plugin owns them.', 'wpconvert-cpt' ) . '</p>';
    echo '<p><code>' . esc_html( implode( ', ', (array) $reserved ) ) . '</code></p>';

    // Purge-on-uninstall toggle.
    $diag_nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpc_cpt_diag_nonce' ) : '';
    echo '<h3>' . esc_html__( 'Uninstall behavior', 'wpconvert-cpt' ) . '</h3>';
    echo '<form method="post">';
    echo '<input type="hidden" name="_wpnonce" value="' . esc_attr( $diag_nonce ) . '" />';
    echo '<input type="hidden" name="wpc_cpt_purge_toggle" value="1" />';
    echo '<label><input type="checkbox" name="wpc_cpt_purge" value="1" ' . checked( $purge, 1, false ) . ' /> '
        . esc_html__( 'Purge all plugin options + transients when uninstalling. (CPT posts are NEVER deleted by uninstall, even with this on.)', 'wpconvert-cpt' )
        . '</label>';
    echo '<p><input type="submit" class="button" value="' . esc_attr__( 'Save', 'wpconvert-cpt' ) . '" /></p>';
    echo '</form>';
}

/**
 * Ship 4c.6 / C2 + C3 — handle the bulk-action POST.
 *
 * Returns an associative array with `message` + optional `error`
 * keys, or null when no bulk action was submitted. The result is
 * rendered as an admin notice at the top of the dashboard.
 *
 * @return array|null
 */
function wpconvert_cpt_handle_bulk_action_post() {
    if ( empty( $_POST['wpc_cpt_bulk_action'] ) ) return null;
    if ( ! function_exists( 'wp_verify_nonce' ) ) return null;
    $nonce = isset( $_POST['wpc_cpt_bulk_nonce'] ) ? (string) wp_unslash( $_POST['wpc_cpt_bulk_nonce'] ) : '';
    if ( ! wp_verify_nonce( $nonce, 'wpconvert_cpt_bulk' ) ) {
        return array( 'message' => __( 'Security check failed.', 'wpconvert-cpt' ), 'error' => true );
    }
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        return array( 'message' => __( 'Permission denied.', 'wpconvert-cpt' ), 'error' => true );
    }
    $action = sanitize_text_field( wp_unslash( $_POST['wpc_cpt_bulk_action'] ) );
    $keys   = isset( $_POST['wpc_cpt_bulk_keys'] ) ? (array) $_POST['wpc_cpt_bulk_keys'] : array();
    $keys   = array_values( array_filter( array_map( function( $k ) {
        return preg_match( '/^[a-f0-9]{40}$/', (string) $k ) ? (string) $k : '';
    }, $keys ) ) );

    if ( empty( $action ) || empty( $keys ) ) {
        return array( 'message' => __( 'Select rows + an action.', 'wpconvert-cpt' ), 'error' => true );
    }

    $ok = 0; $fail = 0;
    switch ( $action ) {
        case 'deactivate':
            foreach ( $keys as $sk ) {
                if ( wpconvert_cpt_do_deactivate( $sk ) ) $ok++; else $fail++;
            }
            return array( 'message' => sprintf(
                /* translators: 1: number of CPTs deactivated, 2: number that failed */
                __( 'Deactivated %1$d CPTs (kept posts). %2$d failed.', 'wpconvert-cpt' ),
                $ok, $fail
            ) );

        case 'delete-with-posts':
            foreach ( $keys as $sk ) {
                if ( wpconvert_cpt_do_delete( $sk, 'with-posts' ) ) $ok++; else $fail++;
            }
            return array( 'message' => sprintf(
                __( 'Deleted %1$d CPTs and trashed their posts. %2$d failed.', 'wpconvert-cpt' ),
                $ok, $fail
            ) );

        case 'delete-keep-posts':
            foreach ( $keys as $sk ) {
                if ( wpconvert_cpt_do_delete( $sk, 'keep-posts' ) ) $ok++; else $fail++;
            }
            return array( 'message' => sprintf(
                __( 'Deleted %1$d CPTs (converted posts to standard posts). %2$d failed.', 'wpconvert-cpt' ),
                $ok, $fail
            ) );

        case 'resync':
            $totals = array( 'created' => 0, 'updated' => 0, 'failed' => 0 );
            foreach ( $keys as $sk ) {
                $s = wpconvert_cpt_import_candidate( $sk );
                if ( is_array( $s ) ) {
                    $totals['created'] += (int) ( $s['created'] ?? 0 );
                    $totals['updated'] += (int) ( $s['updated'] ?? 0 );
                    $totals['failed']  += (int) ( $s['failed']  ?? 0 );
                }
            }
            return array( 'message' => sprintf(
                __( 'Re-sync: %1$d created, %2$d updated, %3$d failed.', 'wpconvert-cpt' ),
                $totals['created'], $totals['updated'], $totals['failed']
            ) );

        case 'activate':
            // Ship 4c.7 / B1 — accept a per-row user-edited slug map. The
            // Pending tab now renders each row's slug as an editable
            // <input>, so the bulk POST carries the user's chosen slugs
            // alongside the section_keys. Empty / missing entries fall
            // back to the auto-suggested slug inside do_activate.
            $slug_map = isset( $_POST['wpc_cpt_slug_overrides'] )
                && is_array( $_POST['wpc_cpt_slug_overrides'] )
                ? (array) $_POST['wpc_cpt_slug_overrides']
                : array();
            // Ship 4c.7 / B7 — accept a per-row ACF-managed map (only
            // populated when ACF is loaded AND the user checked the row's
            // "Manage with ACF" box on the Pending tab). Stored as a set
            // of section_keys whose value is "1" — anything else (missing
            // or "0") is treated as opt-out.
            $acf_map = isset( $_POST['wpc_cpt_acf_managed'] )
                && is_array( $_POST['wpc_cpt_acf_managed'] )
                ? (array) $_POST['wpc_cpt_acf_managed']
                : array();
            // EC-CPT-008 — parallel per-row Meta Box opt-in map.
            $metabox_map = isset( $_POST['wpc_cpt_metabox_managed'] )
                && is_array( $_POST['wpc_cpt_metabox_managed'] )
                ? (array) $_POST['wpc_cpt_metabox_managed']
                : array();
            $blocked = array();
            foreach ( $keys as $sk ) {
                $override = isset( $slug_map[ $sk ] ) ? (string) wp_unslash( $slug_map[ $sk ] ) : '';
                $acf_managed = ! empty( $acf_map[ $sk ] );
                $metabox_managed = ! empty( $metabox_map[ $sk ] );
                if ( wpconvert_cpt_do_activate_from_manifest( $sk, $override, $acf_managed, $metabox_managed ) ) {
                    $ok++;
                } else {
                    $fail++;
                    // Surface the structured conflict reason so the
                    // dashboard notice can tell the user WHY each row
                    // failed (page-exists vs reserved vs invalid).
                    $check_slug = $override !== '' ? wpconvert_cpt_suggest_slug_for( $override ) : '';
                    if ( $check_slug !== '' ) {
                        $v = wpconvert_cpt_validate_slug( $check_slug );
                        if ( ! $v['ok'] ) {
                            $blocked[] = sprintf( '%s: %s (try "%s")',
                                $check_slug, $v['message'], $v['suggestion'] );
                        }
                    }
                }
            }
            $msg = sprintf(
                __( 'Activated %1$d CPTs. %2$d failed (slug conflicts or orphan stamps).', 'wpconvert-cpt' ),
                $ok, $fail
            );
            if ( ! empty( $blocked ) ) {
                $msg .= ' ' . implode( ' | ', array_slice( $blocked, 0, 3 ) );
            }
            return array( 'message' => $msg, 'error' => $fail > 0 && $ok === 0 );
    }
    return array( 'message' => __( 'Unknown action.', 'wpconvert-cpt' ), 'error' => true );
}

/**
 * Ship 4c.6 / C2 — deactivate a CPT. Removes the option entry only.
 * Posts remain in the database (orphan post type). Re-activation
 * restores normal behavior.
 *
 * @param string $section_key
 * @return bool
 */
function wpconvert_cpt_do_deactivate( $section_key ) {
    if ( ! preg_match( '/^[a-f0-9]{40}$/', (string) $section_key ) ) return false;
    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || ! isset( $active[ $section_key ] ) ) return false;
        unset( $active[ $section_key ] );
        if ( function_exists( 'update_option' ) ) {
            update_option( 'wpconvert_cpts', $active, 'no' );
            update_option( 'wpconvert_cpts_needs_flush', 1, 'no' );
        }
        return true;
    } catch ( \Throwable $e ) {
        return false;
    }
}

/**
 * Ship 4c.6 / C3 — delete a CPT. Two modes:
 *
 *   'with-posts'  trashes (not permanently deletes) all imported
 *                 posts of this CPT, then removes the option entry.
 *   'keep-posts'  reassigns each imported post's post_type to 'post'
 *                 (preserving all post meta), then removes the option
 *                 entry. Useful for migrating to standard WP posts.
 *
 * @param string $section_key
 * @param string $mode 'with-posts' | 'keep-posts'
 * @return bool
 */
function wpconvert_cpt_do_delete( $section_key, $mode ) {
    if ( ! preg_match( '/^[a-f0-9]{40}$/', (string) $section_key ) ) return false;
    if ( ! in_array( $mode, array( 'with-posts', 'keep-posts' ), true ) ) return false;
    try {
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || ! isset( $active[ $section_key ] ) ) return false;
        $cfg = $active[ $section_key ];
        $slug = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
        if ( $slug === '' ) return false;

        if ( function_exists( 'get_posts' ) ) {
            $pids = get_posts( array(
                'post_type'   => $slug,
                'post_status' => 'any',
                'fields'      => 'ids',
                'numberposts' => -1,
            ) );
            if ( is_array( $pids ) ) {
                foreach ( $pids as $pid ) {
                    if ( $mode === 'with-posts' ) {
                        if ( function_exists( 'wp_trash_post' ) ) {
                            wp_trash_post( (int) $pid );
                        }
                    } else { // keep-posts
                        if ( function_exists( 'wp_update_post' ) ) {
                            wp_update_post( array( 'ID' => (int) $pid, 'post_type' => 'post' ) );
                        }
                    }
                }
            }
        }

        unset( $active[ $section_key ] );
        if ( function_exists( 'update_option' ) ) {
            update_option( 'wpconvert_cpts', $active, 'no' );
            update_option( 'wpconvert_cpts_needs_flush', 1, 'no' );
        }
        return true;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt do_delete: ' . $e->getMessage() );
        }
        return false;
    }
}

/**
 * Ship 4c.6 / C2 — bulk-activate from manifest. Uses sensible defaults
 * (slug suggestion from the array name; no ACF). For finer control,
 * users use the per-row inline form.
 *
 * @param string $section_key
 * @return bool
 */
function wpconvert_cpt_do_activate_from_manifest( $section_key, $slug_override = '', $acf_managed = false, $metabox_managed = false ) {
    if ( ! preg_match( '/^[a-f0-9]{40}$/', (string) $section_key ) ) return false;
    try {
        $manifest = wpconvert_cpt_get_candidates_manifest();
        $candidate = null;
        foreach ( $manifest as $c ) {
            if ( isset( $c['section_key'] ) && $c['section_key'] === $section_key ) {
                $candidate = $c;
                break;
            }
        }
        if ( ! is_array( $candidate ) ) return false;
        $name = (string) ( $candidate['source_array'] ?? '' );

        // Ship 4c.7 / B1 — accept a user-supplied slug override from the
        // Pending tab. Empty / invalid override falls back to the auto-
        // suggested slug. This is the same path the bulk-action POST uses.
        $slug_override = is_string( $slug_override ) ? trim( $slug_override ) : '';
        $slug = $slug_override !== ''
            ? wpconvert_cpt_suggest_slug_for( $slug_override )
            : wpconvert_cpt_suggest_slug_for( $name );
        if ( $slug === '' ) return false;

        // Ship 4c.7 / B7 — honor the per-row ACF opt-in from the Pending
        // tab (parity with the original admin-notice flow which already
        // supported this via the AJAX path). The flag is only effective
        // when ACF is actually loaded; otherwise the caller's intent is
        // silently downgraded so a half-installed ACF can't break the
        // activation midway.
        $acf_managed = (bool) $acf_managed && wpconvert_cpt_acf_available();

        // EC-CPT-008 — same for the per-row Meta Box opt-in. Downgrade when
        // Meta Box isn't loaded; ACF wins if both somehow arrive for one row.
        $metabox_managed = (bool) $metabox_managed && wpconvert_cpt_metabox_available();
        if ( $metabox_managed && $acf_managed ) {
            $metabox_managed = false;
            if ( function_exists( 'error_log' ) ) {
                error_log( 'wpconvert_cpt: both acf_managed and metabox_managed for "' . $slug . '" — ACF wins, Meta Box dropped.' );
            }
        }

        // Ship 4c.7 / B1 — block activation when the slug is invalid,
        // reserved, owned by another post type, or owned by an existing
        // page/post. Previously we silently `wpc_`-prefixed and moved on,
        // which masked the URL-hijack case (page-exists). Now we hard-fail
        // and surface a structured reason so the UI can prompt the user.
        $validation = wpconvert_cpt_validate_slug( $slug );
        if ( ! $validation['ok'] ) {
            // Auto-rename ONLY in the legacy reserved-or-post-type-exists
            // path AND only when the caller didn't override. Page-exists
            // never auto-renames — it always requires explicit user intent
            // because the wrong rename can break navigation/SEO.
            if ( $slug_override === ''
                && in_array( $validation['reason'], array( 'reserved', 'post-type-exists' ), true ) ) {
                $slug = $validation['suggestion'];
            } else {
                wpconvert_cpt_log_diagnostic(
                    'activation_blocked_slug_conflict',
                    sprintf( '%s reason=%s suggestion=%s',
                        $slug, $validation['reason'], $validation['suggestion'] )
                );
                return false;
            }
        }

        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) ) $active = array();
        if ( isset( $active[ $section_key ] ) ) return false; // already active

        $singular = ucfirst( $name );
        $plural   = ucfirst( $name );
        $cfg = array(
            'enabled'      => true,
            'post_type'    => $slug,
            'singular'     => $singular,
            'plural'       => $plural,
            'public'       => true,
            'has_archive'  => true,
            'show_in_rest' => true,
            'activated_at' => time(),
            'acf_managed'  => $acf_managed,
            'metabox_managed' => $metabox_managed,
        );
        if ( ! empty( $candidate['fields'] ) && is_array( $candidate['fields'] ) ) {
            $cfg['fields'] = array();
            foreach ( $candidate['fields'] as $f ) {
                if ( empty( $f['key'] ) ) continue;
                $trim = array(
                    'key'         => (string) $f['key'],
                    'type'        => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                    'remapped_to' => isset( $f['remapped_to'] ) ? (string) $f['remapped_to'] : (string) $f['key'],
                );
                if ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) {
                    $trim['enum'] = array_values( array_slice( $f['enum'], 0, 50 ) );
                }
                $cfg['fields'][] = $trim;
            }
        }
        $active[ $section_key ] = $cfg;
        if ( function_exists( 'update_option' ) ) {
            update_option( 'wpconvert_cpts', $active, 'no' );
            update_option( 'wpconvert_cpts_needs_flush', 1, 'no' );
        }
        wpconvert_cpt_register_active_post_types();
        // Ship 4c.7 / B7 — when activating ACF-managed from the Pending
        // tab, create the field group up-front (matches the AJAX path
        // behavior). The import that follows will dual-write to ACF meta
        // keys so the user immediately sees their data in ACF's UI.
        if ( $acf_managed ) {
            wpconvert_cpt_create_acf_group_for_cpt( $cfg, $section_key );
        }
        // EC-CPT-008 — same for Meta Box.
        if ( $metabox_managed ) {
            wpconvert_cpt_create_metabox_group_for_cpt( $cfg, $section_key );
        }
        wpconvert_cpt_import_candidate( $section_key );
        return true;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt do_activate_from_manifest: ' . $e->getMessage() );
        }
        return false;
    }
}

/**
 * Ship 4c.6 / C2 + C6 — suggest a CPT slug from a source array name.
 * Lowercases, normalizes to alphanumeric + underscore, caps at 20.
 *
 * @param string $name
 * @return string
 */
function wpconvert_cpt_suggest_slug_for( $name ) {
    // camelCase / PascalCase → snake_case (must happen BEFORE lowercase,
    // otherwise the case boundary information is lost).
    $s = preg_replace( '/([a-z\d])([A-Z])/', '$1_$2', (string) $name );
    $s = preg_replace( '/([A-Z]+)([A-Z][a-z])/', '$1_$2', (string) $s );
    $s = strtolower( $s );
    $s = preg_replace( '/[^a-z0-9_]+/', '_', $s );
    $s = trim( $s, '_' );
    if ( strlen( $s ) > 20 ) $s = substr( $s, 0, 20 );
    return $s;
}

/**
 * Ship 4c.6 / C6 — true if `$slug` is in WPCONVERT_CPT_RESERVED_SLUGS.
 *
 * @param string $slug
 * @return bool
 */
function wpconvert_cpt_slug_is_reserved( $slug ) {
    $reserved = is_serialized( WPCONVERT_CPT_RESERVED_SLUGS )
        ? unserialize( WPCONVERT_CPT_RESERVED_SLUGS )
        : array();
    return in_array( (string) $slug, (array) $reserved, true );
}

/**
 * Ship 4c.6 / C6 — best-effort introspection: which plugin/theme
 * owns a given post type? Falls back to "another plugin" when WP
 * doesn't track that.
 *
 * Ship 4c.7 / B1 — also detects a page or post with `post_name === $slug`.
 * Without this the activation flow silently hijacks the URL of an
 * existing WP page (e.g. activating a `services` CPT with
 * `has_archive => true` steals `/services` away from
 * `page-services.php`). The CPT archive rewrite rule wins over the
 * page rewrite rule, so users see a blank-looking archive listing
 * instead of their page template.
 *
 * @param string $slug
 * @return string  Human-readable owner string for UI display.
 */
function wpconvert_cpt_detect_slug_owner( $slug ) {
    if ( wpconvert_cpt_slug_is_reserved( $slug ) ) {
        return 'WordPress core or a reserved system slug';
    }
    if ( function_exists( 'get_post_type_object' ) ) {
        $obj = get_post_type_object( $slug );
        if ( is_object( $obj ) ) {
            if ( ! empty( $obj->label ) ) return (string) $obj->label;
            return 'another plugin or theme';
        }
    }
    // Ship 4c.7 / B1 — page/post slug collision is the more common
    // real-world failure mode. CPT archive rewrite rules clobber page
    // permalinks of the same name; check `wp_posts` directly.
    $page_owner = wpconvert_cpt_find_page_owner_for_slug( $slug );
    if ( $page_owner !== '' ) return $page_owner;
    return 'another plugin or theme';
}

/**
 * Ship 4c.7 / B1 — query `wp_posts` for a page/post whose `post_name`
 * matches `$slug`. Returns a human-readable owner string, or empty
 * string when no collision exists.
 *
 * Why not `get_page_by_path()`? That resolves hierarchical paths and
 * is overkill — we want a flat slug match against pages and posts
 * (the only built-in types whose permalink is `/{slug}/`). Custom
 * permalink structures could in theory put `/{slug}/` on a post, so
 * we check `post` too, but the dominant case is pages.
 *
 * @param string $slug
 * @return string
 */
function wpconvert_cpt_find_page_owner_for_slug( $slug ) {
    $slug = (string) $slug;
    if ( $slug === '' ) return '';
    if ( ! isset( $GLOBALS['wpdb'] ) ) return '';
    $wpdb = $GLOBALS['wpdb'];
    if ( ! is_object( $wpdb ) || ! method_exists( $wpdb, 'prepare' ) || ! method_exists( $wpdb, 'get_row' ) ) {
        return '';
    }
    $table = isset( $wpdb->posts ) ? $wpdb->posts : 'wp_posts';
    $sql = $wpdb->prepare(
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        "SELECT post_title, post_type, post_status FROM {$table}"
        . " WHERE post_name = %s AND post_type IN ('page','post')"
        . " AND post_status IN ('publish','draft','private','future','pending')"
        . ' LIMIT 1',
        $slug
    );
    $row = $wpdb->get_row( $sql );
    if ( ! $row ) return '';
    $title = isset( $row->post_title ) ? (string) $row->post_title : $slug;
    $type  = isset( $row->post_type ) ? (string) $row->post_type : 'page';
    return sprintf(
        'an existing WordPress %s ("%s")',
        $type === 'post' ? 'post' : 'page',
        $title
    );
}

/**
 * Ship 4c.7 / B1 — propose an alternate slug when the requested one
 * collides with another owner. Strategies, in order:
 *   1) Append `_cpt` (preserves user intent).
 *   2) Add `wpc_` prefix (matches the existing JS fallback).
 *   3) Append `_1`, `_2`, ... until free (last resort).
 *
 * @param string $slug
 * @return string
 */
function wpconvert_cpt_suggest_alternate_slug( $slug ) {
    $slug = (string) $slug;
    if ( $slug === '' ) return 'wpc_cpt';
    // `wpc_` prefix first — it's the alternate the C6 conflict UX has
    // shipped with since Ship 4c.6 and the one all existing tests pin.
    // `_cpt` suffix is the secondary fallback for the page-collision
    // case where users want to keep the meaningful prefix (e.g.
    // `services` colliding with the page → `services_cpt`).
    $candidates = array(
        'wpc_' . $slug,
        $slug . '_cpt',
    );
    foreach ( $candidates as $alt ) {
        if ( strlen( $alt ) > 20 ) $alt = substr( $alt, 0, 20 );
        if ( $alt === '' ) continue;
        if ( wpconvert_cpt_slug_is_reserved( $alt ) ) continue;
        if ( function_exists( 'post_type_exists' ) && post_type_exists( $alt )
            && ! wpconvert_cpt_is_our_registered_post_type( $alt ) ) continue;
        if ( wpconvert_cpt_find_page_owner_for_slug( $alt ) !== '' ) continue;
        return $alt;
    }
    for ( $i = 1; $i < 100; $i++ ) {
        $alt = $slug . '_' . $i;
        if ( strlen( $alt ) > 20 ) $alt = substr( $slug, 0, 20 - strlen( '_' . $i ) ) . '_' . $i;
        if ( wpconvert_cpt_slug_is_reserved( $alt ) ) continue;
        if ( function_exists( 'post_type_exists' ) && post_type_exists( $alt )
            && ! wpconvert_cpt_is_our_registered_post_type( $alt ) ) continue;
        if ( wpconvert_cpt_find_page_owner_for_slug( $alt ) !== '' ) continue;
        return $alt;
    }
    return $slug . '_cpt';
}

/**
 * Ship 4c.7 / B1 — single-source-of-truth slug validator used by the
 * activation flow and by the live JS slug-check AJAX endpoint.
 *
 * Returns array shape:
 *   [
 *     'ok'        => bool,
 *     'reason'    => 'invalid' | 'reserved' | 'post-type-exists'
 *                  | 'page-exists' | null,
 *     'message'   => string  (UI-ready, already translated),
 *     'suggestion'=> string  (only present when ok=false),
 *   ]
 *
 * @param string $slug
 * @return array
 */
function wpconvert_cpt_validate_slug( $slug ) {
    $slug = (string) $slug;
    if ( $slug === '' || ! preg_match( '/^[a-z][a-z0-9_]{0,19}$/', $slug ) ) {
        return array(
            'ok'         => false,
            'reason'     => 'invalid',
            'message'    => __(
                'Slug must be 1-20 chars, lowercase a-z, 0-9, or underscore, and must start with a letter.',
                'wpconvert-cpt'
            ),
            'suggestion' => 'wpc_cpt',
        );
    }
    if ( wpconvert_cpt_slug_is_reserved( $slug ) ) {
        return array(
            'ok'         => false,
            'reason'     => 'reserved',
            'message'    => __(
                'That slug is reserved by WordPress core or a well-known plugin.',
                'wpconvert-cpt'
            ),
            'suggestion' => wpconvert_cpt_suggest_alternate_slug( $slug ),
        );
    }
    if ( function_exists( 'post_type_exists' ) && post_type_exists( $slug )
        && ! wpconvert_cpt_is_our_registered_post_type( $slug ) ) {
        return array(
            'ok'         => false,
            'reason'     => 'post-type-exists',
            'message'    => __(
                'A post type with that slug is already registered by another plugin or theme.',
                'wpconvert-cpt'
            ),
            'suggestion' => wpconvert_cpt_suggest_alternate_slug( $slug ),
        );
    }
    $page_owner = wpconvert_cpt_find_page_owner_for_slug( $slug );
    if ( $page_owner !== '' ) {
        return array(
            'ok'         => false,
            'reason'     => 'page-exists',
            'message'    => sprintf(
                /* translators: %s: human-readable page owner ("an existing WordPress page (\"Services\")"). */
                __(
                    'This slug collides with %s. Activating a CPT archive with this slug would hijack the page URL.',
                    'wpconvert-cpt'
                ),
                $page_owner
            ),
            'suggestion' => wpconvert_cpt_suggest_alternate_slug( $slug ),
        );
    }
    return array(
        'ok'      => true,
        'reason'  => null,
        'message' => '',
    );
}

/**
 * Ship 4c.6 / C8 — first-run welcome notice. Renders ONCE per user, then
 * sets the dismissed flag. Critical: this is a STANDARD admin notice
 * (no modal, no overlay), respecting the never-activate user.
 *
 * Conditions for showing:
 *   - Pro/Agency tier + version floor
 *   - Has manage_options
 *   - In wp-admin
 *   - wpconvert_cpts_installed_at IS set (we know they activated the plugin)
 *   - wpconvert_cpts_wizard_dismissed is 0
 *   - At least 1 pending candidate
 *   - Zero active CPTs (otherwise they don't need welcoming)
 */
function wpconvert_cpt_should_show_welcome() {
    if ( ! wpconvert_cpt_should_run() ) return false;
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) return false;
    if ( ! function_exists( 'is_admin' ) || ! is_admin() ) return false;
    if ( ! function_exists( 'get_option' ) ) return false;
    $installed = (int) get_option( 'wpconvert_cpts_installed_at', 0 );
    if ( ! $installed ) return false;
    $dismissed = (int) get_option( 'wpconvert_cpts_wizard_dismissed', 0 );
    if ( $dismissed ) return false;
    try {
        $manifest = wpconvert_cpt_get_candidates_manifest();
        if ( empty( $manifest ) ) return false;
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! empty( $active ) ) return false;
        // At least one non-display, non-orphan pending candidate.
        $stamped = function_exists( 'wpconvert_cpt_get_stamped_section_keys' )
            ? wpconvert_cpt_get_stamped_section_keys() : array();
        foreach ( $manifest as $c ) {
            $sk   = (string) ( $c['section_key'] ?? '' );
            $name = (string) ( $c['source_array'] ?? '' );
            $file = (string) ( $c['source_file'] ?? '' );
            if ( $name !== '' && wpconvert_cpt_is_display_content_array( $name, $file ) ) continue;
            if ( ! empty( $stamped ) && ! isset( $stamped[ $sk ] ) ) continue;
            return true;
        }
    } catch ( \Throwable $e ) {
        return false;
    }
    return false;
}

/**
 * Ship 4c.6 / C8 — render the welcome notice.
 */
function wpconvert_cpt_render_welcome_notice() {
    if ( ! wpconvert_cpt_should_show_welcome() ) return;
    $tools_url = function_exists( 'admin_url' )
        ? admin_url( 'tools.php?page=wpconvert-cpts&tab=pending' )
        : '?page=wpconvert-cpts&tab=pending';
    $nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_nonce' ) : '';
    $ajax_url = function_exists( 'admin_url' ) ? admin_url( 'admin-ajax.php' ) : '/wp-admin/admin-ajax.php';
    $manifest = wpconvert_cpt_get_candidates_manifest();
    $count = is_array( $manifest ) ? count( $manifest ) : 0;
    ?>
    <div class="notice notice-info wpc-cpt-welcome-notice" style="display:flex;justify-content:space-between;align-items:center;">
        <p>
            <strong><?php esc_html_e( 'Welcome to WPConvert CPTs.', 'wpconvert-cpt' ); ?></strong>
            <?php
            echo esc_html( sprintf(
                /* translators: %d: candidate count */
                _n(
                    'We detected %d editable custom post type candidate in your theme.',
                    'We detected %d editable custom post type candidates in your theme.',
                    $count,
                    'wpconvert-cpt'
                ),
                $count
            ) );
            ?>
        </p>
        <p>
            <a class="button button-primary" href="<?php echo esc_url( $tools_url ); ?>">
                <?php esc_html_e( 'Show me my candidates', 'wpconvert-cpt' ); ?>
            </a>
            <button type="button" class="button button-link wpc-cpt-welcome-dismiss">
                <?php esc_html_e( 'Dismiss', 'wpconvert-cpt' ); ?>
            </button>
        </p>
    </div>
    <script>
    (function(){
      var btn = document.querySelector('.wpc-cpt-welcome-dismiss');
      if (!btn) return;
      btn.addEventListener('click', function(){
        var fd = new FormData();
        fd.append('action', 'wpconvert_cpt_welcome_dismiss');
        fd.append('nonce', <?php echo wp_json_encode( $nonce ); ?>);
        fetch(<?php echo wp_json_encode( $ajax_url ); ?>, { method: 'POST', credentials: 'same-origin', body: fd })
          .then(function(r){ return r.json(); })
          .then(function(){ document.querySelector('.wpc-cpt-welcome-notice').style.display = 'none'; });
      });
    })();
    </script>
    <?php
}

/**
 * Ship 4c.6 / C8 — AJAX endpoint: dismiss the welcome notice for this site.
 */
function wpconvert_cpt_ajax_welcome_dismiss() {
    if ( ! wpconvert_cpt_should_run() ) wp_send_json_error( 'tier-or-version', 403 );
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    if ( function_exists( 'update_option' ) ) {
        update_option( 'wpconvert_cpts_wizard_dismissed', 1, 'no' );
    }
    wp_send_json_success( array( 'dismissed' => true ) );
}

/**
 * Ship 4c.6 / C3 — AJAX endpoint: delete a CPT.
 *
 * Required POST: nonce, section_key, mode ('with-posts' | 'keep-posts').
 */
function wpconvert_cpt_ajax_delete() {
    if ( ! wpconvert_cpt_should_run() ) wp_send_json_error( 'tier-or-version', 403 );
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    $sk   = isset( $_POST['section_key'] ) ? (string) wp_unslash( $_POST['section_key'] ) : '';
    $mode = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '';
    if ( ! preg_match( '/^[a-f0-9]{40}$/', $sk ) ) wp_send_json_error( 'bad-section-key', 400 );
    if ( ! in_array( $mode, array( 'with-posts', 'keep-posts' ), true ) ) wp_send_json_error( 'bad-mode', 400 );
    $ok = wpconvert_cpt_do_delete( $sk, $mode );
    if ( ! $ok ) wp_send_json_error( 'delete-failed', 500 );
    wp_send_json_success( array( 'deleted' => true ) );
}

/**
 * Ship 4c.6 / C4 — AJAX endpoint: re-sync a CPT from the manifest.
 * Wraps `wpconvert_cpt_import_candidate` (which is idempotent).
 *
 * Required POST: nonce, section_key.
 */
function wpconvert_cpt_ajax_resync() {
    if ( ! wpconvert_cpt_should_run() ) wp_send_json_error( 'tier-or-version', 403 );
    if ( ! function_exists( 'current_user_can' )
        || ! current_user_can( wpconvert_cpt_dashboard_capability() ) ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    $sk = isset( $_POST['section_key'] ) ? (string) wp_unslash( $_POST['section_key'] ) : '';
    if ( ! preg_match( '/^[a-f0-9]{40}$/', $sk ) ) wp_send_json_error( 'bad-section-key', 400 );
    $active = wpconvert_cpt_get_active_cpts();
    if ( ! isset( $active[ $sk ] ) ) wp_send_json_error( 'not-active', 404 );
    $summary = wpconvert_cpt_import_candidate( $sk );
    wp_send_json_success( array( 'summary' => $summary ) );
}

/* ─────────────────────────────────────────────
 * 12. ADMIN NOTICE  (Ship 3)
 * ───────────────────────────────────────────── */

/**
 * Decide whether the activation prompt should render on this request.
 * Centralized so the visibility logic is testable in isolation.
 *
 * Conditions (ALL must be true):
 *   - Pro/Agency/PAYG tier AND version floor met
 *   - User has `manage_options`
 *   - Admin context (not a front-end render)
 *   - Manifest has at least one candidate
 *   - At least one candidate is NOT yet activated
 *   - Current user has not dismissed the notice
 *
 * @return bool
 */
function wpconvert_cpt_should_show_notice() {
    if ( ! wpconvert_cpt_should_run() ) return false;
    if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_options' ) ) return false;
    if ( ! function_exists( 'is_admin' ) || ! is_admin() ) return false;

    try {
        if ( function_exists( 'get_current_user_id' ) && function_exists( 'get_user_meta' ) ) {
            $uid = get_current_user_id();
            if ( $uid && get_user_meta( $uid, 'wpconvert_cpt_notice_dismissed', true ) ) {
                return false;
            }
        }

        $manifest = wpconvert_cpt_get_candidates_manifest();
        if ( empty( $manifest ) ) return false;

        $active = wpconvert_cpt_get_active_cpts();
        $active_keys = is_array( $active ) ? array_keys( $active ) : array();

        foreach ( $manifest as $c ) {
            if ( ! isset( $c['section_key'] ) ) continue;
            if ( ! in_array( $c['section_key'], $active_keys, true ) ) {
                return true; // at least one candidate awaits activation
            }
        }
    } catch ( \Throwable $e ) {
        return false;
    }
    return false;
}

/**
 * Render the admin notice. Shows a small panel with each un-activated
 * candidate + a "Set up" button that opens an inline form. Submitting
 * the form calls `wpconvert_cpt_activate_and_import` via fetch().
 *
 * All output is escaped. The inline JS is small enough (~80 lines) to
 * embed in the notice — promoting to a separate file is a Ship 4 task.
 */
function wpconvert_cpt_render_admin_notice() {
    if ( ! wpconvert_cpt_should_show_notice() ) return;
    try {
        // Ship 4c.7 / B8 — read through the shared helper so the notice
        // and the dashboard's Pending tab can NEVER drift in what they
        // consider "activatable". See
        // wpconvert_cpt_get_activatable_pending_candidates() for the
        // full filter set.
        list( $pending, ) = wpconvert_cpt_get_activatable_pending_candidates();
        if ( empty( $pending ) ) return;

        $ajax_url = function_exists( 'admin_url' )
            ? admin_url( 'admin-ajax.php' )
            : '/wp-admin/admin-ajax.php';
        $nonce = function_exists( 'wp_create_nonce' )
            ? wp_create_nonce( 'wpconvert_cpt_nonce' )
            : '';

        // Ship 4c.1 — surface whether ACF is detected so the per-candidate
        // checkbox makes sense to the user.
        $acf_available = wpconvert_cpt_acf_available();
        // EC-CPT-008 — same for Meta Box (MB Builder).
        $metabox_available = wpconvert_cpt_metabox_available();

        echo '<div class="notice notice-info wpconvert-cpt-notice" style="border-left-color:#2271b1;">';
        echo '<h3 style="margin:0.6em 0 0.3em">WPConvert detected '
            . count( $pending )
            . ' content '
            . ( count( $pending ) === 1 ? 'collection' : 'collections' )
            . ' that could become Custom Post Types.</h3>';
        echo '<p style="margin:0 0 1em">Activating turns the static content into editable posts in wp-admin. You can deactivate at any time without losing data.</p>';
        if ( $acf_available ) {
            echo '<p style="margin:0 0 1em;padding:0.5em 0.8em;background:#f0f6fc;border-left:3px solid #2271b1;font-size:0.9em;">'
                . '<strong>ACF detected.</strong> Tick "Manage with ACF" on any candidate below to generate an ACF field group instead of using WPConvert\'s built-in editor. You can edit the group structure in <em>Custom Fields → Field Groups</em>.</p>';
        }

        // Ship 3.2 — pre-compute base slug counts so we can disambiguate
        // colliding suggestions. Without this, 4 candidates named "doctors"
        // all suggest slug="doctor" and the user gets `post-type-collision`
        // on every activation after the first.
        $base_slug_counts = array();
        foreach ( $pending as $c ) {
            $sug = wpconvert_cpt_suggest_labels( $c['source_array'] ?? 'content_item' );
            $base = $sug['slug'];
            $base_slug_counts[ $base ] = ( $base_slug_counts[ $base ] ?? 0 ) + 1;
        }

        echo '<div class="wpconvert-cpt-candidates" style="display:grid;grid-gap:0.5em;margin:0.5em 0 1em;">';
        foreach ( $pending as $c ) {
            $suggestion = wpconvert_cpt_suggest_labels( $c['source_array'] ?? 'content_item' );
            $base_slug = $suggestion['slug'];
            $suggested_slug     = $base_slug;
            $suggested_singular = $suggestion['singular'];
            $suggested_plural   = $suggestion['plural'];

            // Ship 3.2 disambiguation: when the same base slug is suggested
            // by 2+ candidates, append the file-context token.
            if ( ( $base_slug_counts[ $base_slug ] ?? 0 ) > 1 ) {
                $file_tok = wpconvert_cpt_file_context_token( $c['source_file'] ?? '' );
                if ( $file_tok !== '' && $file_tok !== 'x' ) {
                    $suggested_slug = $base_slug . '_' . $file_tok;
                    // Trim to fit the 20-char hard limit. If we'd exceed it,
                    // fall back to a short hash suffix.
                    if ( strlen( $suggested_slug ) > 20 ) {
                        $suggested_slug = substr( $base_slug, 0, 13 ) . '_'
                            . substr( sha1( $c['source_file'] ?? '' ), 0, 6 );
                    }
                }
            }
            if ( ! wpconvert_cpt_is_valid_slug( $suggested_slug ) ) {
                $suggested_slug = 'content_item';
            }

            $field_names = array();
            foreach ( ( $c['fields'] ?? array() ) as $f ) {
                if ( ! empty( $f['key'] ) ) $field_names[] = $f['key'];
            }

            $looks_blog = wpconvert_cpt_looks_like_blog( $c );

            echo '<details class="wpconvert-cpt-candidate" data-section-key="' . esc_attr( $c['section_key'] ) . '" style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:4px;padding:0.6em 1em;">';
            // Ship 3.2: include source_file in the summary so duplicates
            // are distinguishable in the form.
            $source_file_basename = isset( $c['source_file'] )
                ? basename( $c['source_file'] )
                : '';
            echo '<summary style="cursor:pointer;font-weight:600;">'
                . esc_html( $c['source_array'] ?? '(unknown)' )
                . ( $source_file_basename !== ''
                    ? ' <span class="wpc-cpt-src-file" style="color:#2271b1;font-weight:500;font-size:0.85em;">— ' . esc_html( $source_file_basename ) . '</span>'
                    : '' )
                . ' <span style="color:#646970;font-weight:normal;font-size:0.9em;">— '
                . (int) ( $c['item_count'] ?? 0 ) . ' items, '
                . count( $field_names ) . ' fields ('
                . esc_html( implode( ', ', array_slice( $field_names, 0, 4 ) ) )
                . ( count( $field_names ) > 4 ? ', ...' : '' )
                . ')</span></summary>';

            // Ship 3.2: blog-shape informational hint.
            if ( $looks_blog ) {
                echo '<p class="wpc-cpt-blog-hint" style="margin:0.6em 0 0.4em;padding:0.5em 0.8em;background:#fff8e5;border-left:3px solid #dba617;font-size:0.9em;">'
                    . '<strong>Looks like blog posts.</strong> Activating creates a custom post type separate from WordPress\'s native Posts. '
                    . 'To use WP\'s native Posts instead, add <code>define( \'WPCONVERT_NATIVE_BLOG\', true );</code> to <code>wp-config.php</code> '
                    . 'and this candidate will be skipped.</p>';
            }

            echo '<div style="margin-top:0.8em;display:grid;grid-template-columns:120px 1fr;grid-gap:0.4em 1em;align-items:center;max-width:600px;">';
            echo '<label>Slug</label>';
            echo '<input type="text" class="wpc-cpt-slug regular-text" value="' . esc_attr( $suggested_slug ) . '" pattern="[a-z][a-z0-9_]{0,19}" maxlength="20" required>';
            echo '<label>Singular label</label>';
            echo '<input type="text" class="wpc-cpt-singular regular-text" value="' . esc_attr( $suggested_singular ) . '" required>';
            echo '<label>Plural label</label>';
            echo '<input type="text" class="wpc-cpt-plural regular-text" value="' . esc_attr( $suggested_plural ) . '" required>';
            // Ship 4c.1 — per-candidate ACF opt-in. Only shown when ACF is
            // actually loaded; the modal stays free of the option otherwise.
            if ( $acf_available ) {
                echo '<label></label>';
                echo '<label style="font-weight:normal;">'
                    . '<input type="checkbox" class="wpc-cpt-acf-managed" value="1"> '
                    . 'Manage with ACF '
                    . '<span style="color:#646970;font-size:0.85em;">(auto-creates an ACF field group, hides WPConvert\'s meta box)</span>'
                    . '</label>';
            }
            // EC-CPT-008 — per-candidate Meta Box opt-in. Only shown when MB
            // Builder is loaded. Mutually exclusive with the ACF checkbox above
            // (enforced client-side + server-side; ACF wins on conflict).
            if ( $metabox_available ) {
                echo '<label></label>';
                echo '<label style="font-weight:normal;">'
                    . '<input type="checkbox" class="wpc-cpt-metabox-managed" value="1"> '
                    . 'Manage with Meta Box '
                    . '<span style="color:#646970;font-size:0.85em;">(auto-creates a Meta Box field group, hides WPConvert\'s meta box)</span>'
                    . '</label>';
            }
            echo '</div>';
            echo '<p style="margin-top:0.8em;">';
            echo '<button type="button" class="button button-primary wpc-cpt-activate-btn">Activate &amp; import</button> ';
            echo '<button type="button" class="button wpc-cpt-dry-run-btn">Dry-run preview</button> ';
            echo '<span class="wpc-cpt-status" style="margin-left:0.6em;color:#646970;"></span>';
            echo '</p>';
            echo '</details>';
        }
        echo '</div>';
        // Ship 4c.7 / B8 — link to the full dashboard. The notice is
        // optimised for first-time discovery (one-click activate), but
        // power users want the bulk-actions, slug-edit, ACF-managed
        // column, and Diagnostics tab — those only exist in the
        // dashboard. Before B8 there was no in-context link; users
        // who dismissed the notice often forgot the plugin existed.
        $dash_url = function_exists( 'admin_url' )
            ? admin_url( 'tools.php?page=wpconvert-cpt' )
            : '#';
        echo '<p style="margin-top:0.5em;">';
        echo '<a href="' . esc_url( $dash_url ) . '" class="button button-secondary wpc-cpt-open-dashboard-btn">'
            . esc_html__( 'Open WPConvert CPTs dashboard', 'wpconvert-cpt' )
            . '</a> ';
        echo '<button type="button" class="button button-link wpc-cpt-dismiss-btn" style="text-decoration:underline;color:#646970;">'
            . esc_html__( 'Dismiss this notice', 'wpconvert-cpt' )
            . '</button>';
        echo '</p>';
        echo '</div>';

        // Inline JS — small, single-purpose. Vanilla DOM, no jQuery dep.
        // Escaping note: $ajax_url and $nonce are server-generated, never
        // user-controlled, so direct interpolation is safe here.
        ?>
<script>
(function () {
    var AJAX_URL = <?php echo json_encode( $ajax_url ); ?>;
    var NONCE = <?php echo json_encode( $nonce ); ?>;

    function post(action, body) {
        var fd = new FormData();
        fd.append('action', action);
        fd.append('nonce', NONCE);
        Object.keys(body || {}).forEach(function (k) { fd.append(k, body[k]); });
        return fetch(AJAX_URL, { method: 'POST', credentials: 'same-origin', body: fd })
            .then(function (r) { return r.json(); });
    }

    function runActivate(detailsEl, dryRun) {
        var sk = detailsEl.getAttribute('data-section-key');
        var slug = detailsEl.querySelector('.wpc-cpt-slug').value.trim();
        var singular = detailsEl.querySelector('.wpc-cpt-singular').value.trim();
        var plural = detailsEl.querySelector('.wpc-cpt-plural').value.trim();
        var statusEl = detailsEl.querySelector('.wpc-cpt-status');
        // Ship 4c.1 — only present in the DOM when ACF is detected.
        var acfBox = detailsEl.querySelector('.wpc-cpt-acf-managed');
        var acfManaged = acfBox && acfBox.checked ? '1' : '';
        // EC-CPT-008 — only present in the DOM when Meta Box is detected.
        var mbBox = detailsEl.querySelector('.wpc-cpt-metabox-managed');
        var metaboxManaged = mbBox && mbBox.checked ? '1' : '';
        if (!slug || !/^[a-z][a-z0-9_]{0,19}$/.test(slug)) {
            statusEl.textContent = 'Slug must be lowercase, letters/numbers/underscores, max 20 chars';
            statusEl.style.color = '#d63638';
            return;
        }
        statusEl.style.color = '#646970';
        statusEl.textContent = dryRun ? 'Running dry-run…' : 'Activating…';
        post('wpconvert_cpt_activate_and_import', {
            section_key: sk,
            post_type: slug,
            singular: singular,
            plural: plural,
            menu_icon: 'dashicons-admin-post',
            import: '1',
            dry_run: dryRun ? '1' : '',
            acf_managed: acfManaged,
            metabox_managed: metaboxManaged
        }).then(function (resp) {
            if (!resp || !resp.success) {
                statusEl.style.color = '#d63638';
                statusEl.textContent = 'Error: ' + ((resp && resp.data) || 'unknown');
                return;
            }
            var s = resp.data.import || {};
            var msg;
            if (dryRun) {
                msg = 'Dry-run: would create ' + (s.created || 0) +
                    ', update ' + (s.updated || 0) +
                    ', fail ' + (s.failed || 0) + ' (of ' + (s.total_items || 0) + ')';
                statusEl.style.color = '#2271b1';
            } else {
                msg = 'Done: created ' + (s.created || 0) +
                    ', updated ' + (s.updated || 0);
                if (s.failed) msg += ', failed ' + s.failed;
                msg += ' of ' + (s.total_items || 0);
                statusEl.style.color = '#00a32a';
            }
            statusEl.textContent = msg;
        }).catch(function (err) {
            statusEl.style.color = '#d63638';
            statusEl.textContent = 'Network error: ' + err.message;
        });
    }

    function runDismiss() {
        post('wpconvert_cpt_dismiss_notice', {}).then(function (resp) {
            var n = document.querySelector('.wpconvert-cpt-notice');
            if (n) n.style.display = 'none';
        });
    }

    document.querySelectorAll('.wpc-cpt-activate-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            runActivate(btn.closest('details.wpconvert-cpt-candidate'), false);
        });
    });
    document.querySelectorAll('.wpc-cpt-dry-run-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            runActivate(btn.closest('details.wpconvert-cpt-candidate'), true);
        });
    });
    document.querySelectorAll('.wpc-cpt-dismiss-btn').forEach(function (btn) {
        btn.addEventListener('click', runDismiss);
    });
    // EC-CPT-008 — ACF and Meta Box are mutually exclusive per candidate.
    // Checking one unchecks the other so the user can't request both.
    document.querySelectorAll('details.wpconvert-cpt-candidate').forEach(function (d) {
        var acf = d.querySelector('.wpc-cpt-acf-managed');
        var mb = d.querySelector('.wpc-cpt-metabox-managed');
        if (!acf || !mb) return;
        acf.addEventListener('change', function () { if (acf.checked) mb.checked = false; });
        mb.addEventListener('change', function () { if (mb.checked) acf.checked = false; });
    });
})();
</script>
        <?php
    } catch ( \Throwable $e ) {
        // Notice rendering is best-effort. Never bubble.
        return;
    }
}

/* ─────────────────────────────────────────────
 * 13. WP-CLI COMMANDS  (Ship 3 — registered only if WP_CLI is defined)
 * ───────────────────────────────────────────── */

/**
 * Register WP-CLI commands. Idempotent (WP_CLI::add_command no-ops if
 * the command already exists). Gated on the tier check so Starter
 * installs don't get the commands.
 */
function wpconvert_cpt_register_cli_commands() {
    if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;
    if ( ! wpconvert_cpt_should_run() ) return;
    try {
        if ( class_exists( '\\WP_CLI' ) ) {
            \WP_CLI::add_command( 'wpconvert cpt', 'WPConvert_CPT_CLI' );
        }
    } catch ( \Throwable $e ) {
        // CLI command registration is best-effort.
    }
}

// Only define the class when WP_CLI is loaded, so a non-CLI request
// doesn't pay the parse cost.
if ( defined( 'WP_CLI' ) && WP_CLI && ! class_exists( 'WPConvert_CPT_CLI' ) ) {

    /**
     * Manage WPConvert custom post types via the command line.
     */
    class WPConvert_CPT_CLI {

        /**
         * List all detected CPT candidates and their activation state.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert cpt list
         */
        public function list( $args, $assoc_args ) {
            $manifest = wpconvert_cpt_get_candidates_manifest();
            $active   = wpconvert_cpt_get_active_cpts();
            $rows = array();
            foreach ( $manifest as $c ) {
                $sk = $c['section_key'] ?? '';
                $rows[] = array(
                    'section_key' => substr( $sk, 0, 12 ),
                    'source'      => $c['source_array'] ?? '',
                    'items'       => (int) ( $c['item_count'] ?? 0 ),
                    'fields'      => count( $c['fields'] ?? array() ),
                    'active'      => isset( $active[ $sk ] ) ? 'YES' : 'no',
                    'post_type'   => isset( $active[ $sk ]['post_type'] ) ? $active[ $sk ]['post_type'] : '',
                );
            }
            if ( empty( $rows ) ) {
                \WP_CLI::log( 'No CPT candidates detected on this theme.' );
                return;
            }
            \WP_CLI\Utils\format_items(
                'table',
                $rows,
                array( 'section_key', 'source', 'items', 'fields', 'active', 'post_type' )
            );
        }

        /**
         * Activate a CPT and (optionally) import its items.
         *
         * ## OPTIONS
         *
         * <section_key>
         * : Full or short section key from `wp wpconvert cpt list`.
         *
         * --post_type=<slug>
         * : The post-type slug to register (lowercase, [a-z0-9_], max 20).
         *
         * [--singular=<label>]
         * : Singular label (default: derived from post_type).
         *
         * [--plural=<label>]
         * : Plural label (default: singular + 's').
         *
         * [--menu_icon=<dashicon>]
         * : Dashicon class for the admin menu (default: dashicons-admin-post).
         *
         * [--no-import]
         * : Activate but don't import items.
         *
         * [--dry-run]
         * : Print what would happen without writing anything.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert cpt activate aab1ebcfce22 --post_type=pancake_menu --singular="Menu Item" --plural="Menu Items"
         */
        public function activate( $args, $assoc_args ) {
            $section_key = $this->resolve_section_key( $args[0] ?? '' );
            if ( ! $section_key ) {
                \WP_CLI::error( 'Section key not found in manifest.' );
            }
            $slug_raw = $assoc_args['post_type'] ?? '';
            $slug = wpconvert_cpt_normalize_post_type_slug( $slug_raw );
            if ( ! wpconvert_cpt_is_valid_slug( $slug ) ) {
                \WP_CLI::error( 'Invalid --post_type. Must match /^[a-z][a-z0-9_]{0,19}$/ and not collide with a reserved slug.' );
            }
            if ( function_exists( 'post_type_exists' ) && post_type_exists( $slug )
                && ! wpconvert_cpt_is_our_registered_post_type( $slug ) ) {
                \WP_CLI::error( 'Slug "' . $slug . '" is already registered by another plugin or theme.' );
            }
            $singular = $assoc_args['singular'] ?? ucwords( str_replace( '_', ' ', $slug ) );
            $plural   = $assoc_args['plural']   ?? $singular . 's';
            $icon     = $assoc_args['menu_icon'] ?? 'dashicons-admin-post';
            $do_import = ! isset( $assoc_args['no-import'] );
            $dry_run = isset( $assoc_args['dry-run'] );

            $cfg = array(
                'enabled'      => true,
                'post_type'    => $slug,
                'singular'     => $singular,
                'plural'       => $plural,
                'menu_icon'    => $icon,
                'public'       => true,
                'has_archive'  => true,
                'show_in_rest' => true,
                'activated_at' => time(),
            );

            if ( ! $dry_run ) {
                $active = wpconvert_cpt_get_active_cpts();
                if ( ! is_array( $active ) ) $active = array();
                $active[ $section_key ] = $cfg;
                update_option( 'wpconvert_cpts', $active, false );
                wpconvert_cpt_register_active_post_types();
                update_option( 'wpconvert_cpts_needs_flush', 1, false );
                \WP_CLI::success( 'Activated CPT "' . $slug . '" for section ' . substr( $section_key, 0, 12 ) . '.' );
            } else {
                \WP_CLI::log( '[dry-run] Would activate CPT "' . $slug . '".' );
            }

            if ( $do_import ) {
                // Mirror AJAX: pass in-memory $cfg to dry-run so the importer
                // doesn't bail on "cpt-not-activated" when no option was written.
                $import_args = array( 'dry_run' => $dry_run );
                if ( $dry_run ) {
                    $import_args['cpt_config'] = $cfg;
                }
                $summary = wpconvert_cpt_import_candidate( $section_key, $import_args );
                \WP_CLI::log( sprintf(
                    '%s%d created, %d updated, %d failed, %d total',
                    $dry_run ? '[dry-run] ' : '',
                    (int) $summary['created'],
                    (int) $summary['updated'],
                    (int) $summary['failed'],
                    (int) ( $summary['total_items'] ?? 0 )
                ) );
                if ( ! empty( $summary['errors'] ) ) {
                    foreach ( $summary['errors'] as $err ) {
                        \WP_CLI::warning( $err );
                    }
                }
            }
        }

        /**
         * Deactivate a CPT. Posts already imported are NOT deleted —
         * deactivation just stops registering the post type. Re-activate
         * to make the posts visible again.
         *
         * ## OPTIONS
         *
         * <section_key>
         * : Full or short section key.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert cpt deactivate aab1ebcfce22
         */
        public function deactivate( $args, $assoc_args ) {
            $section_key = $this->resolve_section_key( $args[0] ?? '' );
            if ( ! $section_key ) {
                \WP_CLI::error( 'Section key not found in manifest.' );
            }
            $active = wpconvert_cpt_get_active_cpts();
            if ( ! isset( $active[ $section_key ] ) ) {
                \WP_CLI::warning( 'CPT was not active.' );
                return;
            }
            unset( $active[ $section_key ] );
            update_option( 'wpconvert_cpts', $active, false );
            update_option( 'wpconvert_cpts_needs_flush', 1, false );
            \WP_CLI::success( 'Deactivated CPT for section ' . substr( $section_key, 0, 12 ) . '. Imported posts are preserved.' );
        }

        /**
         * Re-import items for an already-active CPT.
         *
         * ## OPTIONS
         *
         * <section_key>
         * : Full or short section key.
         *
         * [--dry-run]
         * : Print what would happen without writing anything.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert cpt import aab1ebcfce22
         */
        public function import( $args, $assoc_args ) {
            $section_key = $this->resolve_section_key( $args[0] ?? '' );
            if ( ! $section_key ) {
                \WP_CLI::error( 'Section key not found in manifest.' );
            }
            $dry_run = isset( $assoc_args['dry-run'] );
            $summary = wpconvert_cpt_import_candidate( $section_key, array( 'dry_run' => $dry_run ) );
            \WP_CLI::log( sprintf(
                '%s%d created, %d updated, %d failed, %d total',
                $dry_run ? '[dry-run] ' : '',
                (int) $summary['created'],
                (int) $summary['updated'],
                (int) $summary['failed'],
                (int) ( $summary['total_items'] ?? 0 )
            ) );
            if ( ! empty( $summary['errors'] ) ) {
                foreach ( $summary['errors'] as $err ) {
                    \WP_CLI::warning( $err );
                }
            }
        }

        /**
         * Resolve a short or full section_key. Returns the FULL key or
         * empty string if not found.
         */
        private function resolve_section_key( $partial ) {
            $partial = is_string( $partial ) ? trim( $partial ) : '';
            if ( $partial === '' ) return '';
            $manifest = wpconvert_cpt_get_candidates_manifest();
            foreach ( $manifest as $c ) {
                $sk = $c['section_key'] ?? '';
                if ( $sk === $partial ) return $sk;
                if ( strpos( $sk, $partial ) === 0 ) return $sk;
            }
            return '';
        }
    }
}

/* ─────────────────────────────────────────────
 * 13.5 META BOXES  (Ship 4a — auto-generated post-edit forms)
 * ─────────────────────────────────────────────
 *
 * Goal
 * ----
 * After Ship 3 imports posts into the activated CPTs, the wp-admin post
 * editor shows the standard title / body / excerpt / featured image
 * widgets, but the rich `_wpc_field_*` post meta the importer wrote
 * (price, credentials, YouTube ID, slug, badge, etc.) has no UI. Users
 * see imported posts but can't EDIT the custom fields. Ship 4a closes
 * that gap by registering ONE meta box per active CPT that auto-builds
 * a form from the field schema stored in `wp_options['wpconvert_cpts']`.
 *
 * Safety properties
 * -----------------
 * - Tier-gated via wpconvert_cpt_should_run() (Starter sees nothing).
 * - Only renders for ACTIVATED CPTs (no orphan UI if a CPT is removed
 *   from the option but its posts still exist).
 * - Renders nothing if the cfg has no `fields` (defensive — protects
 *   against partial activations / hand-edited options).
 * - Save handler bails on: missing nonce, invalid nonce, autosave, post
 *   revision, insufficient `edit_post` capability, wrong post type.
 * - Per-field sanitization reuses Ship 3's wpconvert_cpt_sanitize_field_value
 *   so importer-vs-edit have identical type semantics (no double-sanitize
 *   inconsistencies).
 * - Image picker uses WP core's media library (wp_enqueue_media) — no
 *   custom uploader, no third-party JS, no XSS surface area.
 * - Field schema is READ-ONLY in the meta box. Users edit VALUES, not the
 *   schema (changing field types lives in Ship 4c admin page).
 *
 * Data flow
 * ---------
 *   wp_options['wpconvert_cpts'][section_key]['fields']
 *     -> render_meta_box() iterates and emits <input>/<textarea>/<select>
 *     -> user edits, clicks Update
 *     -> save_meta_box() reads $_POST['wpc_field'], sanitizes per type
 *     -> update_post_meta($post_id, '_wpc_field_<key>', $sanitized)
 *
 * The meta keys MATCH what wpconvert_cpt_import_item writes (same
 * wpconvert_cpt_meta_key_for_field helper), so the importer and the
 * meta box read/write the same storage.
 */

/**
 * Heuristic: does this stored value look like a JSON-encoded array of
 * strings? Used by the `unknown` widget path to render a friendlier
 * multi-line list editor instead of a fragile single-line text input.
 *
 * The pattern is what the Ship 1 candidate detector emits when it
 * encounters a JS array literal but can't infer a scalar type — it
 * captures the source-text verbatim, which for `locations: ["Cypress", "Katy"]`
 * comes through here as the literal STRING `["Cypress", "Katy"]`.
 *
 * Returns the decoded PHP array on a positive match, or null otherwise.
 * (Returning the array — not just a bool — lets callers reuse the parse.)
 *
 * @param mixed $value
 * @return array|null
 */
function wpconvert_cpt_decode_json_list( $value ) {
    if ( ! is_string( $value ) || $value === '' ) return null;
    $trim = trim( $value );
    // Cheap shape gate before json_decode (which is forgiving).
    if ( $trim === '' || $trim[0] !== '[' || substr( $trim, -1 ) !== ']' ) return null;
    // 4 KB cap — guards against pathological inputs / DoS via deep JSON.
    if ( strlen( $trim ) > 4096 ) return null;
    $decoded = json_decode( $trim, true );
    if ( ! is_array( $decoded ) ) return null;
    // Only treat as a "list of strings" if every element is a scalar string.
    // Mixed arrays / nested arrays fall through to plain text input where
    // the user can still see the raw JSON.
    foreach ( $decoded as $el ) {
        if ( ! is_string( $el ) ) return null;
    }
    return $decoded;
}

/**
 * Look up the field schema for an active CPT. Prefers the copy persisted
 * in wp_options (cheap, no I/O) and falls back to the candidate manifest
 * (Ship 3 activations didn't persist fields — back-compat path).
 *
 * Returns an array of normalized field descriptors:
 *   [ { key, type, remapped_to, enum? }, ... ]
 *
 * @param array $cfg          The wp_options['wpconvert_cpts'][section_key] entry.
 * @param string $section_key The section_key for manifest fallback lookup.
 * @return array
 */
function wpconvert_cpt_get_fields_for_cpt( $cfg, $section_key = '' ) {
    if ( is_array( $cfg ) && ! empty( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) {
        $out = array();
        foreach ( $cfg['fields'] as $f ) {
            if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
            $out[] = array(
                'key'         => (string) $f['key'],
                'type'        => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                'remapped_to' => isset( $f['remapped_to'] ) ? (string) $f['remapped_to'] : (string) $f['key'],
                'enum'        => ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) ? $f['enum'] : null,
            );
        }
        return $out;
    }
    // Back-compat: legacy activation without `fields` key — read manifest.
    if ( ! is_string( $section_key ) || $section_key === '' ) return array();
    try {
        $manifest = wpconvert_cpt_get_candidates_manifest();
        foreach ( $manifest as $c ) {
            if ( ! isset( $c['section_key'] ) || $c['section_key'] !== $section_key ) continue;
            $out = array();
            foreach ( ( $c['fields'] ?? array() ) as $f ) {
                if ( empty( $f['key'] ) ) continue;
                $out[] = array(
                    'key'         => (string) $f['key'],
                    'type'        => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                    'remapped_to' => isset( $f['remapped_to'] ) ? (string) $f['remapped_to'] : (string) $f['key'],
                    'enum'        => ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) ? $f['enum'] : null,
                );
            }
            return $out;
        }
    } catch ( \Throwable $e ) {
        // ignore — return empty
    }
    return array();
}

/**
 * Render the form input widget for a single field. Pure-output (echoes
 * directly). Used by render_meta_box; isolated as a separate function so
 * EC-CPT-004 can unit-test per-type rendering.
 *
 * @param array  $field  { key, type, remapped_to, enum }
 * @param mixed  $value  Current stored value.
 * @param string $name   The full <input name="..."> to emit.
 */
function wpconvert_cpt_render_field_widget( $field, $value, $name ) {
    $type = isset( $field['type'] ) ? (string) $field['type'] : 'unknown';
    $value_str = is_scalar( $value ) ? (string) $value : '';
    $name_attr = esc_attr( $name );
    $id_attr   = esc_attr( str_replace( array( '[', ']' ), array( '-', '' ), $name ) );

    switch ( $type ) {
        case 'text-long':
            echo '<textarea name="' . $name_attr . '" id="' . $id_attr . '" rows="4" cols="50" class="large-text wpc-cpt-field wpc-cpt-field-text-long">'
                . esc_textarea( $value_str )
                . '</textarea>';
            break;

        case 'image':
            // Ship 4c.7 / B9 — accept ANY value shape the importer might
            // have written. Pre-B9 we only handled `(int) $value_str`,
            // which silently coerced strings like "/assets/images/foo.jpg"
            // (theme-relative path) or "drPatrick" (React source token)
            // to `0`, leaving the meta box stuck on "No image" forever
            // even though the loop swap was rendering the image just
            // fine. The user surfaced this on the Direct Training
            // conversion (May 19 2026) when every imported `courses`
            // CPT showed "No image" in the edit screen.
            //
            // Resolution path mirrors what the loop swap accepts at
            // line ~6887: numeric ID → use directly; otherwise hand to
            // wpconvert_cpt_resolve_image_to_attachment_id() which
            // covers identifier tokens, theme-relative paths, and
            // sideloadable URLs. On resolution success we ALSO normalise
            // the hidden input to the resolved attachment ID so future
            // saves persist the integer (much cheaper to read on
            // subsequent renders, and matches what the meta-box save
            // path writes when the user picks via the media library).
            $att_id = 0;
            if ( ctype_digit( $value_str ) ) {
                $att_id = (int) $value_str;
            } elseif ( $value_str !== '' && function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' ) ) {
                $att_id = (int) wpconvert_cpt_resolve_image_to_attachment_id( $value_str );
            }

            // Hidden-input policy: if we resolved an ID, write it back.
            // If we did NOT resolve, preserve the original value so the
            // user's data isn't silently destroyed on the next save
            // (the loop swap can still handle the unresolved shape).
            $hidden_value = $att_id > 0 ? (string) $att_id : $value_str;

            // EC-CPT-IMAGE-PREVIEW-URL — prefer the safe URL helper for
            // theme-attached files. WP's stock wp_get_attachment_image_url
            // returns a malformed `…/uploads//wp-content/themes/…` URL when
            // _wp_attached_file is an absolute path outside uploads (which
            // is exactly what wpconvert_cpt_attach_existing_file produces
            // for /assets/images/* files bundled in the theme). The safe
            // helper maps the theme path → theme URI via the
            // _wpc_attachment_path meta the importer recorded, falling back
            // to the stock URL (after sanity-checking the shape) for
            // genuine uploads-resident attachments.
            $preview_url = '';
            if ( $att_id > 0 ) {
                if ( function_exists( 'wpconvert_cpt_get_attachment_url_safe' ) ) {
                    $preview_url = (string) wpconvert_cpt_get_attachment_url_safe( $att_id );
                }
                if ( $preview_url === '' && function_exists( 'wp_get_attachment_image_url' ) ) {
                    $candidate = (string) wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    if ( $candidate !== '' && strpos( $candidate, '/uploads//' ) === false ) {
                        $preview_url = $candidate;
                    }
                }
            }
            echo '<div class="wpc-cpt-image-field" data-name="' . $name_attr . '">';
            echo '<input type="hidden" name="' . $name_attr . '" id="' . $id_attr . '" class="wpc-cpt-field wpc-cpt-field-image" value="' . esc_attr( $hidden_value ) . '">';
            echo '<div class="wpc-cpt-image-preview" style="max-width:120px;margin-bottom:0.4em;">';
            if ( $preview_url !== '' ) {
                echo '<img src="' . esc_attr( $preview_url ) . '" style="max-width:100%;height:auto;border:1px solid #c3c4c7;border-radius:3px;">';
            } else {
                echo '<span style="display:inline-block;padding:1em;color:#646970;background:#f0f0f1;border:1px dashed #c3c4c7;border-radius:3px;font-size:0.85em;">No image</span>';
            }
            echo '</div>';
            echo '<button type="button" class="button wpc-cpt-image-pick">Select image</button> ';
            echo '<button type="button" class="button wpc-cpt-image-remove"' . ( $att_id ? '' : ' style="display:none;"' ) . '>Remove</button>';
            echo '</div>';
            break;

        case 'date':
            echo '<input type="date" name="' . $name_attr . '" id="' . $id_attr
                . '" value="' . esc_attr( $value_str )
                . '" class="wpc-cpt-field wpc-cpt-field-date" pattern="\d{4}-\d{2}-\d{2}">';
            break;

        case 'number':
            // Honour decimals: importer accepts floats, save accepts floats.
            echo '<input type="number" step="any" name="' . $name_attr . '" id="' . $id_attr
                . '" value="' . esc_attr( $value_str )
                . '" class="wpc-cpt-field wpc-cpt-field-number">';
            break;

        case 'url':
            echo '<input type="url" name="' . $name_attr . '" id="' . $id_attr
                . '" value="' . esc_attr( $value_str )
                . '" class="regular-text wpc-cpt-field wpc-cpt-field-url" placeholder="https://">';
            break;

        case 'select':
            $enum = ( isset( $field['enum'] ) && is_array( $field['enum'] ) ) ? $field['enum'] : array();
            echo '<select name="' . $name_attr . '" id="' . $id_attr . '" class="wpc-cpt-field wpc-cpt-field-select">';
            echo '<option value=""' . ( $value_str === '' ? ' selected' : '' ) . '>— select —</option>';
            foreach ( $enum as $opt ) {
                $opt_str = is_scalar( $opt ) ? (string) $opt : '';
                if ( $opt_str === '' ) continue;
                $sel = ( $opt_str === $value_str ) ? ' selected' : '';
                echo '<option value="' . esc_attr( $opt_str ) . '"' . $sel . '>' . esc_html( $opt_str ) . '</option>';
            }
            // If the stored value isn't in the enum (e.g. enum was edited
            // after import), preserve it as a one-off option so the user
            // doesn't silently lose data.
            if ( $value_str !== '' && ! in_array( $value_str, array_map( 'strval', $enum ), true ) ) {
                echo '<option value="' . esc_attr( $value_str ) . '" selected>' . esc_html( $value_str ) . ' (custom)</option>';
            }
            echo '</select>';
            break;

        case 'unknown':
            // Ship 4a.1 — if the stored value is a JSON array of strings
            // (e.g. detector captured `locations: ["Cypress", "Katy"]` as the
            // literal source text), render a multi-line textarea so the user
            // can edit one item per line. Save handler converts back to JSON
            // array storage. Plain strings still get a normal text input.
            $list = wpconvert_cpt_decode_json_list( $value_str );
            if ( $list !== null ) {
                echo '<textarea name="' . $name_attr . '" id="' . $id_attr
                    . '" rows="' . max( 3, min( 8, count( $list ) + 1 ) ) . '" cols="50"'
                    . ' class="large-text wpc-cpt-field wpc-cpt-field-unknown wpc-cpt-field-list"'
                    . ' data-wpc-list="1"'
                    . ' placeholder="One item per line">'
                    . esc_textarea( implode( "\n", $list ) )
                    . '</textarea>';
                // Hidden flag tells the save handler this field was edited as
                // a list (so we re-encode back to JSON). Even if the user
                // deletes everything, the flag persists with the form post.
                echo '<input type="hidden" name="wpc_field_kind[' . esc_attr(
                    preg_replace( '/^wpc_field\[(.+)\]$/', '$1', $name ) ) . ']" value="list">';
                break;
            }
            // Falls through to the text-short default.
        case 'text-short':
        default:
            echo '<input type="text" name="' . $name_attr . '" id="' . $id_attr
                . '" value="' . esc_attr( $value_str )
                . '" class="regular-text wpc-cpt-field wpc-cpt-field-' . esc_attr( $type ) . '">';
            break;
    }
}

/**
 * Render the body of the meta box. Called by WP core via add_meta_box.
 *
 * @param \WP_Post|object $post  The post being edited.
 * @param array           $args  add_meta_box $callback_args (contains 'args').
 */
function wpconvert_cpt_render_meta_box( $post, $args = array() ) {
    try {
        $box_args = isset( $args['args'] ) && is_array( $args['args'] ) ? $args['args'] : array();
        $section_key = isset( $box_args['section_key'] ) ? (string) $box_args['section_key'] : '';
        $cfg         = isset( $box_args['cfg'] ) && is_array( $box_args['cfg'] ) ? $box_args['cfg'] : array();
        // Ship 4c.3 — show only fields that the loop swap can actually
        // substitute on the front-end (plus image fields, which drive
        // the featured-image flow regardless of DOM stamping). The save
        // handler still accepts ALL schema fields, so REST/CLI imports
        // that populate phantom fields (e.g. bio used by sitemap) keep
        // working — only the human-facing editor is filtered.
        $fields      = wpconvert_cpt_get_editable_fields_for_cpt( $cfg, $section_key );
        $full_count  = count( wpconvert_cpt_get_fields_for_cpt( $cfg, $section_key ) );
        $post_id     = isset( $post->ID ) ? (int) $post->ID : 0;

        if ( empty( $fields ) ) {
            echo '<p style="color:#646970;">No fields configured for this content type yet. '
                . 'Run an import from the WPConvert admin notice to populate field definitions.</p>';
            return;
        }

        // Nonce — paired with check in save handler. Action is per-post-type
        // so a forged nonce for `doctor` can't save a `service` post.
        $pt = isset( $post->post_type ) ? (string) $post->post_type : '';
        $nonce_action = 'wpconvert_cpt_save_' . $pt;
        $nonce_field = 'wpc_cpt_nonce';
        if ( function_exists( 'wp_nonce_field' ) ) {
            wp_nonce_field( $nonce_action, $nonce_field );
        }

        echo '<table class="form-table wpc-cpt-meta-table" role="presentation">';
        echo '<tbody>';
        foreach ( $fields as $field ) {
            $display_key = (string) $field['key'];
            $storage_key = isset( $field['remapped_to'] ) && $field['remapped_to'] !== ''
                ? (string) $field['remapped_to']
                : $display_key;
            $meta_key = wpconvert_cpt_meta_key_for_field( $storage_key );
            $current_value = $post_id > 0 && function_exists( 'get_post_meta' )
                ? get_post_meta( $post_id, $meta_key, true )
                : '';
            // Storage key is namespaced under wpc_field[] so save_post can
            // iterate without enumerating wp_options on every keystroke.
            $input_name = 'wpc_field[' . $storage_key . ']';
            $label_id   = 'wpc-cpt-field-' . esc_attr( $storage_key );

            echo '<tr>';
            echo '<th scope="row" style="width:160px;">';
            echo '<label for="' . esc_attr( $label_id ) . '" style="font-weight:600;">'
                . esc_html( $display_key ) . '</label>';
            echo '<br><code style="font-size:0.75em;color:#646970;font-weight:400;">' . esc_html( $field['type'] ) . '</code>';
            echo '</th>';
            echo '<td>';
            wpconvert_cpt_render_field_widget( $field, $current_value, $input_name );
            // Show the resolved meta key as a small hint for debugging.
            echo '<p class="description" style="margin-top:0.3em;color:#646970;font-size:0.8em;">'
                . 'Stored as <code>' . esc_html( $meta_key ) . '</code>';
            if ( $display_key !== $storage_key ) {
                echo ' &middot; remapped from <code>' . esc_html( $display_key ) . '</code> (reserved post column)';
            }
            echo '</p>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Ship 4c.3 — when fields were filtered out, surface a brief
        // note so the editor's field count doesn't look like a bug to
        // users who saw bio/href in earlier versions. The detail panel
        // is collapsed by default to keep the editor tidy.
        $hidden_count = $full_count - count( $fields );
        if ( $hidden_count > 0 ) {
            $shown = array();
            foreach ( $fields as $f ) {
                $shown[] = '<code>' . esc_html( $f['key'] ) . '</code>';
            }
            $hidden = array();
            $all_fields = wpconvert_cpt_get_fields_for_cpt( $cfg, $section_key );
            $shown_keys = array_flip( array_map( function ( $f ) { return $f['key']; }, $fields ) );
            foreach ( $all_fields as $f ) {
                if ( ! isset( $shown_keys[ $f['key'] ] ) ) {
                    $hidden[] = '<code>' . esc_html( $f['key'] ) . '</code>';
                }
            }
            echo '<details style="margin-top:0.8em;color:#646970;font-size:0.85em;">';
            echo '<summary style="cursor:pointer;">'
                . esc_html(
                    sprintf(
                        '%d field%s hidden — these aren\'t rendered by the theme template, so edits wouldn\'t be visible on the front-end.',
                        $hidden_count,
                        $hidden_count === 1 ? '' : 's'
                    )
                )
                . '</summary>';
            echo '<p style="margin:0.5em 0 0;">Hidden: ' . implode( ', ', $hidden ) . '</p>';
            echo '<p style="margin:0.5em 0 0;">Visible: ' . implode( ', ', $shown ) . '</p>';
            echo '<p style="margin:0.5em 0 0;">These fields still exist in the post\'s metadata (visible via REST and CLI tools) — they\'re only hidden from this form to avoid edits that wouldn\'t show up.</p>';
            echo '</details>';
        }

        // How-to: safely add a field the converter did not auto-detect.
        // The converter generalises the high-value, design-safe fields
        // (image, title, description, link, and — where present in every
        // record — badge / button label / price-highlight). Bespoke per-card
        // elements it could not safely generalise stay as static template
        // markup. This panel documents the supported, no-code-conflict way to
        // promote one of those into an editable field.
        $tpl_file = isset( $cfg['source_file'] ) ? (string) $cfg['source_file'] : '';
        echo '<details class="wpc-cpt-addfield-help" style="margin-top:0.8em;color:#3c434a;font-size:0.85em;border-top:1px solid #f0f0f1;padding-top:0.6em;">';
        echo '<summary style="cursor:pointer;font-weight:600;">'
            . esc_html__( 'Need a field that isn\'t listed? Add a missing field safely', 'wpconvert-cpt' )
            . '</summary>';
        echo '<p style="margin:0.6em 0 0;">'
            . esc_html__( 'This content type renders via a "loop swap": the theme keeps one static template card (marked with', 'wpconvert-cpt' )
            . ' <code>data-wpc-cpt-item-template</code>) '
            . esc_html__( 'and re-renders it for every post. A field is editable here only when (a) it exists on the post type and (b) the matching element on the template card is stamped with', 'wpconvert-cpt' )
            . ' <code>data-wpc-cpt-field="&lt;key&gt;"</code>. '
            . esc_html__( 'To add one the converter missed:', 'wpconvert-cpt' )
            . '</p>';
        echo '<ol style="margin:0.5em 0 0 1.2em;padding:0;">';
        echo '<li style="margin:0.25em 0;">'
            . esc_html__( 'Create the field on this post type with Meta Box (Meta Box → Custom Fields, or Meta Box Builder). Give it a clean lowercase key, e.g.', 'wpconvert-cpt' )
            . ' <code>rates</code>. '
            . esc_html__( 'Avoid the reserved keys', 'wpconvert-cpt' )
            . ' <code>title</code>, <code>content</code>, <code>type</code>, <code>status</code>, <code>date</code>, <code>name</code> '
            . esc_html__( '(use e.g. heading/body instead).', 'wpconvert-cpt' )
            . '</li>';
        echo '<li style="margin:0.25em 0;">'
            . esc_html__( 'In the matching theme template', 'wpconvert-cpt' )
            . ( $tpl_file !== '' ? ' (<code>' . esc_html( $tpl_file ) . '</code>)' : '' )
            . ', '
            . esc_html__( 'find the template card and stamp the single element that holds the value, using the SAME key:', 'wpconvert-cpt' )
            . '</li>';
        echo '<li style="list-style:none;margin:0.35em 0;">'
            . '<code style="display:block;white-space:pre-wrap;background:#f6f7f7;padding:0.5em;border-radius:4px;">'
            . esc_html( '<div class="special-card__highlight" data-wpc-cpt-field="rates">Rates as low as $60/night</div>' )
            . '</code></li>';
        echo '<li style="margin:0.25em 0;">'
            . esc_html__( 'Keep the stamp on a single text element. Do NOT stamp a wrapper around several styled rows (e.g. a label + value table) — the loop swap replaces the element\'s text content, which would flatten that markup. For multi-element constructs, leave them static and edit them per-post with the WPConvert Editor after publishing.', 'wpconvert-cpt' )
            . '</li>';
        echo '<li style="margin:0.25em 0;">'
            . esc_html__( 'The stamped element must sit inside this CPT\'s candidate wrapper', 'wpconvert-cpt' )
            . ' (<code>data-wpc-cpt-candidate="' . esc_html( $section_key ) . '"</code>). '
            . esc_html__( 'Reload this editor — the new field will appear above and the loop swap will populate it on the front-end. (If the field saves in admin but never shows on the site, the stamp key does not match the meta key, or the template card is outside the candidate wrapper.)', 'wpconvert-cpt' )
            . '</li>';
        echo '</ol>';
        echo '</details>';
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_render_meta_box: ' . $e->getMessage() );
        }
        echo '<p style="color:#b32d2e;">An error occurred rendering the WPConvert fields meta box. Check error log.</p>';
    }
}

/**
 * Register one meta box per active CPT, on the `add_meta_boxes` hook.
 */
function wpconvert_cpt_register_meta_boxes() {
    try {
        if ( ! wpconvert_cpt_should_run() ) return;
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) || empty( $active ) ) return;

        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) || empty( $cfg['post_type'] ) ) {
                continue;
            }
            $post_type = (string) $cfg['post_type'];
            if ( ! wpconvert_cpt_is_valid_slug( $post_type ) ) continue;

            // Ship 4c.1 — when the CPT is ACF-managed AND ACF is actually
            // loaded, skip our auto meta box (ACF's UI is the editor for
            // this post type). When ACF is loaded but our box still
            // appears, the user would see TWO duplicate edit UIs.
            //
            // Defensive: when cfg.acf_managed is true but ACF isn't loaded
            // (user deactivated ACF since opting in), we DO register our
            // meta box — so editing still works and the front-end doesn't
            // go blank.
            if ( ! empty( $cfg['acf_managed'] ) && wpconvert_cpt_acf_available() ) {
                continue;
            }

            // EC-CPT-008 — same for Meta Box: when this CPT is Meta-Box-managed
            // AND Meta Box is loaded, MB Builder's UI is the editor, so skip
            // our auto meta box. Defensive: if Meta Box was deactivated since
            // opt-in, our box reappears so editing still works.
            if ( ! empty( $cfg['metabox_managed'] ) && wpconvert_cpt_metabox_available() ) {
                continue;
            }

            // Skip if the CPT has no fields to render — no UI, no clutter.
            $fields = wpconvert_cpt_get_fields_for_cpt( $cfg, (string) $section_key );
            if ( empty( $fields ) ) continue;

            $singular = isset( $cfg['singular'] ) ? (string) $cfg['singular'] : ucwords( str_replace( '_', ' ', $post_type ) );
            $box_id    = 'wpconvert_cpt_meta_' . $post_type;
            $box_title = esc_html( $singular ) . ' details';

            if ( function_exists( 'add_meta_box' ) ) {
                add_meta_box(
                    $box_id,
                    $box_title,
                    'wpconvert_cpt_render_meta_box',
                    $post_type,
                    'normal',
                    'high',
                    array( 'section_key' => (string) $section_key, 'cfg' => $cfg )
                );
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_register_meta_boxes: ' . $e->getMessage() );
        }
    }
}

/**
 * Save handler. Bound to `save_post`. Bails on autosave / revision /
 * missing nonce / invalid nonce / insufficient capability / unknown CPT.
 *
 * For each posted `wpc_field[<storage_key>]`:
 *   1. Look up the type from the CPT cfg (or fall back to manifest).
 *   2. Sanitize via wpconvert_cpt_sanitize_field_value.
 *   3. update_post_meta with the same meta key the importer would use.
 *
 * Unknown POSTed keys (not in the CPT cfg) are IGNORED — protects against
 * malicious POST keys writing arbitrary meta.
 *
 * @param int $post_id
 */
function wpconvert_cpt_save_meta_box( $post_id ) {
    try {
        $post_id = (int) $post_id;
        if ( $post_id <= 0 ) return;

        // Standard WP save_post safety screens.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) return;
        if ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) return;

        if ( ! wpconvert_cpt_should_run() ) return;

        // Look up the post type.
        $post_type = '';
        if ( function_exists( 'get_post_type' ) ) {
            $post_type = (string) get_post_type( $post_id );
        }
        if ( $post_type === '' ) return;

        // Match the post type to an active CPT cfg.
        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) ) return;
        $section_key = '';
        $cfg = null;
        foreach ( $active as $sk => $c ) {
            if ( is_array( $c ) && ! empty( $c['enabled'] ) && isset( $c['post_type'] )
                && (string) $c['post_type'] === $post_type ) {
                $section_key = (string) $sk;
                $cfg = $c;
                break;
            }
        }
        if ( $cfg === null ) return;

        // Nonce (per-post-type) — rejects cross-CPT forgery.
        $nonce_action = 'wpconvert_cpt_save_' . $post_type;
        $nonce_field = 'wpc_cpt_nonce';
        $nonce = isset( $_POST[ $nonce_field ] ) ? (string) wp_unslash( $_POST[ $nonce_field ] ) : '';
        if ( $nonce === '' ) return;
        if ( ! function_exists( 'wp_verify_nonce' ) || ! wp_verify_nonce( $nonce, $nonce_action ) ) return;

        // Capability — `edit_post` for THIS specific post, not blanket.
        if ( ! function_exists( 'current_user_can' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        // Read field schema.
        $fields = wpconvert_cpt_get_fields_for_cpt( $cfg, $section_key );
        if ( empty( $fields ) ) return;

        // Build lookup: storage_key -> { type, enum }.
        $by_storage_key = array();
        foreach ( $fields as $f ) {
            $sk = isset( $f['remapped_to'] ) && $f['remapped_to'] !== '' ? (string) $f['remapped_to'] : (string) $f['key'];
            $by_storage_key[ $sk ] = array(
                'type' => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                'enum' => ( isset( $f['enum'] ) && is_array( $f['enum'] ) ) ? $f['enum'] : null,
            );
        }

        $posted = isset( $_POST['wpc_field'] ) && is_array( $_POST['wpc_field'] )
            ? wp_unslash( $_POST['wpc_field'] )
            : array();
        // Ship 4a.1 — companion array set by the list widget. Keys here are
        // a STRICT subset of cfg.fields keys (POST values for unknown keys
        // are dropped by the per-key whitelist below, same as wpc_field).
        $posted_kinds = isset( $_POST['wpc_field_kind'] ) && is_array( $_POST['wpc_field_kind'] )
            ? wp_unslash( $_POST['wpc_field_kind'] )
            : array();

        if ( ! function_exists( 'update_post_meta' ) ) return;

        foreach ( $posted as $key => $value ) {
            $key = (string) $key;
            // Only accept keys that ARE in our cfg — drops malicious POST
            // keys that try to write arbitrary meta.
            if ( ! isset( $by_storage_key[ $key ] ) ) continue;
            $type = $by_storage_key[ $key ]['type'];
            $enum = $by_storage_key[ $key ]['enum'];

            // Ship 4a.1 — list widget: split by newline, trim, drop empties,
            // JSON-encode back to the same storage shape the detector emits.
            // ONLY applies when (a) the field is type=unknown (the list
            // widget's host type) AND (b) the marker is present (positive
            // opt-in — a forged marker on a text-short field is ignored).
            if ( $type === 'unknown'
                && isset( $posted_kinds[ $key ] )
                && $posted_kinds[ $key ] === 'list'
                && is_string( $value ) ) {
                $raw_lines = preg_split( '/\r?\n/', $value );
                $items = array();
                foreach ( $raw_lines as $line ) {
                    $line = is_string( $line ) ? trim( $line ) : '';
                    if ( $line === '' ) continue;
                    // Reuse text-short sanitization per item — strips tags,
                    // matches importer semantics for scalar string items.
                    $items[] = wpconvert_cpt_sanitize_field_value( $line, 'text-short' );
                }
                if ( empty( $items ) ) {
                    // User cleared the list — store empty string (matches
                    // "delete data" semantics from the existing path).
                    update_post_meta( $post_id, wpconvert_cpt_meta_key_for_field( $key ), '' );
                    continue;
                }
                // JSON_UNESCAPED_UNICODE so Danish/etc. characters round-trip
                // cleanly. JSON_UNESCAPED_SLASHES because we never embed URLs.
                $encoded = json_encode( $items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
                if ( $encoded === false ) {
                    // Pathological input — bail without writing.
                    continue;
                }
                update_post_meta( $post_id, wpconvert_cpt_meta_key_for_field( $key ), $encoded );
                continue;
            }

            $sanitized = wpconvert_cpt_sanitize_field_value( $value, $type, $enum );
            $meta_key = wpconvert_cpt_meta_key_for_field( $key );
            update_post_meta( $post_id, $meta_key, $sanitized );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_save_meta_box: ' . $e->getMessage() );
        }
    }
}

/**
 * Enqueue WP's media library + a tiny inline script for the image picker.
 * Only fires on post-edit screens for our CPTs (cheap on every other page).
 *
 * @param string $hook  The admin hook suffix.
 */
function wpconvert_cpt_enqueue_admin_assets( $hook ) {
    try {
        if ( ! wpconvert_cpt_should_run() ) return;
        // Only post-edit screens.
        if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) return;

        // Only for active CPTs.
        $screen_pt = '';
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            if ( $screen && isset( $screen->post_type ) ) {
                $screen_pt = (string) $screen->post_type;
            }
        }
        if ( $screen_pt === '' && isset( $_GET['post'] ) && function_exists( 'get_post_type' ) ) {
            $screen_pt = (string) get_post_type( (int) $_GET['post'] );
        }
        if ( $screen_pt === '' && isset( $_GET['post_type'] ) ) {
            $screen_pt = is_string( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
        }
        if ( $screen_pt === '' ) return;

        $active = wpconvert_cpt_get_active_cpts();
        if ( ! is_array( $active ) ) return;
        $is_ours = false;
        foreach ( $active as $cfg ) {
            if ( is_array( $cfg ) && ! empty( $cfg['post_type'] ) && (string) $cfg['post_type'] === $screen_pt ) {
                $is_ours = true;
                break;
            }
        }
        if ( ! $is_ours ) return;

        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        // Ship 4c.7 / B6 — defensive admin CSS for the meta box.
        // Background: converted themes routinely enqueue the theme's own
        // `style.css` into the BLOCK EDITOR via
        // `add_action('enqueue_block_editor_assets', ...)` so WordPress
        // blocks render with the front-end styling. That CSS contains
        //   body { color: hsl(var(--foreground)); background: hsl(var(--background)); }
        // — perfectly fine inside the Gutenberg iframe where the CSS
        // variables resolve, but DEVASTATING when an older/non-iframed
        // WP build or a non-iframed meta-box panel inherits those rules
        // and the CSS variables are unset. Result: the textarea text
        // matches the background and the user can't see what they're
        // typing (the bug the user reported on May 19 2026).
        //
        // We fix this defensively here rather than relying on the theme
        // because (a) themes routinely add CSS and we can't audit all
        // of them, and (b) site admins may add their own block-editor
        // styles. Strategy: pin our meta-box fields to the WP-admin
        // canonical color tokens with !important so no upstream cascade
        // can hide the text. We scope to `.wpc-cpt-meta-table` so we
        // never touch other plugins' UIs.
        $css = <<<'CSS'
.wpc-cpt-meta-table input.wpc-cpt-field,
.wpc-cpt-meta-table textarea.wpc-cpt-field,
.wpc-cpt-meta-table select.wpc-cpt-field {
    color: #2c3338 !important;
    background-color: #ffffff !important;
    -webkit-text-fill-color: #2c3338 !important;
    caret-color: #2c3338 !important;
    opacity: 1 !important;
    text-shadow: none !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif !important;
}
.wpc-cpt-meta-table textarea.wpc-cpt-field {
    min-height: 80px;
    line-height: 1.5 !important;
}
.wpc-cpt-meta-table input.wpc-cpt-field::placeholder,
.wpc-cpt-meta-table textarea.wpc-cpt-field::placeholder {
    color: #8c8f94 !important;
    -webkit-text-fill-color: #8c8f94 !important;
    opacity: 1 !important;
}
.wpc-cpt-meta-table input.wpc-cpt-field:focus,
.wpc-cpt-meta-table textarea.wpc-cpt-field:focus,
.wpc-cpt-meta-table select.wpc-cpt-field:focus {
    border-color: #2271b1 !important;
    box-shadow: 0 0 0 1px #2271b1 !important;
    outline: 2px solid transparent !important;
}
.wpc-cpt-meta-table .wpc-cpt-image-preview img {
    background-color: #f0f0f1 !important;
}
CSS;
        // Inline-style hook: attach to the WP admin's own stylesheet so
        // we're guaranteed to land AFTER it, beating any theme-supplied
        // editor-styles in the cascade.
        if ( function_exists( 'wp_add_inline_style' ) ) {
            // `wp-admin` is always registered on admin screens.
            wp_add_inline_style( 'wp-admin', $css );
        }

        // Inline JS for the image picker. Pure jQuery (already loaded by
        // wp-admin) so no extra HTTP request.
        $js = <<<'JS'
jQuery(function($){
  $(document).on('click', '.wpc-cpt-image-pick', function(e){
    e.preventDefault();
    var $btn = $(this);
    var $wrap = $btn.closest('.wpc-cpt-image-field');
    var $hidden = $wrap.find('input.wpc-cpt-field-image');
    var $preview = $wrap.find('.wpc-cpt-image-preview');
    var $remove = $wrap.find('.wpc-cpt-image-remove');
    var frame = wp.media({ title: 'Select image', button: { text: 'Use this image' }, multiple: false, library: { type: 'image' } });
    frame.on('select', function(){
      var att = frame.state().get('selection').first().toJSON();
      $hidden.val(att.id);
      var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
      $preview.html('<img src="' + url + '" style="max-width:100%;height:auto;border:1px solid #c3c4c7;border-radius:3px;">');
      $remove.show();
    });
    frame.open();
  });
  $(document).on('click', '.wpc-cpt-image-remove', function(e){
    e.preventDefault();
    var $wrap = $(this).closest('.wpc-cpt-image-field');
    $wrap.find('input.wpc-cpt-field-image').val('');
    $wrap.find('.wpc-cpt-image-preview').html('<span style="display:inline-block;padding:1em;color:#646970;background:#f0f0f1;border:1px dashed #c3c4c7;border-radius:3px;font-size:0.85em;">No image</span>');
    $(this).hide();
  });
});
JS;
        if ( function_exists( 'wp_add_inline_script' ) ) {
            wp_add_inline_script( 'jquery-core', $js );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_enqueue_admin_assets: ' . $e->getMessage() );
        }
    }
}

/* ─────────────────────────────────────────────
 * 13.6 FRONT-END LOOP SWAP  (Ship 4b)
 *
 * Hooks `wpconvert_editor_pre_apply_overrides` (added in
 * wpconvert-editor.php) to expand each stamped CPT section in the
 * response buffer.
 *
 * Flow per section:
 *   1) Find the wrapper containing `data-wpc-cpt-candidate="<key>"`
 *   2) Look up which slug owns that key (via wp_options['wpconvert_cpts'])
 *   3) WP_Query the CPT
 *   4) If 0 posts → keep static markup (empty-state)
 *   5) Otherwise → clone the `data-wpc-cpt-item-template="1"` element
 *      once per post, substitute fields via [data-wpc-cpt-field] stamps,
 *      strip per-item `data-wpc-id` (so editor regex doesn't re-edit
 *      them), then replace ALL static children with the N rendered items
 *
 * Safety invariants enforced by every branch:
 *   - No-CPT-activated case is byte-identical to input
 *   - Starter case is byte-identical (tier gate)
 *   - AJAX / REST / RSS / sitemap responses untouched
 *   - Buffer with no `data-wpc-cpt-candidate=` marker is untouched
 *   - Any exception caught → original buffer returned
 *   - DOMDocument is run ONLY on the per-section substring; everything
 *     outside the stamped sections is preserved byte-identical via
 *     string splicing
 * ───────────────────────────────────────────── */

/**
 * Top-level filter callback. Receives the full response buffer, returns
 * a (possibly-expanded) buffer.
 *
 * @param string $buffer
 * @return string
 */
/**
 * Ship 4b.2 self-heal — for any cfg in $active that's missing the `fields`
 * schema (legacy activations from before the `$candidate` capture bug was
 * fixed), look up the matching candidate in the manifest and patch the cfg
 * in-place. Persists the patched array back to `wp_options` only when
 * something actually changed, so the steady-state cost is one strpos check
 * per page.
 *
 * @param array $active Current wp_options['wpconvert_cpts'] map.
 * @return array        Patched map (may be the same instance).
 */
function wpconvert_cpt_heal_missing_fields_schema( $active ) {
    if ( ! is_array( $active ) || empty( $active ) ) return $active;
    $needs_heal = false;
    foreach ( $active as $sk => $cfg ) {
        if ( ! is_array( $cfg ) ) continue;
        if ( empty( $cfg['fields'] ) || ! is_array( $cfg['fields'] ) ) {
            $needs_heal = true;
            break;
        }
    }
    if ( ! $needs_heal ) return $active;

    if ( ! function_exists( 'wpconvert_cpt_get_candidates_manifest' ) ) return $active;
    $manifest = wpconvert_cpt_get_candidates_manifest();
    if ( ! is_array( $manifest ) || empty( $manifest ) ) return $active;

    // Index manifest by section_key for O(1) lookup.
    $by_key = array();
    foreach ( $manifest as $c ) {
        if ( ! is_array( $c ) ) continue;
        if ( empty( $c['section_key'] ) ) continue;
        $by_key[ (string) $c['section_key'] ] = $c;
    }

    $dirty = false;
    foreach ( $active as $sk => $cfg ) {
        if ( ! is_array( $cfg ) ) continue;
        if ( ! empty( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) continue;
        if ( ! isset( $by_key[ (string) $sk ] ) ) continue;
        $candidate = $by_key[ (string) $sk ];
        if ( empty( $candidate['fields'] ) || ! is_array( $candidate['fields'] ) ) continue;

        $trimmed = array();
        foreach ( $candidate['fields'] as $f ) {
            if ( ! is_array( $f ) || empty( $f['key'] ) ) continue;
            $entry = array(
                'key'         => (string) $f['key'],
                'type'        => isset( $f['type'] ) ? (string) $f['type'] : 'unknown',
                'remapped_to' => isset( $f['remapped_to'] ) ? (string) $f['remapped_to'] : (string) $f['key'],
            );
            if ( ! empty( $f['enum'] ) && is_array( $f['enum'] ) ) {
                $entry['enum'] = array_values( array_slice( $f['enum'], 0, 50 ) );
            }
            $trimmed[] = $entry;
        }
        if ( empty( $trimmed ) ) continue;

        $active[ $sk ]['fields'] = $trimmed;
        $dirty = true;
    }

    if ( $dirty && function_exists( 'update_option' ) ) {
        update_option( 'wpconvert_cpts', $active, false );
    }
    return $active;
}

function wpconvert_cpt_expand_loop_swap( $buffer ) {
    if ( ! is_string( $buffer ) || $buffer === '' ) return $buffer;

    // Non-HTML response guard. AJAX/REST/RSS/sitemap all share this
    // ob_start filter chain in WordPress; the loop swap MUST NOT touch
    // them or it will mangle valid JSON/XML.
    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return $buffer;
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return $buffer;
    if ( function_exists( 'is_feed' ) && is_feed() ) return $buffer;

    // Tier gate. Starter conversions never expand the loop.
    if ( ! function_exists( 'wpconvert_editor_is_pro' ) ) return $buffer;
    if ( ! wpconvert_editor_is_pro() ) return $buffer;

    // Fast bailout: if the buffer has no CPT markers at all, this is
    // the common case (Starter pages, admin pages, the no-activated
    // case). Cheap strpos avoids paying the DOMDocument cost.
    if ( strpos( $buffer, 'data-wpc-cpt-candidate=' ) === false ) return $buffer;

    try {
        $active = get_option( 'wpconvert_cpts', array() );
        if ( ! is_array( $active ) || empty( $active ) ) return $buffer;

        // Ship 4b.2 self-heal — patch any cfg that's missing `fields`. This
        // covers users who activated their CPT before the Ship 4a $candidate
        // bug was fixed: their wp_options row was written with an empty
        // schema, breaking Ship 4b.2's filter validation (and Ship 4a's
        // enum dropdowns). One read of the manifest, one update_option call,
        // then we move on — subsequent page loads skip the patcher because
        // all cfgs already have `fields`.
        $active = wpconvert_cpt_heal_missing_fields_schema( $active );

        // Build section_key → cfg map, but only for ENABLED entries.
        //
        // Ship 4c.0 hotfix — earlier code read the wrong keys (`active`,
        // `section_key`, `slug`) which never matched the CANONICAL shape that
        // the activation flow writes into `wp_options['wpconvert_cpts']`:
        //
        //   wp_options['wpconvert_cpts'] = [
        //     '<section_key_sha1>' => [
        //        'enabled'   => true,         // activation flag
        //        'post_type' => '<slug>',     // CPT slug (what register_post_type uses)
        //        // section_key is the array KEY, not a value field
        //        ...
        //     ],
        //   ];
        //
        // This loop now reads exactly what activation writes. EC-CPT-005f
        // pins the contract end-to-end.
        $section_to_cfg = array();
        foreach ( $active as $section_key => $cfg ) {
            if ( ! is_array( $cfg ) ) continue;
            if ( empty( $cfg['enabled'] ) ) continue;
            if ( ! is_string( $section_key ) ) continue;
            // section_key MUST be the 40-char sha1 the detector emits.
            if ( ! preg_match( '/^[a-f0-9]{40}$/', $section_key ) ) continue;
            $slug = isset( $cfg['post_type'] ) ? (string) $cfg['post_type'] : '';
            if ( $slug === '' ) continue;
            $section_to_cfg[ $section_key ] = array(
                'slug' => $slug,
                'cfg'  => $cfg,
            );
        }
        if ( empty( $section_to_cfg ) ) return $buffer;

        // Walk the buffer, finding+replacing one section at a time.
        $offset = 0;
        $iters = 0;
        $max_iters = 200; // hard upper bound; defensive guardrail
        while ( $iters++ < $max_iters ) {
            $next_match = wpconvert_cpt_find_next_section( $buffer, $offset, $section_to_cfg );
            if ( $next_match === null ) break;

            $bounds = wpconvert_cpt_find_section_bounds(
                $buffer, $next_match['marker_pos']
            );
            if ( $bounds === null ) {
                // Couldn't resolve open/close tags — skip past this marker
                $offset = $next_match['marker_pos'] + 40;
                continue;
            }

            $section_html = substr( $buffer, $bounds['start'], $bounds['end'] - $bounds['start'] );
            $sc = $section_to_cfg[ $next_match['section_key'] ];
            $new_section = wpconvert_cpt_process_section(
                $section_html, $sc['slug'], $sc['cfg']
            );

            if ( $new_section === null || ! is_string( $new_section ) || $new_section === '' ) {
                // Processing failed; preserve static markup
                $offset = $bounds['end'];
                continue;
            }

            // Splice the new section back in
            $buffer = substr( $buffer, 0, $bounds['start'] )
                . $new_section
                . substr( $buffer, $bounds['end'] );
            $offset = $bounds['start'] + strlen( $new_section );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_expand_loop_swap: ' . $e->getMessage() );
        }
        // Whatever went wrong, return the ORIGINAL buffer (the unsplied
        // copy is what we have at function entry, which is the parameter).
        // But we may have already partially-modified it. Return what we
        // have so far — the static fallback is the partially-modified
        // buffer (worst case: some sections expanded, others static).
        return $buffer;
    }

    return $buffer;
}

/**
 * Find the next occurrence in the buffer (starting at $offset) of a
 * `data-wpc-cpt-candidate="<key>"` where <key> is in our active map.
 *
 * @param string $buffer
 * @param int    $offset
 * @param array  $section_to_cfg
 * @return array|null  ['marker_pos' => int, 'section_key' => string] or null
 */
function wpconvert_cpt_find_next_section( $buffer, $offset, $section_to_cfg ) {
    $best_pos = -1;
    $best_key = null;
    foreach ( $section_to_cfg as $section_key => $sc ) {
        $needle = 'data-wpc-cpt-candidate="' . $section_key . '"';
        $pos = strpos( $buffer, $needle, $offset );
        if ( $pos === false ) continue;
        if ( $best_pos < 0 || $pos < $best_pos ) {
            $best_pos = $pos;
            $best_key = $section_key;
        }
    }
    if ( $best_pos < 0 ) return null;
    return array( 'marker_pos' => $best_pos, 'section_key' => $best_key );
}

/**
 * Given the position of the marker INSIDE the opening tag attributes,
 * return the start/end offsets of the entire wrapping element
 * (open tag through matching close tag).
 *
 * Depth tracking handles nested elements of the same tag inside.
 *
 * @param string $buffer
 * @param int    $marker_pos  Position of the data-wpc-cpt-candidate=...
 * @return array|null  ['start' => int, 'end' => int (exclusive)] or null
 */
function wpconvert_cpt_find_section_bounds( $buffer, $marker_pos ) {
    // Walk backwards to find the '<' that starts the open tag
    $open_start = false;
    for ( $i = $marker_pos; $i >= 0 && $i > $marker_pos - 200; $i-- ) {
        if ( $buffer[ $i ] === '<' ) {
            $open_start = $i;
            break;
        }
    }
    if ( $open_start === false ) return null;

    // Walk forwards to find the '>' that closes the open tag
    $open_end = strpos( $buffer, '>', $marker_pos );
    if ( $open_end === false ) return null;

    // Extract the tag name
    $open_tag = substr( $buffer, $open_start, $open_end - $open_start + 1 );
    if ( ! preg_match( '/^<\s*([A-Za-z][A-Za-z0-9]*)/', $open_tag, $m ) ) return null;
    $tag = strtolower( $m[1] );

    // Self-closing? (`<tag … />`). Shouldn't happen for a CPT wrapper but
    // handle defensively.
    if ( substr( rtrim( $open_tag, '>' ), -1 ) === '/' ) {
        return array( 'start' => $open_start, 'end' => $open_end + 1 );
    }

    // Depth-track to the matching close tag.
    $depth = 1;
    $scan = $open_end + 1;
    $open_re = '/<\s*' . preg_quote( $tag, '/' ) . '\b[^>]*>/i';
    $close_re = '/<\s*\/\s*' . preg_quote( $tag, '/' ) . '\s*>/i';
    $end = null;
    $depth_iters = 0;
    while ( $depth > 0 && $depth_iters++ < 1000 ) {
        $next_open_pos = false;
        $next_open_len = 0;
        if ( preg_match( $open_re, $buffer, $om, PREG_OFFSET_CAPTURE, $scan ) ) {
            $next_open_pos = $om[0][1];
            $next_open_len = strlen( $om[0][0] );
            // Skip self-closing matches
            if ( substr( rtrim( $om[0][0], '>' ), -1 ) === '/' ) {
                $scan = $next_open_pos + $next_open_len;
                continue;
            }
        }
        $next_close_pos = false;
        $next_close_len = 0;
        if ( preg_match( $close_re, $buffer, $cm, PREG_OFFSET_CAPTURE, $scan ) ) {
            $next_close_pos = $cm[0][1];
            $next_close_len = strlen( $cm[0][0] );
        }
        if ( $next_close_pos === false ) return null; // unclosed

        if ( $next_open_pos !== false && $next_open_pos < $next_close_pos ) {
            $depth++;
            $scan = $next_open_pos + $next_open_len;
        } else {
            $depth--;
            if ( $depth === 0 ) {
                $end = $next_close_pos + $next_close_len;
            } else {
                $scan = $next_close_pos + $next_close_len;
            }
        }
    }
    if ( $end === null ) return null;
    return array( 'start' => $open_start, 'end' => $end );
}

/**
 * Parse the section's HTML with DOMDocument, find the template item,
 * clone it once per post, substitute fields, and serialize back.
 *
 * @param string $section_html
 * @param string $slug
 * @param array  $cfg
 * @return string|null  Modified section HTML, or null on failure.
 */
function wpconvert_cpt_process_section( $section_html, $slug, $cfg ) {
    if ( ! class_exists( '\DOMDocument' ) ) return null;
    if ( ! class_exists( '\DOMXPath' ) ) return null;
    if ( ! function_exists( 'get_posts' ) ) return null;

    /*
     * Ship 4b.2 — filtered loops. The detector stamps each grid that
     * iterates the candidate array via `.filter(i => i.X === "Y").map(...)`
     * with `data-wpc-cpt-filter-field="X"` + `data-wpc-cpt-filter-value="Y"`.
     * We read those attrs off the wrapper open tag BEFORE invoking the
     * query so that each grid renders only its matching subset. Field
     * names are validated against $cfg['fields'] (anything else is dropped
     * silently — defense against stale/malformed stamps).
     */
    $filter_field = '';
    $filter_value = '';
    if ( preg_match( '/<[^>]*data-wpc-cpt-filter-field="([^"]*)"[^>]*>/', $section_html, $mf ) ) {
        $filter_field = (string) $mf[1];
    }
    if ( preg_match( '/<[^>]*data-wpc-cpt-filter-value="([^"]*)"[^>]*>/', $section_html, $mv ) ) {
        // Decode any HTML entities we might have stamped (e.g. & → &amp;)
        // so the postmeta comparison uses the raw text.
        $filter_value = html_entity_decode( (string) $mv[1], ENT_QUOTES, 'UTF-8' );
    }
    // Validate filter_field against cfg fields list — only allow filtering
    // on a field the CPT knows about.
    $valid_filter = false;
    if ( $filter_field !== '' && isset( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) {
        foreach ( $cfg['fields'] as $fdef ) {
            if ( ! is_array( $fdef ) ) continue;
            $fk = isset( $fdef['key'] ) ? (string) $fdef['key'] : '';
            $rk = isset( $fdef['remapped_to'] ) ? (string) $fdef['remapped_to'] : '';
            if ( $fk === $filter_field || $rk === $filter_field ) {
                $valid_filter = true;
                break;
            }
        }
    }

    // Defensive: cap the per-section query size. Real templates have a
    // few static items; the CPT can have many posts but we don't want
    // to render thousands of items on a single page request without a
    // cache. Ship 4b.1 will add transient caching + pagination; for
    // now, cap at 500 to prevent runaway page renders.
    $max_posts = 500;
    $query_args = array(
        'post_type'      => $slug,
        'post_status'    => 'publish',
        'posts_per_page' => $max_posts,
        'orderby'        => 'menu_order ID',
        'order'          => 'ASC',
        'suppress_filters' => false,
        'no_found_rows'  => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    );
    if ( $valid_filter ) {
        // Ship 4c.1 hotfix #4 — query ONLY the canonical key for the
        // current mode. Previously (hotfix #3) we OR'd the ACF and
        // legacy keys, but that caused DUPLICATES: a post whose user-
        // edited ACF value moved from 'sød' → 'salt' would still match
        // the 'sød' filter via the stale legacy `_wpc_field_category`,
        // appearing in BOTH grids. Single-key query is safe because:
        //
        //   • When acf_managed=true: the importer dual-writes both keys
        //     and the heal hook backfills any missed posts — so ACF is
        //     the source of truth and is always populated.
        //   • When acf_managed=false: only the legacy key is written,
        //     and ACF is irrelevant.
        if ( ! empty( $cfg['metabox_managed'] ) && wpconvert_cpt_metabox_available() ) {
            // EC-CPT-008 — Meta Box stores values under the BARE field id, so
            // the canonical filter key is the remapped/bare id (same as the
            // ACF name resolution, which returns the bare key for our shapes).
            $meta_key = wpconvert_cpt_acf_field_name_for_key( $filter_field, $cfg );
        } elseif ( ! empty( $cfg['acf_managed'] ) && wpconvert_cpt_acf_available() ) {
            $meta_key = wpconvert_cpt_acf_field_name_for_key( $filter_field, $cfg );
        } else {
            $meta_key = wpconvert_cpt_meta_key_for_field( $filter_field );
        }
        $query_args['meta_query'] = array(
            array( 'key' => $meta_key, 'value' => $filter_value, 'compare' => '=' ),
        );
    }
    $posts = get_posts( $query_args );

    if ( empty( $posts ) ) {
        // Empty-state: keep static markup unchanged. This is the
        // user-friendly default — if the CPT has no posts yet, the
        // visitor still sees the original React-rendered items rather
        // than a blank section.
        return $section_html;
    }

    $dom = new \DOMDocument();
    // Suppress libxml errors; HTML in the wild is rarely strict-valid.
    $prev_use_errors = libxml_use_internal_errors( true );
    // `loadHTML` defaults to ISO-8859-1; force UTF-8 with the encoding
    // hint trick. LIBXML_HTML_NOIMPLIED + LIBXML_HTML_NODEFDTD avoid
    // DOMDocument wrapping the fragment in <html><body> on serialization.
    $ok = $dom->loadHTML(
        '<?xml encoding="UTF-8"?>' . $section_html,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $prev_use_errors );
    if ( ! $ok ) return null;

    $xpath = new \DOMXPath( $dom );

    // Find the wrapper (has data-wpc-cpt-candidate)
    $wrappers = $xpath->query( '//*[@data-wpc-cpt-candidate]' );
    if ( ! $wrappers || $wrappers->length === 0 ) return null;
    $wrapper = $wrappers->item( 0 );

    // Find the template item inside the wrapper
    $templates = $xpath->query( './/*[@data-wpc-cpt-item-template="1"]', $wrapper );
    if ( ! $templates || $templates->length === 0 ) return null;
    $template = $templates->item( 0 );

    // Render each post by cloning the template and substituting.
    $new_items = array();
    foreach ( $posts as $post ) {
        $clone = $template->cloneNode( true );
        if ( ! ( $clone instanceof \DOMElement ) ) continue;
        // Strip per-item template marker on the clone.
        $clone->removeAttribute( 'data-wpc-cpt-item-template' );
        wpconvert_cpt_substitute_fields_on_node( $clone, $xpath, $post, $cfg );
        // EC-CPT-LINK-001 — point each cloned card's detail link at its own page.
        wpconvert_cpt_rewrite_slug_links( $clone, $xpath, $post, $cfg );
        wpconvert_cpt_strip_per_item_editor_ids( $clone, $xpath );
        wpconvert_cpt_strip_field_markers( $clone, $xpath );
        $new_items[] = $clone;
    }
    if ( empty( $new_items ) ) return null;

    // Remove every existing direct child of the wrapper (template + the
    // other static cards) and replace with the rendered items.
    while ( $wrapper->firstChild ) {
        $wrapper->removeChild( $wrapper->firstChild );
    }
    foreach ( $new_items as $item ) {
        $wrapper->appendChild( $item );
    }

    // Serialize back. With NOIMPLIED + NODEFDTD, DOMDocument emits the
    // fragment without <html>/<body>/doctype wrappers. We also strip
    // any residual xml processing instruction at the start.
    $serialized = $dom->saveHTML();
    if ( ! is_string( $serialized ) ) return null;
    // Trim the leading "xml encoding" PI if DOMDocument kept it.
    $serialized = preg_replace( '/^<\?xml[^?]*\?>\s*/', '', $serialized );
    // Trim trailing whitespace/newlines that DOMDocument loves to add.
    $serialized = rtrim( $serialized, "\r\n" );
    // Ship 4c.7 / B3 — restore camelCase SVG attribute names that
    // DOMDocument lowercased while in HTML4 parsing mode (viewBox →
    // viewbox, etc.). SVG is case-sensitive — without this, every
    // inline icon inside a CPT-swapped section renders at intrinsic
    // size or breaks outright.
    $serialized = wpconvert_cpt_restore_svg_attr_case( $serialized );
    if ( $serialized === '' || $serialized === null ) return null;
    return $serialized;
}

/**
 * Ship 4c.7 / B3 — restore camelCase SVG attribute names after the
 * DOMDocument HTML4 parser lowercased them.
 *
 * Why this is necessary: PHP's libxml is an HTML4 parser. HTML4
 * lowercases all attribute names. SVG is XML — case-sensitive —
 * so `viewBox`, `preserveAspectRatio`, `gradientUnits`, etc. become
 * `viewbox`, `preserveaspectratio`, `gradientunits`, which the
 * browser silently ignores. Result: inline SVG icons render at
 * intrinsic 24×24 pixels (or 0×0) inside what should be a 28×28
 * Tailwind icon slot. Visually, icons "disappear" after loop swap
 * runs.
 *
 * We solve this in post-serialization with a regex sweep: each known
 * SVG camelCase attribute is matched in its lowercased form when it
 * appears as an attribute name (`<...lowered=...>`), then replaced
 * with its canonical camelCase. The match is anchored to a word
 * boundary on the left + an equals/whitespace on the right so we
 * don't accidentally rewrite text content or class names that
 * coincidentally contain the same substring.
 *
 * List source: SVG 1.1 / SVG 2 attribute reference, scoped to
 * attributes that actually appear in real-world content (icon
 * libraries: Lucide, Heroicons, Phosphor, Tabler; chart libraries:
 * Chart.js, D3 outputs; logo SVGs).
 *
 * @param string $html Serialized DOMDocument output.
 * @return string
 */
function wpconvert_cpt_restore_svg_attr_case( $html ) {
    if ( ! is_string( $html ) || $html === '' ) return $html;
    if ( strpos( $html, '<svg' ) === false && strpos( $html, '<SVG' ) === false ) {
        // Cheap bail — no SVG, no need to restore. Almost all converted
        // themes have inline SVGs (Lucide is the default), but this still
        // helps when a candidate section happens to have none.
        return $html;
    }

    static $map = null;
    if ( $map === null ) {
        $camel = array(
            'viewBox', 'preserveAspectRatio',
            'gradientUnits', 'gradientTransform',
            'patternUnits', 'patternTransform', 'patternContentUnits',
            'spreadMethod',
            'clipPath', 'clipPathUnits',
            'maskUnits', 'maskContentUnits',
            'filterUnits', 'primitiveUnits',
            'lengthAdjust', 'textLength',
            'requiredFeatures', 'requiredExtensions', 'systemLanguage',
            'baseProfile', 'contentScriptType', 'contentStyleType',
            'externalResourcesRequired', 'zoomAndPan',
            'attributeName', 'attributeType',
            'calcMode', 'keyTimes', 'keySplines',
            'keyPoints', 'repeatCount', 'repeatDur',
            'fillRule',
            'pointsAtX', 'pointsAtY', 'pointsAtZ',
            'kernelMatrix', 'kernelUnitLength',
            'surfaceScale', 'specularConstant', 'specularExponent',
            'diffuseConstant', 'limitingConeAngle',
            'stdDeviation', 'stitchTiles', 'baseFrequency', 'numOctaves',
            'tableValues', 'targetX', 'targetY',
            'edgeMode', 'xChannelSelector', 'yChannelSelector',
            'startOffset', 'pathLength',
            'markerHeight', 'markerWidth', 'markerUnits',
            'refX', 'refY',
            // SMIL / animation, mostly safe to include even if rare.
            'accumulate', 'additive', 'restart',
        );
        $map = array();
        foreach ( $camel as $c ) {
            $map[ strtolower( $c ) ] = $c;
        }
    }

    // One pass with an alternation regex over all known lowercased
    // attribute names. Anchored to `<tag …` context: must be preceded by
    // a space (attribute separator) and followed by `=`, optional spaces,
    // then quote — that's the unmistakable shape of an HTML attribute.
    // We deliberately don't try to scope to <svg> or its descendants
    // (DOMDocument doesn't preserve namespace, and matching every nested
    // element would be brittle) — the attribute name uniqueness alone is
    // enough to avoid collateral damage on non-SVG content. None of the
    // names in $map are valid HTML attributes outside SVG.
    $lowered_names = implode( '|', array_map( function( $k ) {
        return preg_quote( $k, '/' );
    }, array_keys( $map ) ) );
    $pattern = '/(\s)(' . $lowered_names . ')(\s*=\s*[\'"])/i';

    $html = preg_replace_callback( $pattern, function( $m ) use ( $map ) {
        $lc = strtolower( $m[2] );
        $canonical = isset( $map[ $lc ] ) ? $map[ $lc ] : $m[2];
        return $m[1] . $canonical . $m[3];
    }, $html );

    return is_string( $html ) ? $html : '';
}

/**
 * For each element with data-wpc-cpt-field inside $node, replace its
 * content / attribute with the post's meta value for that field.
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @param \WP_Post    $post
 * @param array       $cfg
 * @return void
 */
function wpconvert_cpt_substitute_fields_on_node( $node, $xpath, $post, $cfg ) {
    $field_nodes = $xpath->query( './/*[@data-wpc-cpt-field]', $node );
    if ( ! $field_nodes || $field_nodes->length === 0 ) return;

    // Build a quick lookup of cfg fields by key for type info.
    $fields_by_key = array();
    if ( ! empty( $cfg['fields'] ) && is_array( $cfg['fields'] ) ) {
        foreach ( $cfg['fields'] as $f ) {
            if ( empty( $f['key'] ) ) continue;
            $fields_by_key[ (string) $f['key'] ] = $f;
        }
    }

    foreach ( $field_nodes as $el ) {
        if ( ! ( $el instanceof \DOMElement ) ) continue;
        $key = $el->getAttribute( 'data-wpc-cpt-field' );
        if ( $key === '' ) continue;

        $meta_key = wpconvert_cpt_meta_key_for_field( $key );
        // Ship 4c.1 — dual-read: prefer ACF when cfg.acf_managed + ACF
        // available, fall back to our `_wpc_field_*` meta otherwise.
        $value = wpconvert_cpt_read_field_value( $post, $key, $meta_key, $cfg );

        // EC-CPT-ICON-001 — HTML/markup field (per-item icon SVG). The stamped
        // element is REPLACED by the stored markup so each cloned card shows its
        // own icon instead of the template card's. Guarded by the explicit
        // data-wpc-cpt-html marker so ordinary text/href/image fields are
        // unaffected. Empty markup → leave the template icon in place.
        if ( $el->getAttribute( 'data-wpc-cpt-html' ) === '1' ) {
            $markup = is_string( $value ) ? trim( $value ) : '';
            if ( $markup !== '' && $el->parentNode ) {
                $frag = wpconvert_cpt_import_html_fragment( $el->ownerDocument, $markup );
                if ( $frag && $frag->childNodes && $frag->childNodes->length > 0 ) {
                    $el->parentNode->replaceChild( $frag, $el );
                }
            }
            continue;
        }

        $type = isset( $fields_by_key[ $key ]['type'] )
            ? (string) $fields_by_key[ $key ]['type']
            : 'text-short';

        // For image fields: update alt to the post title even if the
        // meta value is empty (so accessible tools describe the right
        // post, not the template item). src substitution still requires
        // a non-empty meta value.
        if ( $type === 'image' ) {
            $alt = isset( $post->post_title ) ? (string) $post->post_title : '';
            if ( $alt !== '' && $el->hasAttribute( 'alt' ) ) {
                $el->setAttribute( 'alt', $alt );
            }
        }

        if ( $type === 'image' ) {
            // Ship 4c.1 hotfix #3 — robust image resolution. Handle every
            // value shape we might see in the wild:
            //
            //   (a) Numeric attachment ID  → wp_get_attachment_url()
            //   (b) Full URL or theme path → use as-is
            //   (c) Raw identifier token   → wpconvert_cpt_resolve_image_to_attachment_id()
            //   (d) Empty / 0 / unresolvable → fall back to this post's
            //                                  _thumbnail_id (the featured
            //                                  image, which the importer
            //                                  always sets if it could)
            //
            // Critical for ACF-managed posts where the user might have
            // cleared the image field (saves as `0`) or the dual-write
            // never resolved a non-zero ID at import time. Without this
            // fallback the front-end goes blank for those posts.
            $url = '';
            $att_id = 0;
            $val_str = is_scalar( $value ) ? (string) $value : '';

            if ( ctype_digit( $val_str ) ) {
                $att_id = (int) $val_str;
            } elseif ( $val_str !== ''
                && ! filter_var( $val_str, FILTER_VALIDATE_URL )
                && strpos( $val_str, '/' ) !== 0
                && function_exists( 'wpconvert_cpt_resolve_image_to_attachment_id' ) ) {
                // Raw token like "pancakeBacon" — try resolving via the
                // theme's assets/images directory.
                $att_id = (int) wpconvert_cpt_resolve_image_to_attachment_id( $val_str );
            } elseif ( $val_str !== '' ) {
                // Already a URL or a theme-relative path.
                $url = $val_str;
            }

            // Defensive fallback to _thumbnail_id when nothing resolved.
            if ( $att_id <= 0 && $url === '' && function_exists( 'get_post_meta' ) ) {
                $att_id = (int) get_post_meta( $post->ID, '_thumbnail_id', true );
            }

            if ( $att_id > 0 ) {
                // Ship 4c.1 hotfix #4 — safe resolver. Handles theme-
                // attached files (theme dir, not uploads) and rejects
                // WP's known malformed URL shape.
                $resolved = function_exists( 'wpconvert_cpt_get_attachment_url_safe' )
                    ? wpconvert_cpt_get_attachment_url_safe( $att_id )
                    : ( function_exists( 'wp_get_attachment_url' ) ? wp_get_attachment_url( $att_id ) : '' );
                if ( is_string( $resolved ) && $resolved !== '' ) $url = $resolved;
            }

            // Only update if we actually have a sane URL or path.
            if ( $url !== ''
                && ( filter_var( $url, FILTER_VALIDATE_URL ) || strpos( $url, '/' ) === 0 ) ) {
                if ( $el->hasAttribute( 'src' ) ) {
                    $el->setAttribute( 'src', $url );
                }
                if ( $el->hasAttribute( 'srcset' ) ) {
                    // Drop srcset since the new image won't have matching
                    // responsive variants. Browser falls back to src.
                    $el->removeAttribute( 'srcset' );
                }
            }
            continue;
        }

        if ( $value === '' || $value === null ) continue;

        $new_value = (string) $value;

        // Ship 4c.7 / B5 — when the stamped element is an anchor or
        // media element AND the value looks URL-shaped, write the
        // attribute (href / src) instead of replacing the visible
        // text. Without this the runtime would clobber "Learn more"
        // anchor text with a raw URL and leave the original (template-
        // hardcoded) href in place — exactly the opposite of intent.
        $tag = strtolower( $el->nodeName );
        if ( $tag === 'a' && wpconvert_cpt_is_url_like_value( $new_value ) ) {
            if ( $el->hasAttribute( 'href' ) || $el->hasAttribute( 'data-href' ) ) {
                if ( $el->hasAttribute( 'href' ) ) {
                    $el->setAttribute( 'href', $new_value );
                }
                if ( $el->hasAttribute( 'data-href' ) ) {
                    $el->setAttribute( 'data-href', $new_value );
                }
                continue;
            }
        }
        if ( ( $tag === 'img' || $tag === 'source' ) && wpconvert_cpt_is_url_like_value( $new_value ) ) {
            if ( $el->hasAttribute( 'src' ) ) {
                $el->setAttribute( 'src', $new_value );
                continue;
            }
        }

        // Text-shaped field. Use data-wpc-cpt-original to know what
        // substring to replace inside the element's text content (so
        // format suffixes like " kr" are preserved).
        $original = $el->getAttribute( 'data-wpc-cpt-original' );

        if ( $original === '' ) {
            // No original recorded — replace entire text content.
            wpconvert_cpt_replace_element_text( $el, $new_value );
        } else {
            // Replace the original substring inside the element's
            // descendant text nodes. If not found (template drifted),
            // fall back to replacing the entire text content.
            $replaced = wpconvert_cpt_replace_text_in_node(
                $el, $original, $new_value
            );
            if ( ! $replaced ) {
                wpconvert_cpt_replace_element_text( $el, $new_value );
            }
        }
    }

    // EC-CPT-023 — colour classes and bar geometry live in `class` / `style`,
    // never in text, so they need their own substitution pass.
    wpconvert_cpt_substitute_presentation_on_node( $node, $xpath, $post, $cfg );
}

/**
 * EC-CPT-023 — swap per-item PRESENTATION values on a cloned item.
 *
 * Source arrays often carry presentation alongside content, e.g.
 * `{ label:'Paid', count:128, tone:'bg-teal-50 text-teal-700',
 *    bar:'bg-[#0D9488]', pct:72 }`. Those values render as `class` tokens and
 * inline style declarations, so the text/href/image branches above can never
 * host them — every cloned row would keep the TEMPLATE row's colours and
 * geometry (the "all bars are the same green" symptom).
 *
 * The detector stamps an unambiguous host element with either
 *   data-wpc-cpt-class-field + data-wpc-cpt-class-original   (class tokens), or
 *   data-wpc-cpt-style-field + data-wpc-cpt-style-prop
 *                            + data-wpc-cpt-style-original   (one declaration).
 *
 * Only the recorded template tokens / declaration are touched; structural
 * classes and every other style declaration are preserved. An empty or missing
 * meta value leaves the element exactly as the template rendered it.
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @param \WP_Post    $post
 * @param array       $cfg
 * @return void
 */
function wpconvert_cpt_substitute_presentation_on_node( $node, $xpath, $post, $cfg ) {
    $hits = $xpath->query(
        './/*[@data-wpc-cpt-class-field or @data-wpc-cpt-style-field]',
        $node
    );
    if ( ! $hits || $hits->length === 0 ) return;

    foreach ( $hits as $el ) {
        if ( ! ( $el instanceof \DOMElement ) ) continue;

        // ── class token swap ──────────────────────────────────────────
        $class_key = $el->getAttribute( 'data-wpc-cpt-class-field' );
        if ( $class_key !== '' ) {
            $value = wpconvert_cpt_read_field_value(
                $post, $class_key, wpconvert_cpt_meta_key_for_field( $class_key ), $cfg
            );
            $incoming = is_scalar( $value ) ? trim( (string) $value ) : '';
            if ( $incoming !== '' ) {
                $original = trim( $el->getAttribute( 'data-wpc-cpt-class-original' ) );
                $current  = preg_split( '/\s+/', trim( $el->getAttribute( 'class' ) ), -1, PREG_SPLIT_NO_EMPTY );
                $drop     = $original === ''
                    ? array()
                    : preg_split( '/\s+/', $original, -1, PREG_SPLIT_NO_EMPTY );
                $add      = preg_split( '/\s+/', $incoming, -1, PREG_SPLIT_NO_EMPTY );

                $kept = array();
                foreach ( (array) $current as $tok ) {
                    if ( in_array( $tok, (array) $drop, true ) ) continue;
                    $kept[] = $tok;
                }
                foreach ( (array) $add as $tok ) {
                    if ( ! in_array( $tok, $kept, true ) ) $kept[] = $tok;
                }
                $el->setAttribute( 'class', implode( ' ', $kept ) );
            }
        }

        // ── single style declaration swap ─────────────────────────────
        $style_key = $el->getAttribute( 'data-wpc-cpt-style-field' );
        if ( $style_key !== '' ) {
            $prop = strtolower( trim( $el->getAttribute( 'data-wpc-cpt-style-prop' ) ) );
            $orig = trim( $el->getAttribute( 'data-wpc-cpt-style-original' ) );
            if ( $prop !== '' && preg_match( '/^(-?[0-9.]+)([a-z%]*)$/i', $orig, $om ) ) {
                $unit  = $om[2];
                $value = wpconvert_cpt_read_field_value(
                    $post, $style_key, wpconvert_cpt_meta_key_for_field( $style_key ), $cfg
                );
                $incoming = is_scalar( $value ) ? trim( (string) $value ) : '';
                if ( $incoming !== '' && preg_match( '/^-?[0-9]+(\.[0-9]+)?$/', $incoming ) ) {
                    $decls = array();
                    foreach ( explode( ';', (string) $el->getAttribute( 'style' ) ) as $decl ) {
                        if ( trim( $decl ) === '' ) continue;
                        $parts = explode( ':', $decl, 2 );
                        if ( count( $parts ) !== 2 ) { $decls[] = trim( $decl ); continue; }
                        if ( strtolower( trim( $parts[0] ) ) === $prop ) {
                            $decls[] = $prop . ': ' . $incoming . $unit;
                        } else {
                            $decls[] = trim( $parts[0] ) . ': ' . trim( $parts[1] );
                        }
                    }
                    $el->setAttribute( 'style', implode( '; ', $decls ) . ';' );
                }
            }
        }
    }
}

/**
 * EC-CPT-ICON-001 — parse an HTML/SVG markup string and import its top-level
 * nodes into `$doc`, returning a DocumentFragment ready to insert. Used by the
 * loop-swap runtime to replace a stamped icon element with each post's own
 * captured icon markup.
 *
 * Uses a throwaway DOMDocument with the same UTF-8 + NOIMPLIED/NODEFDTD flags
 * as the section parser so no <html>/<body>/doctype wrappers leak in. SVG
 * attribute case (viewBox, etc.) is lowercased by libxml here but restored by
 * wpconvert_cpt_restore_svg_attr_case() on the final serialized output.
 *
 * @param \DOMDocument $doc   The destination document (owner of the target node).
 * @param string       $html  The markup to import.
 * @return \DOMDocumentFragment|null
 */
function wpconvert_cpt_import_html_fragment( $doc, $html ) {
    if ( ! ( $doc instanceof \DOMDocument ) ) return null;
    $html = (string) $html;
    if ( trim( $html ) === '' ) return null;
    if ( ! class_exists( '\DOMDocument' ) ) return null;

    $tmp = new \DOMDocument();
    $prev = libxml_use_internal_errors( true );
    $ok = $tmp->loadHTML(
        '<?xml encoding="UTF-8"?><div id="wpc-frag-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $prev );
    if ( ! $ok ) return null;

    // With NOIMPLIED the wrapping <div> is the document element.
    $wrap = $tmp->documentElement;
    if ( ! $wrap || ! $wrap->hasChildNodes() ) return null;

    $frag = $doc->createDocumentFragment();
    foreach ( iterator_to_array( $wrap->childNodes ) as $child ) {
        $imported = $doc->importNode( $child, true );
        if ( $imported ) {
            $frag->appendChild( $imported );
        }
    }
    return ( $frag->childNodes && $frag->childNodes->length > 0 ) ? $frag : null;
}

/**
 * Ship 4c.7 / B5 — PHP mirror of the JS `isUrlLikeValue` heuristic.
 * Used by the loop-swap substitute to decide whether a `<a>` /
 * `<img>` / `<source>` element's value should be written to the
 * `href` / `src` attribute (rather than replacing visible text).
 *
 * KEEP IN SYNC with `isUrlLikeValue` in cpt-candidate-detector.js.
 *
 * @param string $value
 * @return bool
 */
function wpconvert_cpt_is_url_like_value( $value ) {
    if ( ! is_string( $value ) || $value === '' ) return false;
    if ( preg_match( '/\s/', $value ) ) return false;
    if ( $value[0] === '/' || $value[0] === '#' ) return true;
    if ( strncmp( $value, './', 2 ) === 0 || strncmp( $value, '../', 3 ) === 0 ) return true;
    if ( preg_match( '#^https?://#i', $value ) ) return true;
    if ( stripos( $value, 'mailto:' ) === 0 ) return true;
    if ( stripos( $value, 'tel:' ) === 0 ) return true;
    return false;
}

/**
 * Walk text descendants of $node and replace the first occurrence of
 * $needle with $replacement. Returns true if a replacement happened.
 *
 * @param \DOMElement $node
 * @param string      $needle
 * @param string      $replacement
 * @return bool
 */
function wpconvert_cpt_replace_text_in_node( $node, $needle, $replacement ) {
    if ( $needle === '' ) return false;
    // Walk children depth-first
    $stack = array();
    foreach ( $node->childNodes as $child ) {
        $stack[] = $child;
    }
    while ( ! empty( $stack ) ) {
        $cur = array_shift( $stack );
        if ( $cur instanceof \DOMText ) {
            $data = $cur->nodeValue;
            if ( is_string( $data ) && strpos( $data, $needle ) !== false ) {
                // Replace ONE occurrence to keep idempotency under
                // repeated renders + handle "Free Free" edge cases.
                $pos = strpos( $data, $needle );
                $cur->nodeValue = substr( $data, 0, $pos )
                    . $replacement
                    . substr( $data, $pos + strlen( $needle ) );
                return true;
            }
        } elseif ( $cur instanceof \DOMElement ) {
            foreach ( $cur->childNodes as $grandchild ) {
                $stack[] = $grandchild;
            }
        }
    }
    return false;
}

/**
 * Replace ALL text content of $node with a single text node containing
 * $newValue. Discards any inner markup. Used as a fallback when the
 * original-value substring can't be located.
 *
 * @param \DOMElement $node
 * @param string      $newValue
 * @return void
 */
function wpconvert_cpt_replace_element_text( $node, $newValue ) {
    while ( $node->firstChild ) {
        $node->removeChild( $node->firstChild );
    }
    if ( $newValue !== '' ) {
        $node->appendChild( $node->ownerDocument->createTextNode( $newValue ) );
    }
}

/**
 * Strip every `data-wpc-id` attribute INSIDE the cloned item so the
 * editor's `data-wpc-id`-based regex doesn't try to re-apply the same
 * edit to all N rendered items (which would create N identical headings
 * even when only one was edited). The section-wrapper's data-wpc-id is
 * NOT touched — Ship 4b only operates on items inside the wrapper.
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @return void
 */
function wpconvert_cpt_strip_per_item_editor_ids( $node, $xpath ) {
    // The editor affordances we remove from every rendered item. data-wpc-id /
    // data-wpc-bg-id are the keys the WPConvert Editor saves/applies edits by;
    // data-wpc-editable is what makes an element show the inline edit UI.
    //
    // EC-CPT-EDITOR-016 — we ALSO strip data-wpc-editable (not just the ids).
    // The loop swap regenerates this region from the template on every render,
    // and clones lose their data-wpc-id, so a WPConvert Editor edit to any
    // element here (a non-field `.validity` date, an offer row, even a stamped
    // field) can be neither saved nor re-applied — it silently reverts on
    // reload. Removing the affordance means the editor never offers a control
    // that can't persist; CPT content is managed from the CPT screen instead.
    $attrs = array( 'data-wpc-id', 'data-wpc-bg-id', 'data-wpc-editable' );

    if ( $node instanceof \DOMElement ) {
        foreach ( $attrs as $a ) {
            if ( $node->hasAttribute( $a ) ) $node->removeAttribute( $a );
        }
    }
    $hits = $xpath->query(
        './/*[@data-wpc-id or @data-wpc-bg-id or @data-wpc-editable]', $node
    );
    if ( $hits ) {
        foreach ( $hits as $el ) {
            if ( ! ( $el instanceof \DOMElement ) ) continue;
            foreach ( $attrs as $a ) {
                if ( $el->hasAttribute( $a ) ) $el->removeAttribute( $a );
            }
        }
    }
}

/**
 * Strip the Ship 4b stamp attributes from rendered items (they're
 * runtime-only metadata and don't need to ship to the browser).
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @return void
 */
function wpconvert_cpt_strip_field_markers( $node, $xpath ) {
    $hits = $xpath->query(
        './/*[@data-wpc-cpt-field or @data-wpc-cpt-original'
        . ' or @data-wpc-cpt-href-field or @data-wpc-cpt-href-original'
        . ' or @data-wpc-cpt-class-field or @data-wpc-cpt-style-field]',
        $node
    );
    // EC-CPT-LINK-001 — defensively drop the href-rewrite markers too, in case
    // wpconvert_cpt_rewrite_slug_links didn't run for this render path.
    // EC-CPT-023 — same for the class/style presentation markers.
    $marker_attrs = array(
        'data-wpc-cpt-field', 'data-wpc-cpt-original',
        'data-wpc-cpt-href-field', 'data-wpc-cpt-href-original',
        'data-wpc-cpt-class-field', 'data-wpc-cpt-class-original',
        'data-wpc-cpt-style-field', 'data-wpc-cpt-style-prop',
        'data-wpc-cpt-style-original',
    );
    if ( $hits ) {
        foreach ( $hits as $el ) {
            if ( ! ( $el instanceof \DOMElement ) ) continue;
            foreach ( $marker_attrs as $a ) {
                if ( $el->hasAttribute( $a ) ) $el->removeAttribute( $a );
            }
        }
    }
    // Also strip on the node itself if present.
    if ( $node instanceof \DOMElement ) {
        foreach ( $marker_attrs as $a ) {
            if ( $node->hasAttribute( $a ) ) $node->removeAttribute( $a );
        }
    }
}

/**
 * EC-CPT-LINK-001 — point a cloned card's detail link at its OWN page.
 *
 * The general (non-WC) loop swap clones ONE template card per post and only
 * substitutes stamped `data-wpc-cpt-field` elements. On card grids whose card
 * (or "Learn more" link) points at a per-item detail page — e.g. Vertex
 * services, `<a href="…/ice-machine-services/">` — the href is not a field
 * stamp (the slug value isn't URL-shaped), so every cloned card kept the
 * TEMPLATE item's URL. The detector stamps the detail anchor with
 * `data-wpc-cpt-href-field` (the slug field key) + `data-wpc-cpt-href-original`
 * (the template item's slug). Here we swap that slug segment for each post's own
 * slug, preserving the template href's structure (home_url wrapper, any path
 * prefix, trailing slash). The WC path has its own permalink rewriter
 * (wpconvert_wc_rewrite_product_links); this covers plain CPT card grids.
 *
 * The markers are stripped as we go, so they never ship to the browser.
 *
 * @param \DOMElement $clone
 * @param \DOMXPath   $xpath
 * @param \WP_Post    $post
 * @param array       $cfg
 * @return void
 */
function wpconvert_cpt_rewrite_slug_links( $clone, $xpath, $post, $cfg ) {
    // descendant-or-self so a whole-card <a> (the clone root) is included.
    $nodes = $xpath->query( 'descendant-or-self::*[@data-wpc-cpt-href-field]', $clone );
    if ( ! $nodes || $nodes->length === 0 ) return;

    foreach ( $nodes as $el ) {
        if ( ! ( $el instanceof \DOMElement ) ) continue;

        $field    = (string) $el->getAttribute( 'data-wpc-cpt-href-field' );
        $original = (string) $el->getAttribute( 'data-wpc-cpt-href-original' );
        // Strip the runtime-only markers regardless of outcome.
        $el->removeAttribute( 'data-wpc-cpt-href-field' );
        $el->removeAttribute( 'data-wpc-cpt-href-original' );

        if ( $field === '' || ! $el->hasAttribute( 'href' ) ) continue;

        $meta_key = wpconvert_cpt_meta_key_for_field( $field );
        $value    = wpconvert_cpt_read_field_value( $post, $field, $meta_key, $cfg );
        $slug     = is_scalar( $value ) ? trim( (string) $value ) : '';
        if ( $slug === '' ) continue;

        $href = (string) $el->getAttribute( 'href' );
        if ( $href === '' ) continue;

        if ( $original !== '' && strpos( $href, $original ) !== false ) {
            // Preserve the template URL's structure — swap only the slug.
            $new_href = str_replace( $original, $slug, $href );
        } else {
            // Template slug drifted / not found — build a home_url path.
            $path = '/' . ltrim( $slug, '/' );
            if ( substr( $path, -1 ) !== '/' ) $path .= '/';
            $new_href = function_exists( 'home_url' ) ? home_url( $path ) : $path;
        }

        if ( function_exists( 'esc_url' ) ) $new_href = esc_url( $new_href );
        $el->setAttribute( 'href', $new_href );
    }
}

/* ─────────────────────────────────────────────
 * 13.7 WOOCOMMERCE IMPORTER  (Ship 4c.6 — EC-CPT-011)
 *
 * Candidates the build-time detector flagged with
 * `intent: 'woocommerce-product'` are NEVER offered as CPTs. Instead:
 *
 *   - admin notice offers a one-click "Import N products" flow that
 *     creates real WC_Product_Simple products (chunked AJAX, global
 *     transient lock, idempotent via stable external keys, stale-PID
 *     self-heal when a product is deleted in WC admin),
 *   - the front-end loop swap renders the imported products through
 *     the same DOM-stamped template the CPT path uses (static HTML is
 *     preserved until the FIRST import so installing WC never blanks
 *     a page),
 *   - `wp wpconvert-wc list|import|status` mirrors the flow for CI /
 *     headless deploys,
 *   - HPOS compatibility is declared on before_woocommerce_init.
 *
 * Every entry point is tier-gated via wpconvert_cpt_should_run() and
 * no-ops when WooCommerce isn't active.
 * ───────────────────────────────────────────── */

if ( ! defined( 'WPCONVERT_WC_IMPORTS_OPTION' ) ) {
    define( 'WPCONVERT_WC_IMPORTS_OPTION', 'wpconvert_wc_imports' );
}
if ( ! defined( 'WPCONVERT_WC_LAST_RUN_OPTION' ) ) {
    define( 'WPCONVERT_WC_LAST_RUN_OPTION', 'wpconvert_wc_last_run' );
}
if ( ! defined( 'WPCONVERT_WC_LOCK_TRANSIENT' ) ) {
    define( 'WPCONVERT_WC_LOCK_TRANSIENT', 'wpconvert_wc_import_lock' );
}

/**
 * A3 — is WooCommerce loaded on this request?
 *
 * @return bool
 */
function wpconvert_wc_is_active() {
    return class_exists( 'WooCommerce' ) || function_exists( 'wc_get_product' );
}

/**
 * Manifest candidates carrying the WC intent flag. Cheap (manifest is
 * already cached by wpconvert_cpt_get_candidates_manifest).
 *
 * @return array[]
 */
function wpconvert_wc_get_candidates() {
    $out = array();
    try {
        foreach ( wpconvert_cpt_get_candidates_manifest() as $c ) {
            if ( is_array( $c )
                && isset( $c['intent'] ) && $c['intent'] === 'woocommerce-product'
                && ! empty( $c['section_key'] ) ) {
                $out[] = $c;
            }
        }
    } catch ( \Throwable $e ) {
        return array();
    }
    return $out;
}

/**
 * Flattened import queue across ALL WC candidates, in manifest order.
 * The chunked AJAX handler slices this by offset/limit.
 *
 * @return array[]  Each entry: ['candidate' => array, 'item' => array]
 */
function wpconvert_wc_get_import_queue() {
    $queue = array();
    foreach ( wpconvert_wc_get_candidates() as $c ) {
        $items = ( isset( $c['items'] ) && is_array( $c['items'] ) ) ? $c['items'] : array();
        foreach ( $items as $item ) {
            if ( is_array( $item ) ) {
                $queue[] = array( 'candidate' => $c, 'item' => $item );
            }
        }
    }
    return $queue;
}

/**
 * The stable-key → product-ID tracking map.
 *
 * @return array
 */
function wpconvert_wc_get_imports() {
    if ( ! function_exists( 'get_option' ) ) return array();
    $map = get_option( WPCONVERT_WC_IMPORTS_OPTION, array() );
    return is_array( $map ) ? $map : array();
}

/**
 * Persist the tracking map with autoload=no (review fix: the map can
 * grow to hundreds of entries; never pay the every-request memory tax).
 *
 * @param array $map
 */
function wpconvert_wc_save_imports( $map ) {
    if ( ! function_exists( 'update_option' ) ) return;
    if ( function_exists( 'add_option' ) ) {
        add_option( WPCONVERT_WC_IMPORTS_OPTION, array(), '', 'no' );
    }
    update_option( WPCONVERT_WC_IMPORTS_OPTION, $map, false );
}

/**
 * A5 — stable idempotency key for one item. `external_id` (the
 * detector's `id` remap) is primary; `custom_slug` (the `slug` remap)
 * is second; sha1(section_key|name) is the fallback. NEVER the array
 * index — re-ordering the source array must not duplicate products.
 *
 * @param string $section_key
 * @param array  $item
 * @return string
 */
function wpconvert_wc_stable_item_key( $section_key, $item ) {
    foreach ( array( 'external_id', 'custom_slug' ) as $k ) {
        if ( isset( $item[ $k ] ) && is_scalar( $item[ $k ] ) && (string) $item[ $k ] !== '' ) {
            return (string) $item[ $k ];
        }
    }
    $name = '';
    foreach ( array( 'name', 'heading', 'title' ) as $k ) {
        if ( isset( $item[ $k ] ) && is_string( $item[ $k ] ) && $item[ $k ] !== '' ) {
            $name = $item[ $k ];
            break;
        }
    }
    return sha1( (string) $section_key . '|' . $name );
}

/**
 * Imported product IDs for one candidate, in source-item order, with
 * stale entries (deleted products / non-products) filtered out.
 *
 * @param array $candidate
 * @return int[]
 */
function wpconvert_wc_imported_pids_for_candidate( $candidate ) {
    $pids = array();
    $map = wpconvert_wc_get_imports();
    if ( empty( $map ) ) return $pids;
    $sk = isset( $candidate['section_key'] ) ? (string) $candidate['section_key'] : '';
    $items = ( isset( $candidate['items'] ) && is_array( $candidate['items'] ) ) ? $candidate['items'] : array();
    foreach ( $items as $item ) {
        if ( ! is_array( $item ) ) continue;
        $key = wpconvert_wc_stable_item_key( $sk, $item );
        if ( ! isset( $map[ $key ] ) ) continue;
        $pid = (int) $map[ $key ];
        if ( $pid <= 0 ) continue;
        if ( function_exists( 'get_post_type' ) && get_post_type( $pid ) !== 'product' ) continue;
        $pids[] = $pid;
    }
    return array_values( array_unique( $pids ) );
}

/**
 * Count of VALID tracked imports across all WC candidates. Drives the
 * pre-import vs post-import notice branch.
 *
 * @return int
 */
function wpconvert_wc_total_import_count() {
    $n = 0;
    foreach ( wpconvert_wc_get_candidates() as $c ) {
        $n += count( wpconvert_wc_imported_pids_for_candidate( $c ) );
    }
    return $n;
}

/**
 * A5 — parse a price value as the AI builders emit them: plain numbers,
 * `"$69.00"`, `"69 kr"`, `"€69,00"`, `"₹999"`, `"Rs. 1,299"`,
 * `"R$ 99,90"`, `"1.299,00 €"`, `"¥1,200"`. Returns
 * `[numeric-string, detected-symbol]` or null when unparseable.
 *
 * @param mixed $raw
 * @return array|null
 */
function wpconvert_wc_parse_price( $raw ) {
    if ( is_int( $raw ) || is_float( $raw ) ) {
        return array( (string) $raw, '' );
    }
    if ( ! is_string( $raw ) ) return null;
    $s = trim( $raw );
    if ( $s === '' ) return null;

    // Symbol detection — longest tokens first so "R$" beats "$".
    $symbol = '';
    foreach ( array( 'R$', 'Rs.', 'Rs', 'kr.', 'kr', 'zł', 'CHF', '$', '€', '£', '¥', '₹', '₩', '₽', '₺', '฿', '₫' ) as $tok ) {
        if ( mb_stripos( $s, $tok ) !== false ) {
            $symbol = $tok;
            break;
        }
    }

    // Extract the first digit-run (digits + separators). The leading-digit
    // anchor keeps "Rs." / "kr." dots out of the number.
    if ( ! preg_match( '/[0-9][0-9.,\s\x{00A0}\x{202F}]*/u', $s, $m ) ) {
        return null;
    }
    $num = preg_replace( '/[\s\x{00A0}\x{202F}]+/u', '', rtrim( $m[0], " .,\xC2\xA0" ) );
    if ( $num === '' || $num === null ) return null;

    $has_dot = strpos( $num, '.' ) !== false;
    $has_comma = strpos( $num, ',' ) !== false;
    if ( $has_dot && $has_comma ) {
        // The LAST separator is the decimal mark ("1.299,00" / "1,299.00").
        if ( strrpos( $num, ',' ) > strrpos( $num, '.' ) ) {
            $num = str_replace( '.', '', $num );
            $num = str_replace( ',', '.', $num );
        } else {
            $num = str_replace( ',', '', $num );
        }
    } elseif ( $has_comma ) {
        // ",NNN"-grouped → thousands; otherwise decimal ("99,90").
        $num = preg_match( '/^\d{1,3}(,\d{3})+$/', $num )
            ? str_replace( ',', '', $num )
            : str_replace( ',', '.', $num );
    } elseif ( $has_dot ) {
        // ".NNN"-grouped → European thousands; otherwise decimal ("69.00").
        if ( preg_match( '/^\d{1,3}(\.\d{3})+$/', $num ) ) {
            $num = str_replace( '.', '', $num );
        }
    }
    if ( ! is_numeric( $num ) ) return null;
    return array( $num, $symbol );
}

/**
 * Currency code for a detected symbol ('' when ambiguous/unknown).
 *
 * @param string $symbol
 * @return string
 */
function wpconvert_wc_symbol_to_currency( $symbol ) {
    $map = array(
        '$' => 'USD', '€' => 'EUR', '£' => 'GBP', '¥' => 'JPY',
        '₹' => 'INR', 'Rs' => 'INR', 'Rs.' => 'INR', 'R$' => 'BRL',
        'kr' => 'SEK', 'kr.' => 'DKK', 'zł' => 'PLN', '₩' => 'KRW',
        '₽' => 'RUB', '₺' => 'TRY', '฿' => 'THB', '₫' => 'VND',
        'CHF' => 'CHF',
    );
    return isset( $map[ $symbol ] ) ? $map[ $symbol ] : '';
}

/**
 * A3 — compare the theme's price symbols against the store currency.
 * Returns null on match / unknown, or ['theme' => ..., 'store' => ...].
 *
 * @return array|null
 */
function wpconvert_wc_detect_currency_mismatch() {
    try {
        if ( ! function_exists( 'get_option' ) ) return null;
        $store = (string) get_option( 'woocommerce_currency', '' );
        if ( $store === '' ) return null;

        $theme = '';
        foreach ( wpconvert_wc_get_import_queue() as $entry ) {
            $item = $entry['item'];
            foreach ( array( 'price', 'originalPrice', 'originalprice', 'salePrice', 'saleprice', 'cost' ) as $k ) {
                foreach ( $item as $ik => $iv ) {
                    if ( strtolower( (string) $ik ) !== strtolower( $k ) ) continue;
                    if ( ! is_string( $iv ) ) continue;
                    $parsed = wpconvert_wc_parse_price( $iv );
                    if ( $parsed && $parsed[1] !== '' ) {
                        $theme = wpconvert_wc_symbol_to_currency( $parsed[1] );
                    }
                    break;
                }
                if ( $theme !== '' ) break;
            }
            if ( $theme !== '' ) break;
        }
        if ( $theme === '' ) return null;
        if ( $theme === $store ) return null;
        // "kr" is ambiguous across the Nordic currencies — any of them counts
        // as a match so we don't nag a Norwegian store about Swedish kronor.
        $nordic = array( 'SEK', 'NOK', 'DKK', 'ISK' );
        if ( in_array( $theme, $nordic, true ) && in_array( $store, $nordic, true ) ) return null;
        return array( 'theme' => $theme, 'store' => $store );
    } catch ( \Throwable $e ) {
        return null;
    }
}

/**
 * A5 — GLOBAL import lock (one import button imports all WC candidates,
 * so the lock isn't per-section like the CPT one). Stores the holder's
 * user ID so a 409 response can say who is importing. TTL 5 min.
 *
 * @return bool  True when acquired by this call.
 */
function wpconvert_wc_acquire_import_lock() {
    if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
        return true; // No transients — best-effort proceed.
    }
    if ( get_transient( WPCONVERT_WC_LOCK_TRANSIENT ) !== false ) {
        return false;
    }
    $uid = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
    set_transient( WPCONVERT_WC_LOCK_TRANSIENT, $uid, 5 * MINUTE_IN_SECONDS );
    return true;
}

/**
 * @return int  Lock-holder user ID (0 when unlocked).
 */
function wpconvert_wc_get_lock_holder() {
    if ( ! function_exists( 'get_transient' ) ) return 0;
    $v = get_transient( WPCONVERT_WC_LOCK_TRANSIENT );
    return $v === false ? 0 : (int) $v;
}

/**
 * Release the global import lock. Idempotent.
 */
function wpconvert_wc_release_import_lock() {
    if ( function_exists( 'delete_transient' ) ) {
        delete_transient( WPCONVERT_WC_LOCK_TRANSIENT );
    }
}

/**
 * A4 — `intermediate_image_sizes_advanced` filter callback. Returning
 * [] makes WP create only the full-size attachment during import;
 * thumbnails are regenerated lazily afterwards. Cuts sideload time
 * by ~60-80% on image-heavy catalogs.
 *
 * @param array $sizes
 * @return array
 */
function wpconvert_wc_disable_thumbnail_sizes( $sizes ) {
    return array();
}

/**
 * A4 — resolve featured + gallery images for one item.
 *
 * Wraps the EXISTING `wpconvert_cpt_resolve_image_to_attachment_id()`
 * (kebab-case + Vite-fingerprint handling, theme-attach, sideload) —
 * the fresh part is only the gallery aggregation + featured dedup.
 *
 * @param array $item
 * @param array $candidate
 * @return array  ['featured' => int, 'gallery' => int[]]
 */
function wpconvert_wc_resolve_images_for_item( $item, $candidate ) {
    $featured = 0;
    $gallery = array();
    try {
        // Featured: the first image-typed field from the candidate schema.
        $image_key = '';
        if ( isset( $candidate['fields'] ) && is_array( $candidate['fields'] ) ) {
            foreach ( $candidate['fields'] as $f ) {
                if ( is_array( $f ) && isset( $f['type'] ) && $f['type'] === 'image' ) {
                    $image_key = isset( $f['remapped_to'] ) && $f['remapped_to'] !== ''
                        ? (string) $f['remapped_to']
                        : (string) ( $f['key'] ?? '' );
                    break;
                }
            }
            // EC-CPT-019 — defense in depth for manifests where an
            // image-bearing field was typed `url` (the detector pre-fix
            // mis-typed extensionless image-CDN URLs like
            // images.unsplash.com/photo-…?w=800, so neon-elite's `image`
            // field came through as `url` and the loop above found
            // nothing → every product imported with no thumbnail). When no
            // `type=image` field exists, fall back to the first field whose
            // name is image-like; resolution still verifies the bytes.
            if ( $image_key === '' ) {
                $image_like = array(
                    'image', 'img', 'photo', 'picture', 'thumbnail', 'thumb',
                    'featured', 'cover', 'poster', 'avatar', 'hero',
                );
                foreach ( $candidate['fields'] as $f ) {
                    if ( ! is_array( $f ) ) continue;
                    $cand_key = isset( $f['remapped_to'] ) && $f['remapped_to'] !== ''
                        ? (string) $f['remapped_to']
                        : (string) ( $f['key'] ?? '' );
                    if ( $cand_key !== '' && in_array( strtolower( $cand_key ), $image_like, true ) ) {
                        $image_key = $cand_key;
                        break;
                    }
                }
            }
        }
        if ( $image_key !== '' && isset( $item[ $image_key ] ) && is_scalar( $item[ $image_key ] ) ) {
            $featured = (int) wpconvert_cpt_resolve_image_to_attachment_id( (string) $item[ $image_key ] );
        }

        // Gallery: every resolvable identifier, deduped against featured.
        if ( isset( $item['gallery'] ) && is_array( $item['gallery'] ) ) {
            foreach ( $item['gallery'] as $identifier ) {
                if ( ! is_scalar( $identifier ) || (string) $identifier === '' ) continue;
                $att = (int) wpconvert_cpt_resolve_image_to_attachment_id( (string) $identifier );
                if ( $att <= 0 ) continue;            // broken identifier → skip silently
                if ( $att === $featured ) continue;    // featured dedup
                if ( in_array( $att, $gallery, true ) ) continue;
                $gallery[] = $att;
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc resolve_images: ' . $e->getMessage() );
        }
    }
    return array( 'featured' => $featured, 'gallery' => $gallery );
}

/**
 * A5 — import ONE item as a WC_Product_Simple.
 *
 * Idempotent: the stable key is looked up in the tracking map first;
 * a still-valid mapped PID short-circuits as 'skipped'. A mapped PID
 * whose product was deleted in WC admin is healed (dropped + re-imported).
 *
 * @param array $candidate
 * @param array $item
 * @param array $args  { dry_run: bool }
 * @return array  { status: created|skipped|failed|would-create|would-skip,
 *                  pid: int, name: string, reason?: string }
 */
function wpconvert_wc_import_item( $candidate, $item, $args = array() ) {
    $dry_run = ! empty( $args['dry_run'] );
    $sk = isset( $candidate['section_key'] ) ? (string) $candidate['section_key'] : '';

    // Case-insensitive item-field lookup (manifest keys keep source casing,
    // e.g. `originalPrice`).
    $lc = array();
    foreach ( $item as $k => $v ) {
        if ( is_string( $k ) ) $lc[ strtolower( $k ) ] = $v;
    }

    $name = '';
    foreach ( array( 'name', 'heading', 'title' ) as $k ) {
        if ( isset( $lc[ $k ] ) && is_string( $lc[ $k ] ) && trim( $lc[ $k ] ) !== '' ) {
            $name = trim( $lc[ $k ] );
            break;
        }
    }
    if ( $name === '' ) {
        return array( 'status' => 'failed', 'pid' => 0, 'name' => '(unnamed)', 'reason' => 'no-name' );
    }

    try {
        $stable_key = wpconvert_wc_stable_item_key( $sk, $item );
        $map = wpconvert_wc_get_imports();

        // Idempotency + stale-PID heal.
        if ( isset( $map[ $stable_key ] ) ) {
            $pid = (int) $map[ $stable_key ];
            $still_product = $pid > 0
                && function_exists( 'get_post_type' )
                && get_post_type( $pid ) === 'product';
            if ( $still_product ) {
                return array(
                    'status' => $dry_run ? 'would-skip' : 'skipped',
                    'pid'    => $pid,
                    'name'   => $name,
                );
            }
            // Product was deleted in WC admin — drop the stale entry and
            // fall through to re-import.
            unset( $map[ $stable_key ] );
            if ( ! $dry_run ) {
                wpconvert_wc_save_imports( $map );
            }
        }

        // Price resolution. `originalPrice` + `salePrice` → regular + sale;
        // otherwise the plain price field is the regular price.
        $sale_raw = isset( $lc['saleprice'] ) ? $lc['saleprice'] : null;
        $regular_raw = null;
        foreach ( array( 'originalprice', 'regularprice', 'price', 'cost' ) as $k ) {
            if ( isset( $lc[ $k ] ) ) { $regular_raw = $lc[ $k ]; break; }
        }
        if ( $regular_raw === null && $sale_raw !== null ) {
            $regular_raw = $sale_raw;
            $sale_raw = null;
        }
        $regular = null;
        $sale = null;
        if ( $regular_raw !== null ) {
            $regular = wpconvert_wc_parse_price( $regular_raw );
            if ( $regular === null ) {
                return array( 'status' => 'failed', 'pid' => 0, 'name' => $name, 'reason' => 'unparseable-price' );
            }
        }
        if ( $sale_raw !== null ) {
            $sale = wpconvert_wc_parse_price( $sale_raw ); // unparseable sale → just skip the sale
        }

        if ( $dry_run ) {
            return array( 'status' => 'would-create', 'pid' => 0, 'name' => $name );
        }
        if ( ! class_exists( 'WC_Product_Simple' ) ) {
            return array( 'status' => 'failed', 'pid' => 0, 'name' => $name, 'reason' => 'wc-not-available' );
        }

        $product = new \WC_Product_Simple();
        $product->set_name( $name );

        // Copy mapping. Short punchy line → short description (excerpt);
        // detailed body → long description (product page body).
        //
        // EC-CPT-IMPORT-DESC — the source array rarely names its copy
        // fields literally `description`. Bolt/Lovable/v0 catalogs use
        // `tagline`/`subtitle` for the one-liner and `overview`/`about`/
        // `details` for the body (DownUnderPeps: `tagline` + `overview`).
        // Falling through these synonyms means imported products keep real
        // copy instead of a blank description. Purely additive — the
        // literal `description`/`desc` keys still win when present.
        $short_desc_keys = array( 'description', 'desc', 'summary', 'excerpt', 'tagline', 'subtitle', 'blurb', 'caption' );
        $long_desc_keys  = array( 'body', 'content', 'overview', 'about', 'details', 'longdescription', 'fulldescription', 'bio', 'story' );
        foreach ( $short_desc_keys as $k ) {
            if ( isset( $lc[ $k ] ) && is_string( $lc[ $k ] ) && trim( $lc[ $k ] ) !== '' ) {
                $product->set_short_description( $lc[ $k ] );
                break;
            }
        }
        foreach ( $long_desc_keys as $k ) {
            if ( isset( $lc[ $k ] ) && is_string( $lc[ $k ] ) && trim( $lc[ $k ] ) !== '' ) {
                $product->set_description( $lc[ $k ] );
                break;
            }
        }

        // Review fix #11 — WC defaults don't reliably surface in the shop
        // loop; force publish/visible/instock explicitly.
        $product->set_status( 'publish' );
        $product->set_catalog_visibility( 'visible' );
        $product->set_stock_status( 'instock' );

        if ( $regular !== null ) {
            $product->set_regular_price( $regular[0] );
        }
        if ( $sale !== null ) {
            $product->set_sale_price( $sale[0] );
        }

        // SKU with collision fallback: append -WPC{n} and continue.
        $sku = isset( $lc['sku'] ) && is_scalar( $lc['sku'] ) ? (string) $lc['sku'] : '';
        if ( $sku !== '' ) {
            try {
                $product->set_sku( $sku );
            } catch ( \Exception $e ) {
                for ( $n = 1; $n <= 20; $n++ ) {
                    try {
                        $product->set_sku( $sku . '-WPC' . $n );
                        break;
                    } catch ( \Exception $e2 ) {
                        // keep trying
                    }
                }
            }
        }

        // EC-CPT-IMPORT-ATTRIBUTES — carry the source spec table / string
        // lists into WooCommerce custom product attributes (rendered in the
        // "Additional information" tab). The detector emits `item.attributes`
        // as `[{name, value}]`; a value may be pipe-joined for multi-value
        // lists (storage/features). Must be set BEFORE save().
        if ( isset( $item['attributes'] ) && is_array( $item['attributes'] )
            && class_exists( 'WC_Product_Attribute' ) ) {
            $wc_attrs = array();
            $seen = array();
            $pos  = 0;
            foreach ( $item['attributes'] as $attr ) {
                if ( ! is_array( $attr ) ) continue;
                $an = isset( $attr['name'] ) && is_scalar( $attr['name'] ) ? trim( (string) $attr['name'] ) : '';
                $av = isset( $attr['value'] ) && is_scalar( $attr['value'] ) ? trim( (string) $attr['value'] ) : '';
                if ( $an === '' || $av === '' ) continue;
                // De-dupe by name (case-insensitive) — first wins.
                $key = strtolower( $an );
                if ( isset( $seen[ $key ] ) ) continue;
                $seen[ $key ] = true;
                $options = array();
                foreach ( explode( '|', $av ) as $opt ) {
                    $opt = trim( $opt );
                    if ( $opt !== '' ) $options[] = $opt;
                }
                if ( empty( $options ) ) continue;
                $o = new \WC_Product_Attribute();
                $o->set_id( 0 ); // 0 = custom (non-taxonomy) attribute
                $o->set_name( $an );
                $o->set_options( $options );
                $o->set_position( $pos++ );
                $o->set_visible( true );
                $o->set_variation( false );
                $wc_attrs[] = $o;
            }
            if ( ! empty( $wc_attrs ) && method_exists( $product, 'set_attributes' ) ) {
                $product->set_attributes( $wc_attrs );
            }
        }

        $pid = (int) $product->save();
        if ( $pid <= 0 ) {
            return array( 'status' => 'failed', 'pid' => 0, 'name' => $name, 'reason' => 'save-failed' );
        }

        // Images (A4): featured via thumbnail, gallery via WC's meta key.
        $images = wpconvert_wc_resolve_images_for_item( $item, $candidate );
        if ( $images['featured'] > 0 && function_exists( 'set_post_thumbnail' ) ) {
            set_post_thumbnail( $pid, $images['featured'] );
        }
        if ( ! empty( $images['gallery'] ) && function_exists( 'update_post_meta' ) ) {
            update_post_meta( $pid, '_product_image_gallery', implode( ',', $images['gallery'] ) );
        }

        // Category → product_cat term (created on first use).
        $category = isset( $lc['category'] ) && is_scalar( $lc['category'] ) ? trim( (string) $lc['category'] ) : '';
        if ( $category !== ''
            && function_exists( 'term_exists' ) && function_exists( 'wp_insert_term' )
            && function_exists( 'wp_set_object_terms' ) ) {
            $term = term_exists( $category, 'product_cat' );
            if ( ! $term ) {
                $term = wp_insert_term( $category, 'product_cat' );
            }
            if ( is_array( $term ) && isset( $term['term_id'] ) ) {
                wp_set_object_terms( $pid, array( (int) $term['term_id'] ), 'product_cat', true );
            }
        }

        // Custom theme fields (badge, color, featured…) — preserved as
        // `_wpc_field_{key}` meta so the loop swap can substitute them.
        if ( isset( $item['custom'] ) && is_array( $item['custom'] ) && function_exists( 'update_post_meta' ) ) {
            foreach ( $item['custom'] as $ck => $cv ) {
                if ( ! is_string( $ck ) || $ck === '' ) continue;
                if ( ! is_scalar( $cv ) ) continue;
                update_post_meta( $pid, wpconvert_cpt_meta_key_for_field( $ck ), $cv );
            }
        }

        // Review fix #9 — dual-write tracking: option map AND post meta.
        $map[ $stable_key ] = $pid;
        wpconvert_wc_save_imports( $map );
        if ( function_exists( 'update_post_meta' ) ) {
            update_post_meta( $pid, '_wpc_wc_external_id', $stable_key );
            update_post_meta( $pid, '_wpc_imported_section_key', $sk );
        }

        return array( 'status' => 'created', 'pid' => $pid, 'name' => $name );
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc import_item: ' . $e->getMessage() );
        }
        return array( 'status' => 'failed', 'pid' => 0, 'name' => $name, 'reason' => $e->getMessage() );
    }
}

/**
 * A5 — process one chunk of the flattened queue. Shared by the AJAX
 * handler and the WP-CLI command.
 *
 * @param int   $offset
 * @param int   $limit
 * @param array $args  { dry_run: bool }
 * @return array  { done, processed_count, total_count, created_count,
 *                  skipped_count, success: [], failed: [] }
 */
function wpconvert_wc_run_import( $offset, $limit, $args = array() ) {
    $queue = wpconvert_wc_get_import_queue();
    $total = count( $queue );
    $offset = max( 0, (int) $offset );
    $limit = max( 1, min( 200, (int) $limit ) );
    $slice = array_slice( $queue, $offset, $limit );

    // A4 — deferred thumbnail generation for the duration of the chunk.
    $filter_added = false;
    if ( function_exists( 'add_filter' ) ) {
        add_filter( 'intermediate_image_sizes_advanced', 'wpconvert_wc_disable_thumbnail_sizes', 10, 1 );
        $filter_added = true;
    }

    $success = array();
    $failed = array();
    $created = 0;
    $skipped = 0;
    try {
        foreach ( $slice as $entry ) {
            $res = wpconvert_wc_import_item( $entry['candidate'], $entry['item'], $args );
            if ( $res['status'] === 'failed' ) {
                $failed[] = array( 'name' => $res['name'], 'reason' => isset( $res['reason'] ) ? $res['reason'] : 'unknown' );
            } else {
                $success[] = array( 'name' => $res['name'], 'pid' => $res['pid'], 'status' => $res['status'] );
                if ( $res['status'] === 'created' || $res['status'] === 'would-create' ) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }
    } finally {
        if ( $filter_added && function_exists( 'remove_filter' ) ) {
            remove_filter( 'intermediate_image_sizes_advanced', 'wpconvert_wc_disable_thumbnail_sizes', 10 );
        }
    }

    // Lazy thumbnail regeneration for everything attached in this chunk.
    if ( $created > 0 && empty( $args['dry_run'] ) && function_exists( 'wp_schedule_single_event' ) ) {
        wp_schedule_single_event( time() + 60, 'wpconvert_wc_regenerate_thumbnails' );
    }

    return array(
        'done'            => ( $offset + count( $slice ) ) >= $total,
        'processed_count' => count( $slice ),
        'total_count'     => $total,
        'created_count'   => $created,
        'skipped_count'   => $skipped,
        'success'         => $success,
        'failed'          => $failed,
    );
}

/**
 * Cron callback — regenerate the thumbnail sizes the import skipped.
 * Best-effort; missing media functions (CLI contexts) are tolerated.
 */
function wpconvert_wc_cron_regenerate_thumbnails() {
    try {
        if ( ! function_exists( 'get_posts' )
            || ! function_exists( 'wp_generate_attachment_metadata' )
            || ! function_exists( 'wp_update_attachment_metadata' )
            || ! function_exists( 'get_attached_file' ) ) {
            return;
        }
        $pids = array();
        foreach ( wpconvert_wc_get_candidates() as $c ) {
            $pids = array_merge( $pids, wpconvert_wc_imported_pids_for_candidate( $c ) );
        }
        foreach ( array_unique( $pids ) as $pid ) {
            $tid = (int) get_post_meta( $pid, '_thumbnail_id', true );
            if ( $tid <= 0 ) continue;
            $file = get_attached_file( $tid );
            if ( ! is_string( $file ) || $file === '' || ! file_exists( $file ) ) continue;
            $meta = wp_generate_attachment_metadata( $tid, $file );
            if ( is_array( $meta ) ) {
                wp_update_attachment_metadata( $tid, $meta );
            }
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc regen_thumbnails: ' . $e->getMessage() );
        }
    }
}

/**
 * Accumulate per-chunk results into the `wpconvert_wc_last_run` option
 * (autoload=no). Offset 0 starts a fresh record.
 *
 * @param array $result  Return value of wpconvert_wc_run_import().
 * @param int   $offset
 */
function wpconvert_wc_record_run_progress( $result, $offset ) {
    if ( ! function_exists( 'update_option' ) ) return;
    $run = array();
    if ( (int) $offset > 0 && function_exists( 'get_option' ) ) {
        $run = get_option( WPCONVERT_WC_LAST_RUN_OPTION, array() );
        if ( ! is_array( $run ) ) $run = array();
    }
    if ( empty( $run ) ) {
        $run = array( 'started_at' => time(), 'success_count' => 0, 'created_count' => 0, 'failed' => array() );
    }
    $run['success_count'] = (int) ( $run['success_count'] ?? 0 ) + count( $result['success'] );
    $run['created_count'] = (int) ( $run['created_count'] ?? 0 ) + (int) $result['created_count'];
    $prev_failed = isset( $run['failed'] ) && is_array( $run['failed'] ) ? $run['failed'] : array();
    $run['failed'] = array_merge( $prev_failed, $result['failed'] );
    $run['total'] = (int) $result['total_count'];
    $run['updated_at'] = time();
    if ( ! empty( $result['done'] ) ) {
        $run['finished_at'] = time();
    }
    if ( function_exists( 'add_option' ) ) {
        add_option( WPCONVERT_WC_LAST_RUN_OPTION, array(), '', 'no' );
    }
    update_option( WPCONVERT_WC_LAST_RUN_OPTION, $run, false );
}

/**
 * Capability gate shared by the AJAX handler and the admin notice.
 * `manage_woocommerce` is the canonical WC capability; `manage_options`
 * is the fallback for stores where WC hasn't registered roles yet.
 *
 * @return bool
 */
function wpconvert_wc_current_user_can_import() {
    if ( ! function_exists( 'current_user_can' ) ) return false;
    return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
}

/**
 * A5 — chunked AJAX import endpoint.
 *
 * POST params: nonce (wpconvert_cpt_nonce), offset, limit.
 *
 * Security mirrors wpconvert_cpt_ajax_activate_and_import():
 * tier gate → capability → nonce → WC-active. wp_send_json_* stays
 * OUTSIDE try/catch (it wp_die()s in real WP).
 */
function wpconvert_wc_ajax_import_products() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! wpconvert_wc_current_user_can_import() ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    if ( ! wpconvert_wc_is_active() ) {
        wp_send_json_error( 'wc-not-active', 403 );
    }

    $offset = isset( $_POST['offset'] ) ? max( 0, (int) $_POST['offset'] ) : 0;
    $limit = isset( $_POST['limit'] ) ? (int) $_POST['limit'] : 15;
    if ( $limit < 1 ) $limit = 1;
    if ( $limit > 50 ) $limit = 50;

    // Global concurrent-import lock. Acquired on the FIRST chunk; later
    // chunks must come from the same user (the holder), otherwise 409.
    $uid = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
    if ( $offset === 0 ) {
        if ( ! wpconvert_wc_acquire_import_lock() ) {
            wp_send_json_error(
                array( 'error' => 'locked', 'lock_holder' => wpconvert_wc_get_lock_holder() ),
                409
            );
        }
    } else {
        $holder = wpconvert_wc_get_lock_holder();
        if ( $holder !== 0 && $holder !== $uid ) {
            wp_send_json_error(
                array( 'error' => 'locked', 'lock_holder' => $holder ),
                409
            );
        }
        // Refresh the TTL so long catalogs don't lose the lock mid-run.
        if ( function_exists( 'set_transient' ) ) {
            set_transient( WPCONVERT_WC_LOCK_TRANSIENT, $uid, 5 * MINUTE_IN_SECONDS );
        }
    }

    $error_message = null;
    $result = null;
    try {
        $result = wpconvert_wc_run_import( $offset, $limit );
        wpconvert_wc_record_run_progress( $result, $offset );
    } catch ( \Throwable $e ) {
        $error_message = $e->getMessage();
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc ajax_import: ' . $error_message );
        }
    }

    if ( $error_message !== null ) {
        wpconvert_wc_release_import_lock();
        wp_send_json_error( array( 'error' => 'exception', 'message' => $error_message ), 500 );
    }
    if ( ! empty( $result['done'] ) ) {
        wpconvert_wc_release_import_lock();
    }
    wp_send_json_success( $result );
}

/**
 * EC-CPT-012 / undo — bulk-remove every WPConvert-imported product.
 *
 * Walks the `wpconvert_wc_imports` tracking map and force-deletes ONLY
 * posts that (a) still exist, (b) are still `product` posts, and (c)
 * still carry the `_wpc_wc_external_id` marker meta we wrote at import
 * time. Products the user created themselves — or imported products the
 * user re-purposed by stripping the marker — are never touched.
 *
 * Attachments are intentionally KEPT: featured/gallery images resolve
 * through the shared theme-attach path and may be referenced by other
 * content; deleting them could break unrelated pages. WP's media
 * library cleanup is the user's call.
 *
 * Clears the tracking map and the last-run summary afterwards, so the
 * admin notice falls back to the pre-import state and a fresh import is
 * possible (the front-end loop swap reverts to the original static HTML
 * because no valid tracked PIDs remain).
 *
 * @return array {removed_count, skipped_count, total_count}
 */
function wpconvert_wc_run_remove() {
    $map = wpconvert_wc_get_imports();
    $removed = 0;
    $skipped = 0;

    foreach ( $map as $key => $pid ) {
        $pid = (int) $pid;
        if ( $pid <= 0 ) { $skipped++; continue; }
        if ( ! function_exists( 'get_post_type' ) || get_post_type( $pid ) !== 'product' ) {
            // Stale entry — product already deleted in WC admin.
            $skipped++;
            continue;
        }
        $marker = function_exists( 'get_post_meta' )
            ? get_post_meta( $pid, '_wpc_wc_external_id', true )
            : '';
        if ( $marker === '' || $marker === false || $marker === null ) {
            // Not provably ours any more — leave it alone.
            $skipped++;
            continue;
        }
        if ( function_exists( 'wp_delete_post' ) ) {
            wp_delete_post( $pid, true );
            $removed++;
        } else {
            $skipped++;
        }
    }

    // Reset tracking so the notice returns to the pre-import state and a
    // re-import starts clean.
    wpconvert_wc_save_imports( array() );
    if ( function_exists( 'delete_option' ) ) {
        delete_option( WPCONVERT_WC_LAST_RUN_OPTION );
    } elseif ( function_exists( 'update_option' ) ) {
        update_option( WPCONVERT_WC_LAST_RUN_OPTION, array(), false );
    }

    // EC-CPT-013 — also disconnect cart/checkout pages, restoring their
    // original static content and WC's previous page-ID options.
    $pages_restored = function_exists( 'wpconvert_wc_restore_connected_pages' )
        ? wpconvert_wc_restore_connected_pages()
        : 0;

    return array(
        'removed_count'  => $removed,
        'skipped_count'  => $skipped,
        'total_count'    => count( $map ),
        'pages_restored' => $pages_restored,
    );
}

/**
 * EC-CPT-012 / undo — AJAX endpoint: remove all imported products.
 *
 * POST params: nonce (wpconvert_cpt_nonce).
 *
 * Security mirrors wpconvert_wc_ajax_import_products() exactly:
 * tier gate → capability → nonce → WC-active. Takes the SAME global
 * lock as the importer so a remove can never race a running import
 * (409 when held). wp_send_json_* stays OUTSIDE try/catch.
 */
function wpconvert_wc_ajax_remove_products() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! wpconvert_wc_current_user_can_import() ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    if ( ! wpconvert_wc_is_active() ) {
        wp_send_json_error( 'wc-not-active', 403 );
    }

    if ( ! wpconvert_wc_acquire_import_lock() ) {
        wp_send_json_error(
            array( 'error' => 'locked', 'lock_holder' => wpconvert_wc_get_lock_holder() ),
            409
        );
    }

    $error_message = null;
    $result = null;
    try {
        $result = wpconvert_wc_run_remove();
    } catch ( \Throwable $e ) {
        $error_message = $e->getMessage();
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc ajax_remove: ' . $error_message );
        }
    }
    wpconvert_wc_release_import_lock();

    if ( $error_message !== null ) {
        wp_send_json_error( array( 'error' => 'exception', 'message' => $error_message ), 500 );
    }
    wp_send_json_success( $result );
}

/**
 * A3/A8 — the WooCommerce admin notice. Three states:
 *
 *   1. WC not active → "Install WooCommerce" hint.
 *   2. WC active, nothing imported → "Import N products" button +
 *      pre-import preview modal (A8) + chunked-AJAX progress JS.
 *   3. Imports exist (or a run was recorded) → post-import dashboard
 *      with WC links, currency-mismatch warning, retry-failed button.
 *
 * Renders nothing when there are no WC-intent candidates.
 */
function wpconvert_wc_render_admin_notice() {
    if ( ! wpconvert_cpt_should_run() ) return;
    if ( ! function_exists( 'is_admin' ) || ! is_admin() ) return;
    if ( ! wpconvert_wc_current_user_can_import() ) return;

    try {
        $candidates = wpconvert_wc_get_candidates();
        if ( empty( $candidates ) ) return;

        $queue = wpconvert_wc_get_import_queue();
        $total = count( $queue );
        if ( $total === 0 ) return;

        // Truncation surface (review fix #20) — MAX_ITEMS_IN_MANIFEST caps
        // each candidate at 200 items; the source may have had more.
        $is_truncated = false;
        $source_total = 0;
        foreach ( $candidates as $c ) {
            if ( ! empty( $c['is_truncated'] ) ) $is_truncated = true;
            $source_total += (int) ( $c['item_count'] ?? 0 );
        }
        if ( $source_total < $total ) $source_total = $total;

        // ── State 1: WC not installed/active → install hint ──
        if ( ! wpconvert_wc_is_active() ) {
            $install_url = function_exists( 'admin_url' )
                ? admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' )
                : '#';
            echo '<div class="notice notice-info wpc-wc-notice wpc-wc-install-hint">';
            echo '<h3 style="margin:0.6em 0 0.3em">WPConvert found ' . (int) $total
                . ' product' . ( $total === 1 ? '' : 's' ) . ' in your theme.</h3>';
            echo '<p style="margin:0 0 1em">Install WooCommerce to import them as real, manageable products. '
                . 'Until then the products render as static content.</p>';
            echo '<p><a href="' . esc_url( $install_url ) . '" class="button button-primary">Install WooCommerce</a></p>';
            echo '</div>';
            return;
        }

        $imported = wpconvert_wc_total_import_count();
        $last_run = function_exists( 'get_option' )
            ? get_option( WPCONVERT_WC_LAST_RUN_OPTION, array() )
            : array();
        if ( ! is_array( $last_run ) ) $last_run = array();

        $mismatch = wpconvert_wc_detect_currency_mismatch();

        // ── State 3: post-import dashboard ──
        if ( $imported > 0 || ! empty( $last_run ) ) {
            $products_url = function_exists( 'admin_url' )
                ? admin_url( 'edit.php?post_type=product' )
                : '#';
            $shop_url = function_exists( 'admin_url' )
                ? admin_url( 'admin.php?page=wc-settings&tab=products' )
                : '#';
            $failed = isset( $last_run['failed'] ) && is_array( $last_run['failed'] )
                ? $last_run['failed']
                : array();
            $run_total = isset( $last_run['total'] ) ? (int) $last_run['total'] : $total;

            echo '<div class="notice notice-success wpc-wc-notice wpc-wc-dashboard">';
            echo '<h3 style="margin:0.6em 0 0.3em">WPConvert &rarr; WooCommerce import</h3>';
            echo '<p class="wpc-wc-last-imported" style="margin:0 0 0.6em">Last imported: '
                . (int) $imported . ' of ' . (int) $run_total . ' products</p>';
            if ( $mismatch !== null ) {
                echo '<p class="wpc-wc-currency-warning" style="margin:0 0 0.6em;padding:0.5em 0.8em;background:#fff8e5;border-left:3px solid #dba617;">'
                    . '<strong>Currency mismatch.</strong> Your theme prices look like '
                    . esc_html( $mismatch['theme'] ) . ' but the store currency is '
                    . esc_html( $mismatch['store'] ) . '. Review prices or change the currency in WooCommerce settings.</p>';
            }
            echo '<p style="margin:0 0 0.6em">';
            echo '<a href="' . esc_url( $products_url ) . '" class="button button-primary wpc-wc-view-products-btn">View products in WooCommerce admin</a> ';
            echo '<a href="' . esc_url( $shop_url ) . '" class="button wpc-wc-configure-shop-btn">Configure your Shop page</a> ';
            if ( count( $failed ) > 0 ) {
                echo '<button type="button" class="button wpc-wc-retry-btn" data-failed-count="' . (int) count( $failed ) . '">Retry failed ('
                    . (int) count( $failed ) . ')</button> ';
            }
            // EC-CPT-012 / undo — bulk-remove every imported product.
            echo '<button type="button" class="button wpc-wc-remove-btn" data-count="' . (int) $imported . '">Remove imported products</button> ';
            echo '<span class="wpc-wc-status" style="margin-left:0.6em;color:#646970;"></span>';
            echo '</p>';

            // EC-CPT-013 — next-steps checklist: imported ✓ → connect
            // cart & checkout → set up payments.
            $connectable = wpconvert_wc_connectable_pages();
            $connect_state = function_exists( 'get_option' )
                ? get_option( WPCONVERT_WC_CONNECT_STATE_OPTION, array() )
                : array();
            $payments_url = function_exists( 'admin_url' )
                ? admin_url( 'admin.php?page=wc-settings&tab=checkout' )
                : '#';
            echo '<ol class="wpc-wc-next-steps" style="margin:0 0 0.8em 1.2em">';
            echo '<li style="margin:0 0 0.3em"><span style="color:#00a32a">&#10003;</span> '
                . (int) $imported . ' product' . ( $imported === 1 ? '' : 's' ) . ' imported</li>';
            if ( ! empty( $connectable ) ) {
                echo '<li style="margin:0 0 0.3em">'
                    . '<button type="button" class="button wpc-wc-connect-btn" data-pages="'
                    . esc_attr( implode( ',', array_keys( $connectable ) ) )
                    . '">Connect cart &amp; checkout pages</button>'
                    . ' <span style="color:#646970">Your converted '
                    . esc_html( implode( ' and ', array_map( function ( $s ) { return '/' . $s; }, array_keys( $connectable ) ) ) )
                    . ' page' . ( count( $connectable ) === 1 ? '' : 's' )
                    . ' will show the live WooCommerce cart/checkout (the original design is backed up and restored if you remove the import).</span></li>';
            } elseif ( ! empty( $connect_state ) && is_array( $connect_state ) ) {
                echo '<li style="margin:0 0 0.3em"><span style="color:#00a32a">&#10003;</span> Cart &amp; checkout pages connected</li>';
            } else {
                echo '<li style="margin:0 0 0.3em">WooCommerce\'s own cart &amp; checkout pages are used &mdash; nothing to connect.</li>';
            }
            echo '<li style="margin:0 0 0.3em"><a href="' . esc_url( $payments_url ) . '">Set up payments</a> so customers can check out.</li>';
            echo '</ol>';
            if ( count( $failed ) > 0 ) {
                echo '<details class="wpc-wc-failed-list" style="margin:0 0 0.6em"><summary style="cursor:pointer">Failed items</summary><ul style="margin:0.4em 0 0 1.2em">';
                foreach ( array_slice( $failed, 0, 20 ) as $f ) {
                    echo '<li>' . esc_html( (string) ( $f['name'] ?? '?' ) )
                        . ' <span style="color:#646970">— ' . esc_html( (string) ( $f['reason'] ?? '' ) ) . '</span></li>';
                }
                echo '</ul></details>';
            }
            echo '</div>';
            wpconvert_wc_print_notice_js();
            return;
        }

        // ── State 2: pre-import — button + A8 preview modal ──
        $sample_names = array();
        $categories = array();
        foreach ( $queue as $entry ) {
            $item = $entry['item'];
            $nm = '';
            foreach ( array( 'name', 'heading', 'title' ) as $k ) {
                foreach ( $item as $ik => $iv ) {
                    if ( strtolower( (string) $ik ) === $k && is_string( $iv ) && $iv !== '' ) {
                        $nm = $iv;
                        break;
                    }
                }
                if ( $nm !== '' ) break;
            }
            if ( $nm !== '' && count( $sample_names ) < 5 ) $sample_names[] = $nm;
            foreach ( $item as $ik => $iv ) {
                if ( strtolower( (string) $ik ) === 'category' && is_string( $iv ) && trim( $iv ) !== '' ) {
                    $categories[ trim( $iv ) ] = true;
                }
            }
        }
        $categories = array_keys( $categories );

        echo '<div class="notice notice-info wpc-wc-notice wpc-wc-pre-import">';
        echo '<h3 style="margin:0.6em 0 0.3em">WPConvert found ' . (int) $total
            . ' product' . ( $total === 1 ? '' : 's' ) . ' in your theme.</h3>';
        echo '<p style="margin:0 0 1em">Import them into WooCommerce to manage stock, prices, and orders. '
            . 'Your product pages keep their current design.</p>';
        echo '<p>';
        echo '<button type="button" class="button button-primary wpc-wc-import-btn" data-total="' . (int) $total . '">Import '
            . (int) $total . ' products to WooCommerce</button> ';
        echo '<span class="wpc-wc-status" style="margin-left:0.6em;color:#646970;"></span>';
        echo '</p>';
        echo '<progress class="wpc-wc-progress" max="' . (int) $total . '" value="0" style="display:none;width:320px;"></progress>';

        // A8 — pre-import preview modal (hidden until the button is clicked).
        echo '<div class="wpc-wc-preview-modal" style="display:none;position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,0.55);">';
        echo '<div class="wpc-wc-preview-inner" style="max-width:480px;margin:8vh auto 0;background:#fff;border-radius:6px;padding:1.2em 1.5em;">';
        echo '<h2 style="margin-top:0">About to import ' . (int) $total . ' products to WooCommerce</h2>';
        if ( ! empty( $sample_names ) ) {
            echo '<p class="wpc-wc-preview-samples" style="margin:0 0 0.6em">'
                . esc_html( implode( ', ', $sample_names ) )
                . ( $total > count( $sample_names )
                    ? ' <span style="color:#646970">+ ' . ( (int) $total - count( $sample_names ) ) . ' more</span>'
                    : '' )
                . '</p>';
        }
        if ( ! empty( $categories ) ) {
            echo '<p style="margin:0 0 0.2em"><strong>Categories</strong></p>';
            echo '<ul class="wpc-wc-preview-categories" style="margin:0 0 0.6em 1.2em">';
            foreach ( $categories as $cat ) {
                $exists = function_exists( 'term_exists' ) && term_exists( $cat, 'product_cat' );
                echo '<li>' . esc_html( $cat )
                    . ( $exists
                        ? ' <span class="wpc-wc-cat-exists" style="color:#00a32a">&#10003; exists</span>'
                        : ' <span class="wpc-wc-cat-new" style="color:#646970">will be created</span>' )
                    . '</li>';
            }
            echo '</ul>';
        }
        if ( $mismatch !== null ) {
            echo '<p class="wpc-wc-currency-warning" style="margin:0 0 0.6em;padding:0.5em 0.8em;background:#fff8e5;border-left:3px solid #dba617;">'
                . '<strong>Currency mismatch.</strong> Theme prices look like ' . esc_html( $mismatch['theme'] )
                . ' but the store currency is ' . esc_html( $mismatch['store'] ) . '.</p>';
        }
        if ( $is_truncated ) {
            echo '<p class="wpc-wc-truncation-warning" style="margin:0 0 0.6em;padding:0.5em 0.8em;background:#fff8e5;border-left:3px solid #dba617;">'
                . 'Importing the first ' . (int) $total . ' of ' . (int) $source_total
                . ' products — add the rest in WooCommerce admin.</p>';
        }
        // Rough estimate: ~1.5s per item (sideloads dominate), min 5s.
        $eta = max( 5, (int) ceil( $total * 1.5 ) );
        echo '<p class="wpc-wc-preview-eta" style="margin:0 0 1em;color:#646970">Estimated time: ~' . $eta . ' seconds</p>';
        echo '<p style="margin:0;text-align:right">';
        echo '<button type="button" class="button wpc-wc-cancel-btn">Cancel</button> ';
        echo '<button type="button" class="button button-primary wpc-wc-confirm-btn">Import now</button>';
        echo '</p>';
        echo '</div></div>';
        echo '</div>';
        wpconvert_wc_print_notice_js();
    } catch ( \Throwable $e ) {
        // Notice rendering is best-effort. Never bubble.
        return;
    }
}

/**
 * Inline JS for the WC notice: A8 modal open/close + the chunked-AJAX
 * import loop with <progress> feedback. Vanilla DOM, no dependencies —
 * matches the existing CPT notice JS style.
 */
function wpconvert_wc_print_notice_js() {
    $ajax_url = function_exists( 'admin_url' )
        ? admin_url( 'admin-ajax.php' )
        : '/wp-admin/admin-ajax.php';
    $nonce = function_exists( 'wp_create_nonce' )
        ? wp_create_nonce( 'wpconvert_cpt_nonce' )
        : '';
    ?>
<script>
(function () {
    var AJAX_URL = <?php echo json_encode( $ajax_url ); ?>;
    var NONCE = <?php echo json_encode( $nonce ); ?>;
    var CHUNK = 15;

    function post(body) {
        var fd = new FormData();
        fd.append('action', 'wpconvert_wc_import_products');
        fd.append('nonce', NONCE);
        Object.keys(body || {}).forEach(function (k) { fd.append(k, body[k]); });
        return fetch(AJAX_URL, { method: 'POST', credentials: 'same-origin', body: fd })
            .then(function (r) { return r.json(); });
    }

    function runImport(notice) {
        var statusEl = notice.querySelector('.wpc-wc-status');
        var progress = notice.querySelector('.wpc-wc-progress');
        var processed = 0;
        var created = 0;
        var failed = 0;
        if (progress) progress.style.display = 'inline-block';

        function step(offset) {
            if (statusEl) statusEl.textContent = 'Importing\u2026 (' + processed + ' done)';
            post({ offset: String(offset), limit: String(CHUNK) }).then(function (resp) {
                if (!resp || !resp.success) {
                    var msg = (resp && resp.data && (resp.data.error || resp.data)) || 'unknown';
                    if (statusEl) {
                        statusEl.style.color = '#d63638';
                        statusEl.textContent = 'Error: ' + (typeof msg === 'string' ? msg : JSON.stringify(msg));
                    }
                    return;
                }
                var d = resp.data;
                processed += d.processed_count || 0;
                created += d.created_count || 0;
                failed += (d.failed || []).length;
                if (progress) {
                    progress.max = d.total_count || progress.max;
                    progress.value = Math.min(processed, progress.max);
                }
                if (!d.done) {
                    step(offset + CHUNK);
                    return;
                }
                if (statusEl) {
                    statusEl.style.color = failed ? '#dba617' : '#00a32a';
                    statusEl.textContent = 'Done: ' + created + ' imported'
                        + (failed ? ', ' + failed + ' failed' : '')
                        + ' \u2014 reload to see the dashboard.';
                }
            }).catch(function (err) {
                if (statusEl) {
                    statusEl.style.color = '#d63638';
                    statusEl.textContent = 'Network error: ' + err.message;
                }
            });
        }
        step(0);
    }

    document.querySelectorAll('.wpc-wc-notice').forEach(function (notice) {
        var importBtn = notice.querySelector('.wpc-wc-import-btn');
        var modal = notice.querySelector('.wpc-wc-preview-modal');
        if (importBtn) {
            importBtn.addEventListener('click', function () {
                // A8 — preview first; the AJAX chain only starts on confirm.
                if (modal) { modal.style.display = 'block'; return; }
                runImport(notice);
            });
        }
        if (modal) {
            var cancel = modal.querySelector('.wpc-wc-cancel-btn');
            var confirmBtn = modal.querySelector('.wpc-wc-confirm-btn');
            if (cancel) cancel.addEventListener('click', function () { modal.style.display = 'none'; });
            if (confirmBtn) confirmBtn.addEventListener('click', function () {
                modal.style.display = 'none';
                if (importBtn) importBtn.disabled = true;
                runImport(notice);
            });
        }
        var retryBtn = notice.querySelector('.wpc-wc-retry-btn');
        if (retryBtn) {
            // Re-running the import is safe: succeeded items skip via the
            // idempotency map, so only the failed subset is re-attempted.
            retryBtn.addEventListener('click', function () {
                retryBtn.disabled = true;
                runImport(notice);
            });
        }
        // EC-CPT-012 / undo — bulk-remove imported products (confirm first).
        var removeBtn = notice.querySelector('.wpc-wc-remove-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                var n = removeBtn.getAttribute('data-count') || 'all';
                if (!window.confirm(
                    'Remove ' + n + ' imported product(s) from WooCommerce?\n\n' +
                    'Your pages fall back to their original static design and you can ' +
                    're-import at any time. Products you created yourself are kept.'
                )) return;
                removeBtn.disabled = true;
                var statusEl = notice.querySelector('.wpc-wc-status');
                if (statusEl) { statusEl.style.color = '#646970'; statusEl.textContent = 'Removing\u2026'; }
                var fd = new FormData();
                fd.append('action', 'wpconvert_wc_remove_products');
                fd.append('nonce', NONCE);
                fetch(AJAX_URL, { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (resp) {
                        if (!resp || !resp.success) {
                            var msg = (resp && resp.data && (resp.data.error || resp.data)) || 'unknown';
                            if (statusEl) {
                                statusEl.style.color = '#d63638';
                                statusEl.textContent = 'Error: ' + (typeof msg === 'string' ? msg : JSON.stringify(msg));
                            }
                            removeBtn.disabled = false;
                            return;
                        }
                        if (statusEl) {
                            statusEl.style.color = '#00a32a';
                            statusEl.textContent = 'Removed ' + (resp.data.removed_count || 0)
                                + ' product(s) \u2014 reload to re-import.';
                        }
                    })
                    .catch(function (err) {
                        if (statusEl) {
                            statusEl.style.color = '#d63638';
                            statusEl.textContent = 'Network error: ' + err.message;
                        }
                        removeBtn.disabled = false;
                    });
            });
        }
        // EC-CPT-013 — connect the converted cart/checkout pages to WC.
        var connectBtn = notice.querySelector('.wpc-wc-connect-btn');
        if (connectBtn) {
            connectBtn.addEventListener('click', function () {
                var pages = connectBtn.getAttribute('data-pages') || 'cart, checkout';
                if (!window.confirm(
                    'Connect your converted ' + pages.split(',').join(' and ') + ' page(s) to WooCommerce?\n\n' +
                    'The static page content is replaced with the live WooCommerce cart/checkout. ' +
                    'The original design is backed up and restored if you remove the import.'
                )) return;
                connectBtn.disabled = true;
                var statusEl = notice.querySelector('.wpc-wc-status');
                if (statusEl) { statusEl.style.color = '#646970'; statusEl.textContent = 'Connecting\u2026'; }
                var fd = new FormData();
                fd.append('action', 'wpconvert_wc_connect_pages');
                fd.append('nonce', NONCE);
                fetch(AJAX_URL, { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (resp) {
                        if (!resp || !resp.success) {
                            var msg = (resp && resp.data && (resp.data.error || resp.data)) || 'unknown';
                            if (statusEl) {
                                statusEl.style.color = '#d63638';
                                statusEl.textContent = 'Error: ' + (typeof msg === 'string' ? msg : JSON.stringify(msg));
                            }
                            connectBtn.disabled = false;
                            return;
                        }
                        var n = Object.keys((resp.data && resp.data.connected) || {}).length;
                        if (statusEl) {
                            statusEl.style.color = '#00a32a';
                            statusEl.textContent = 'Connected ' + n + ' page(s) \u2014 your cart and checkout are live.';
                        }
                    })
                    .catch(function (err) {
                        if (statusEl) {
                            statusEl.style.color = '#d63638';
                            statusEl.textContent = 'Network error: ' + err.message;
                        }
                        connectBtn.disabled = false;
                    });
            });
        }
    });
})();
</script>
    <?php
}

/* ─────────────────────────────────────────────
 * 13.8 WC FRONT-END LOOP SWAP  (Ship 4c.6 / A6)
 * ───────────────────────────────────────────── */

/**
 * A6 — expand WC-intent stamped sections into rendered product cards.
 *
 * Runs on the same output-buffer filter as the CPT loop swap (priority
 * 11, after it — the two operate on disjoint section keys because a
 * WC-intent candidate is never CPT-activated).
 *
 * Correctness fix #1: BEFORE the first import the section keeps its
 * original static HTML — installing WC must never blank a page.
 *
 * @param string $buffer
 * @return string
 */
function wpconvert_wc_expand_loop_swap( $buffer ) {
    if ( ! is_string( $buffer ) || $buffer === '' ) return $buffer;

    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return $buffer;
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return $buffer;
    if ( function_exists( 'is_feed' ) && is_feed() ) return $buffer;

    // Tier gate (correctness fix #5) — Starter keeps static HTML.
    if ( ! wpconvert_cpt_should_run() ) return $buffer;

    if ( strpos( $buffer, 'data-wpc-cpt-candidate=' ) === false ) return $buffer;
    if ( ! wpconvert_wc_is_active() ) return $buffer;

    try {
        // Only sections with at least one VALID import are swapped.
        $section_map = array();
        foreach ( wpconvert_wc_get_candidates() as $c ) {
            $sk = (string) $c['section_key'];
            if ( ! preg_match( '/^[a-f0-9]{40}$/', $sk ) ) continue;
            $pids = wpconvert_wc_imported_pids_for_candidate( $c );
            if ( empty( $pids ) ) continue; // pre-import → static fallback
            $section_map[ $sk ] = array( 'candidate' => $c, 'pids' => $pids );
        }
        if ( empty( $section_map ) ) return $buffer;

        $offset = 0;
        $iters = 0;
        while ( $iters++ < 200 ) {
            $next_match = wpconvert_cpt_find_next_section( $buffer, $offset, $section_map );
            if ( $next_match === null ) break;

            $bounds = wpconvert_cpt_find_section_bounds( $buffer, $next_match['marker_pos'] );
            if ( $bounds === null ) {
                $offset = $next_match['marker_pos'] + 40;
                continue;
            }

            $section_html = substr( $buffer, $bounds['start'], $bounds['end'] - $bounds['start'] );
            $entry = $section_map[ $next_match['section_key'] ];
            $new_section = wpconvert_wc_process_section( $section_html, $entry['candidate'], $entry['pids'] );

            if ( $new_section === null || ! is_string( $new_section ) || $new_section === '' ) {
                $offset = $bounds['end'];
                continue;
            }

            $buffer = substr( $buffer, 0, $bounds['start'] )
                . $new_section
                . substr( $buffer, $bounds['end'] );
            $offset = $bounds['start'] + strlen( $new_section );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc_expand_loop_swap: ' . $e->getMessage() );
        }
        return $buffer;
    }

    return $buffer;
}

/**
 * A6 — render one WC-intent section: query the imported products
 * (restricted to the tracked PIDs, source order preserved), clone the
 * stamped template per product, substitute WC data, and stamp the
 * wrapper for the editor deferral (A7).
 *
 * @param string $section_html
 * @param array  $candidate
 * @param int[]  $pids
 * @return string|null
 */
function wpconvert_wc_process_section( $section_html, $candidate, $pids ) {
    if ( ! class_exists( '\DOMDocument' ) || ! class_exists( '\DOMXPath' ) ) return null;
    if ( ! function_exists( 'get_posts' ) || ! function_exists( 'wc_get_product' ) ) return null;

    // Filter-view attrs (same stamping contract as the CPT path).
    $filter_field = '';
    $filter_value = '';
    if ( preg_match( '/<[^>]*data-wpc-cpt-filter-field="([^"]*)"[^>]*>/', $section_html, $mf ) ) {
        $filter_field = (string) $mf[1];
    }
    if ( preg_match( '/<[^>]*data-wpc-cpt-filter-value="([^"]*)"[^>]*>/', $section_html, $mv ) ) {
        $filter_value = html_entity_decode( (string) $mv[1], ENT_QUOTES, 'UTF-8' );
    }

    $query_args = array(
        'post_type'        => 'product',
        'post_status'      => 'publish',
        'posts_per_page'   => 500,
        'post__in'         => array_map( 'intval', $pids ),
        'orderby'          => 'post__in',
        'fields'           => 'ids',
        'suppress_filters' => false,
        'no_found_rows'    => true,
    );
    if ( $filter_field !== '' && $filter_value !== '' ) {
        $lf = strtolower( $filter_field );
        if ( in_array( $lf, array( 'category', 'categories', 'cat' ), true ) ) {
            // Item #16 — filter views map to product_cat (the importer
            // created the terms from the same source values).
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'name',
                    'terms'    => array( $filter_value ),
                ),
            );
        } else {
            // Non-category filters hit the preserved custom-field meta.
            $query_args['meta_query'] = array(
                array(
                    'key'     => wpconvert_cpt_meta_key_for_field( $filter_field ),
                    'value'   => $filter_value,
                    'compare' => '=',
                ),
            );
        }
    }
    $ids = get_posts( $query_args );
    if ( ! is_array( $ids ) || empty( $ids ) ) {
        // Empty-state (incl. filter views with no matches): keep static markup.
        return $section_html;
    }

    $products = array();
    foreach ( $ids as $pid ) {
        $product = wc_get_product( (int) $pid );
        if ( $product ) {
            $products[] = array( 'pid' => (int) $pid, 'product' => $product );
        }
    }
    if ( empty( $products ) ) return $section_html;

    $dom = new \DOMDocument();
    $prev_use_errors = libxml_use_internal_errors( true );
    $ok = $dom->loadHTML(
        '<?xml encoding="UTF-8"?>' . $section_html,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $prev_use_errors );
    if ( ! $ok ) return null;

    $xpath = new \DOMXPath( $dom );
    $wrappers = $xpath->query( '//*[@data-wpc-cpt-candidate]' );
    if ( ! $wrappers || $wrappers->length === 0 ) return null;
    $wrapper = $wrappers->item( 0 );
    if ( ! ( $wrapper instanceof \DOMElement ) ) return null;

    $templates = $xpath->query( './/*[@data-wpc-cpt-item-template="1"]', $wrapper );
    if ( ! $templates || $templates->length === 0 ) return null;
    $template = $templates->item( 0 );

    $new_items = array();
    foreach ( $products as $p ) {
        $clone = $template->cloneNode( true );
        if ( ! ( $clone instanceof \DOMElement ) ) continue;
        $clone->removeAttribute( 'data-wpc-cpt-item-template' );
        wpconvert_wc_substitute_fields_on_node( $clone, $xpath, $p['product'], $p['pid'] );
        // EC-CPT-021 — point the card's detail link at THIS product. Runs
        // before the add-to-cart rewrite so the still-<button> cart control
        // is never mistaken for a product link.
        wpconvert_wc_rewrite_product_links( $clone, $xpath, $p['pid'] );
        // EC-CPT-013 — "Add to cart"-style buttons become live WC links.
        wpconvert_wc_rewrite_add_to_cart_buttons( $clone, $xpath, $p['product'], $p['pid'] );
        wpconvert_cpt_strip_per_item_editor_ids( $clone, $xpath );
        wpconvert_cpt_strip_field_markers( $clone, $xpath );
        // A7 — per-card deep link for the editor's "Manage in WooCommerce".
        $admin_url = function_exists( 'admin_url' )
            ? admin_url( 'post.php?post=' . $p['pid'] . '&action=edit' )
            : '/wp-admin/post.php?post=' . $p['pid'] . '&action=edit';
        $clone->setAttribute( 'data-wpc-wc-admin-url', $admin_url );
        $new_items[] = $clone;
    }
    if ( empty( $new_items ) ) return null;

    while ( $wrapper->firstChild ) {
        $wrapper->removeChild( $wrapper->firstChild );
    }
    foreach ( $new_items as $item ) {
        $wrapper->appendChild( $item );
    }
    // A7 — the editor's inline-edit overlay defers to WC admin on
    // anything inside this wrapper.
    $wrapper->setAttribute( 'data-wpc-wc-managed', '1' );

    $serialized = $dom->saveHTML();
    if ( ! is_string( $serialized ) ) return null;
    $serialized = preg_replace( '/^<\?xml[^?]*\?>\s*/', '', $serialized );
    $serialized = rtrim( $serialized, "\r\n" );
    $serialized = wpconvert_cpt_restore_svg_attr_case( $serialized );
    if ( $serialized === '' || $serialized === null ) return null;
    return $serialized;
}

/**
 * A6 — per-clone field substitution from WC product data + preserved
 * custom-field meta. Field readers per the plan:
 *
 *   name/title/heading        → get_name()
 *   description/desc/summary  → get_short_description() ?: get_description()
 *   price-family              → get_price_html() (sale renders stricken)
 *   image                     → featured-image URL on the <img> src
 *   category/categories       → wc_get_product_category_list()
 *   anything else             → get_post_meta(_wpc_field_{key})
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @param object      $product  WC_Product
 * @param int         $pid
 * @return void
 */
function wpconvert_wc_substitute_fields_on_node( $node, $xpath, $product, $pid ) {
    $field_nodes = $xpath->query( './/*[@data-wpc-cpt-field]', $node );
    $image_handled = false;

    foreach ( ( $field_nodes ?: array() ) as $el ) {
        if ( ! ( $el instanceof \DOMElement ) ) continue;
        $key = $el->getAttribute( 'data-wpc-cpt-field' );
        if ( $key === '' ) continue;
        $lk = strtolower( $key );
        $tag = strtolower( $el->nodeName );

        if ( $tag === 'img' || in_array( $lk, array( 'image', 'img', 'photo', 'picture', 'thumbnail' ), true ) ) {
            $image_handled = true;
            $alt = (string) $product->get_name();
            if ( $alt !== '' && $el->hasAttribute( 'alt' ) ) {
                $el->setAttribute( 'alt', $alt );
            }
            $att_id = function_exists( 'get_post_meta' )
                ? (int) get_post_meta( $pid, '_thumbnail_id', true )
                : 0;
            if ( $att_id > 0 ) {
                $url = function_exists( 'wpconvert_cpt_get_attachment_url_safe' )
                    ? wpconvert_cpt_get_attachment_url_safe( $att_id )
                    : ( function_exists( 'wp_get_attachment_url' ) ? wp_get_attachment_url( $att_id ) : '' );
                if ( is_string( $url ) && $url !== ''
                    && ( filter_var( $url, FILTER_VALIDATE_URL ) || strpos( $url, '/' ) === 0 ) ) {
                    if ( $el->hasAttribute( 'src' ) ) {
                        $el->setAttribute( 'src', $url );
                    }
                    if ( $el->hasAttribute( 'srcset' ) ) {
                        $el->removeAttribute( 'srcset' );
                    }
                }
            }
            continue;
        }

        if ( in_array( $lk, array( 'name', 'title', 'heading' ), true ) ) {
            wpconvert_wc_replace_field_text( $el, (string) $product->get_name() );
            continue;
        }

        if ( in_array( $lk, array( 'description', 'desc', 'summary', 'body' ), true ) ) {
            $desc = (string) $product->get_short_description();
            if ( $desc === '' ) $desc = (string) $product->get_description();
            wpconvert_wc_replace_field_text( $el, $desc );
            continue;
        }

        if ( in_array( $lk, array( 'price', 'cost', 'regularprice', 'saleprice', 'originalprice' ), true ) ) {
            wpconvert_wc_replace_field_html( $el, (string) $product->get_price_html() );
            continue;
        }

        if ( in_array( $lk, array( 'category', 'categories' ), true ) ) {
            $cats = function_exists( 'wc_get_product_category_list' )
                ? (string) wc_get_product_category_list( $pid )
                : '';
            if ( $cats !== '' ) {
                wpconvert_wc_replace_field_html( $el, $cats );
            }
            continue;
        }

        // Custom theme field preserved at import (correctness fix #7).
        $value = function_exists( 'get_post_meta' )
            ? get_post_meta( $pid, wpconvert_cpt_meta_key_for_field( $key ), true )
            : '';
        if ( $value === '' || $value === null || is_array( $value ) ) continue;
        wpconvert_wc_replace_field_text( $el, (string) $value );
    }

    // EC-CPT-015 — unstamped-image heal. The build-time stamper can miss
    // the image field (Vite-hashed src vs. un-hashed manifest value —
    // neon-elite shipped exactly that), leaving every clone frozen on the
    // template card's image. When no image field was stamped, the clone
    // contains exactly ONE <img>, and the product has a real featured
    // image, swapping that img is unambiguous.
    if ( ! $image_handled ) {
        wpconvert_wc_substitute_sole_image( $node, $xpath, $product, $pid );
    }
}

/**
 * EC-CPT-015 — replace the clone's single un-stamped <img> with the
 * product's featured image. No-op unless the clone has exactly one img
 * AND the product has a thumbnail.
 *
 * @param \DOMElement $node
 * @param \DOMXPath   $xpath
 * @param object      $product  WC_Product
 * @param int         $pid
 * @return void
 */
function wpconvert_wc_substitute_sole_image( $node, $xpath, $product, $pid ) {
    $att_id = function_exists( 'get_post_meta' )
        ? (int) get_post_meta( $pid, '_thumbnail_id', true )
        : 0;
    if ( $att_id <= 0 ) return;

    $imgs = $xpath->query( './/img', $node );
    if ( ! $imgs || $imgs->length !== 1 ) return;
    $el = $imgs->item( 0 );
    if ( ! ( $el instanceof \DOMElement ) ) return;

    $url = function_exists( 'wpconvert_cpt_get_attachment_url_safe' )
        ? wpconvert_cpt_get_attachment_url_safe( $att_id )
        : ( function_exists( 'wp_get_attachment_url' ) ? wp_get_attachment_url( $att_id ) : '' );
    if ( ! is_string( $url ) || $url === ''
        || ( ! filter_var( $url, FILTER_VALIDATE_URL ) && strpos( $url, '/' ) !== 0 ) ) {
        return;
    }
    if ( $el->hasAttribute( 'src' ) ) {
        $el->setAttribute( 'src', $url );
    }
    if ( $el->hasAttribute( 'srcset' ) ) {
        $el->removeAttribute( 'srcset' );
    }
    $alt = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : '';
    if ( $alt !== '' && $el->hasAttribute( 'alt' ) ) {
        $el->setAttribute( 'alt', $alt );
    }
}

/**
 * Text substitution honoring data-wpc-cpt-original (so format suffixes
 * like " kr" survive), mirroring the CPT path.
 *
 * @param \DOMElement $el
 * @param string      $new_value
 */
function wpconvert_wc_replace_field_text( $el, $new_value ) {
    if ( $new_value === '' ) return;
    $original = $el->getAttribute( 'data-wpc-cpt-original' );
    if ( $original === '' || ! wpconvert_cpt_replace_text_in_node( $el, $original, $new_value ) ) {
        wpconvert_cpt_replace_element_text( $el, $new_value );
    }
}

/**
 * Replace the element's children with an HTML fragment (price HTML,
 * category links). Falls back to plain-text substitution when the
 * fragment can't be parsed.
 *
 * @param \DOMElement $el
 * @param string      $html
 */
function wpconvert_wc_replace_field_html( $el, $html ) {
    if ( $html === '' ) return;
    $doc = $el->ownerDocument;
    $fragment = $doc->createDocumentFragment();
    $appended = false;
    try {
        // appendXML needs well-formed markup; WC's price/category HTML is.
        $prev = libxml_use_internal_errors( true );
        $appended = @$fragment->appendXML( $html );
        libxml_clear_errors();
        libxml_use_internal_errors( $prev );
    } catch ( \Throwable $e ) {
        $appended = false;
    }
    if ( ! $appended ) {
        wpconvert_wc_replace_field_text( $el, trim( strip_tags( $html ) ) );
        return;
    }
    while ( $el->firstChild ) {
        $el->removeChild( $el->firstChild );
    }
    $el->appendChild( $fragment );
}

/* ─────────────────────────────────────────────
 * 13.8b WC CART WIRING  (EC-CPT-013)
 *
 * Runtime-only cart integration: header cart-icon links resolve to the
 * live WooCommerce cart, product-card "Add to cart" buttons become real
 * WC add-to-cart links, and the converted /cart + /checkout pages can be
 * connected to WooCommerce with one click.
 *
 * Every entry point early-returns through wpconvert_wc_cart_wiring_enabled()
 * — non-ecom themes (empty import map) and Starter installs are
 * byte-identical and untouched.
 * ───────────────────────────────────────────── */

if ( ! defined( 'WPCONVERT_WC_CONNECT_STATE_OPTION' ) ) {
    define( 'WPCONVERT_WC_CONNECT_STATE_OPTION', 'wpconvert_wc_connect_state' );
}

/**
 * EC-CPT-013 — the shared cart-wiring gate.
 *
 *   1. Tier gate (Starter excluded, same as the loop swap).
 *   2. WooCommerce active on this request.
 *   3. At least one product actually imported by WPConvert.
 *
 * Non-ecom sites fail (3) even when the owner installs WooCommerce for
 * unrelated reasons: nothing was ever imported, so the map is empty.
 *
 * @return bool
 */
function wpconvert_wc_cart_wiring_enabled() {
    if ( ! wpconvert_cpt_should_run() ) return false;
    if ( ! wpconvert_wc_is_active() ) return false;
    $map = wpconvert_wc_get_imports();
    return is_array( $map ) && ! empty( $map );
}

/**
 * EC-CPT-013 — does an anchor href point at the converted theme's
 * cart-ish route?
 *
 * Mirrors the EC-NAV-ICON-001 route list in generate.js and covers both
 * of its output shapes: home_url('/cart/') (route matched → absolute,
 * trailing slash) and the bare literal '/cart' fallback. Only relative
 * or same-host hrefs qualify — external checkout providers (Shopify,
 * Gumroad, Lemon Squeezy, …) are never rewritten. '/shop' is an archive
 * link, not a cart, and is deliberately absent from the list.
 *
 * @param string $href
 * @return bool
 */
function wpconvert_wc_href_is_cart_path( $href ) {
    $href = trim( (string) $href );
    if ( $href === '' || $href[0] === '#' ) return false;
    $parts = function_exists( 'wp_parse_url' ) ? wp_parse_url( $href ) : @parse_url( $href );
    if ( ! is_array( $parts ) ) return false;
    if ( ! empty( $parts['scheme'] )
        && ! in_array( strtolower( (string) $parts['scheme'] ), array( 'http', 'https' ), true ) ) {
        return false;
    }
    if ( ! empty( $parts['host'] ) ) {
        $home_host = '';
        if ( function_exists( 'home_url' ) ) {
            $home = function_exists( 'wp_parse_url' )
                ? wp_parse_url( home_url( '/' ) )
                : @parse_url( home_url( '/' ) );
            if ( is_array( $home ) && ! empty( $home['host'] ) ) {
                $home_host = strtolower( (string) $home['host'] );
            }
        }
        if ( $home_host === '' || strtolower( (string) $parts['host'] ) !== $home_host ) {
            return false;
        }
    }
    $path = isset( $parts['path'] ) ? strtolower( rtrim( (string) $parts['path'], '/' ) ) : '';
    if ( $path === '' ) return false;
    return in_array(
        $path,
        array( '/cart', '/bag', '/basket', '/shopping-cart', '/shopping-bag' ),
        true
    );
}

/**
 * EC-CPT-013 — rewrite cart-route anchors to wc_get_cart_url().
 *
 * Runs on the same output-buffer filter as the loop swaps (priority 12,
 * after the WC swap at 11). Attribute-only change: tag, classes, and
 * inner HTML are untouched, so the editor's CTA marking and saved
 * data-wpc-id edits (which run after this filter) are unaffected —
 * a user's saved href edit applies after ours and wins.
 *
 * @param string $buffer
 * @return string
 */
function wpconvert_wc_wire_cart_links( $buffer ) {
    if ( ! is_string( $buffer ) || $buffer === '' ) return $buffer;

    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return $buffer;
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return $buffer;
    if ( function_exists( 'is_feed' ) && is_feed() ) return $buffer;

    // Cheap pre-check before any gating/regex work.
    if ( stripos( $buffer, 'cart' ) === false
        && stripos( $buffer, 'bag' ) === false
        && stripos( $buffer, 'basket' ) === false ) {
        return $buffer;
    }

    if ( ! wpconvert_wc_cart_wiring_enabled() ) return $buffer;
    if ( ! function_exists( 'wc_get_cart_url' ) ) return $buffer;

    try {
        $cart_url = (string) wc_get_cart_url();
        if ( $cart_url === '' ) return $buffer;

        $out = preg_replace_callback(
            '/(<a\b[^>]*?\bhref=)(["\'])([^"\']*)\2/i',
            function ( $m ) use ( $cart_url ) {
                if ( ! wpconvert_wc_href_is_cart_path( $m[3] ) ) return $m[0];
                $url = function_exists( 'esc_url' ) ? esc_url( $cart_url ) : $cart_url;
                return $m[1] . $m[2] . $url . $m[2];
            },
            $buffer
        );
        return is_string( $out ) && $out !== '' ? $out : $buffer;
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc_wire_cart_links: ' . $e->getMessage() );
        }
        return $buffer;
    }
}

/**
 * EC-CPT-021 — point a cloned product card's detail link at the right
 * product.
 *
 * The WC loop swap clones the FIRST card (the template) once per product,
 * then substitutes name/price/image/etc. via [data-wpc-cpt-field] stamps.
 * But the card's product-detail anchor — the <a> wrapping the image
 * and/or the title — carries an href baked in at conversion time: the
 * TEMPLATE product's URL (e.g. /product/iphone-15-pro-max). That href is
 * not a field stamp, so substitution leaves it untouched and every cloned
 * card links back to the template product. This rewrites those anchors to
 * each clone's own permalink.
 *
 * Detection is structural and matches the universal AI product-card
 * shape: the product link is the anchor that wraps the card image or the
 * title heading. Cart/bag anchors and in-page (#) hrefs are left alone.
 * Must run BEFORE wpconvert_wc_rewrite_add_to_cart_buttons() so the cart
 * control (still a <button> at this point) is never matched.
 *
 * @param \DOMElement $clone
 * @param \DOMXPath   $xpath
 * @param int         $pid
 * @return void
 */
function wpconvert_wc_rewrite_product_links( $clone, $xpath, $pid ) {
    $permalink = function_exists( 'get_permalink' ) ? get_permalink( (int) $pid ) : '';
    if ( ! is_string( $permalink ) || $permalink === '' ) return;

    // Anchor wrapping the card image and/or the title heading = the
    // product-detail link in virtually every AI-built product card.
    $anchors = $xpath->query(
        './/a[.//img] | .//a[.//h1 or .//h2 or .//h3 or .//h4 or .//h5 or .//h6]',
        $clone
    );
    if ( ! $anchors || $anchors->length === 0 ) return;

    $url = function_exists( 'esc_url' ) ? esc_url( $permalink ) : $permalink;
    $seen = array();
    foreach ( $anchors as $a ) {
        if ( ! ( $a instanceof \DOMElement ) ) continue;
        // An anchor can satisfy both predicates (wraps img AND heading).
        $hash = spl_object_hash( $a );
        if ( isset( $seen[ $hash ] ) ) continue;
        $seen[ $hash ] = true;

        if ( ! $a->hasAttribute( 'href' ) ) continue;
        $href = trim( (string) $a->getAttribute( 'href' ) );
        if ( $href === '' || $href[0] === '#' ) continue;
        // Never clobber a cart/bag/basket link.
        if ( wpconvert_wc_href_is_cart_path( $href ) ) continue;

        $a->setAttribute( 'href', $url );
    }
}

/**
 * EC-CPT-013 — rewrite a cloned product card's "Add to cart"-style
 * button into a real WooCommerce add-to-cart link.
 *
 * Called from the WC loop swap's clone loop, so it only ever runs inside
 * data-wpc-wc-managed wrappers the plugin fully owns. The original
 * element's class list is preserved (design intact); the GET href works
 * with zero JS and WC's wc-add-to-cart script upgrades it to AJAX.
 *
 * Conservative on purpose: exact-phrase text match only, and only for
 * purchasable in-stock products — anything else leaves the card as-is.
 *
 * @param \DOMElement $clone
 * @param \DOMXPath   $xpath
 * @param object      $product  WC_Product
 * @param int         $pid
 * @return void
 */
function wpconvert_wc_rewrite_add_to_cart_buttons( $clone, $xpath, $product, $pid ) {
    if ( ! is_object( $product ) ) return;
    if ( method_exists( $product, 'is_purchasable' ) && ! $product->is_purchasable() ) return;
    if ( method_exists( $product, 'is_in_stock' ) && ! $product->is_in_stock() ) return;

    $nodes = $xpath->query( './/button | .//a', $clone );
    if ( ! $nodes || $nodes->length === 0 ) return;

    $phrases = array( 'add to cart', 'add to bag', 'add to basket', 'buy now' );
    // EC-CPT-017 — compact product-card buttons. AI builders frequently
    // label the card button just "Add" (or "Buy") next to a cart icon
    // (neon-elite: <svg class="lucide lucide-shopping-cart"/>Add). The
    // short verbs alone are too ambiguous ("Add" could be "Add note"),
    // so they only match when the element ALSO contains a shopping
    // cart/bag/basket icon.
    $short_verbs = array( 'add', 'buy', '+ add', 'add +' );

    // Snapshot first — replacing nodes while iterating a live NodeList
    // is undefined behavior.
    $targets = array();
    foreach ( $nodes as $el ) {
        if ( ! ( $el instanceof \DOMElement ) ) continue;
        $text = strtolower( trim( preg_replace( '/\s+/u', ' ', (string) $el->textContent ) ) );
        if ( in_array( $text, $phrases, true ) ) {
            $targets[] = $el;
            continue;
        }
        if ( in_array( $text, $short_verbs, true )
            && wpconvert_wc_element_has_cart_icon( $el, $xpath ) ) {
            $targets[] = $el;
        }
    }
    if ( empty( $targets ) ) return;

    $url = method_exists( $product, 'add_to_cart_url' )
        ? (string) $product->add_to_cart_url()
        : '';
    if ( $url === '' ) $url = '?add-to-cart=' . (int) $pid;

    foreach ( $targets as $el ) {
        $doc = $el->ownerDocument;
        if ( ! $doc || ! $el->parentNode ) continue;
        $a = $doc->createElement( 'a' );
        foreach ( $el->attributes as $attr ) {
            $an = strtolower( (string) $attr->name );
            // Drop button-only / event attributes; keep class, style, etc.
            if ( in_array( $an, array( 'type', 'href', 'disabled', 'onclick', 'form' ), true ) ) continue;
            $a->setAttribute( $attr->name, $attr->value );
        }
        $cls = trim( $a->getAttribute( 'class' ) . ' add_to_cart_button ajax_add_to_cart' );
        $a->setAttribute( 'class', $cls );
        $a->setAttribute( 'href', $url );
        $a->setAttribute( 'data-product_id', (string) (int) $pid );
        $a->setAttribute( 'data-quantity', '1' );
        $a->setAttribute( 'rel', 'nofollow' );
        $name = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : '';
        if ( $name !== '' ) {
            $a->setAttribute( 'aria-label', 'Add ' . $name . ' to your cart' );
        }
        while ( $el->firstChild ) {
            $a->appendChild( $el->firstChild );
        }
        $el->parentNode->replaceChild( $a, $el );
    }
}

/**
 * EC-CPT-017 — does this button/anchor contain a shopping cart/bag/
 * basket icon? Matches the icon-library class conventions the converter
 * already relies on for header cart icons (lucide-shopping-cart,
 * fa-cart-shopping, generic cart/bag/basket class tokens) on any
 * descendant svg/i/span.
 *
 * @param \DOMElement $el
 * @param \DOMXPath   $xpath
 * @return bool
 */
function wpconvert_wc_element_has_cart_icon( $el, $xpath ) {
    $icons = $xpath->query( './/svg | .//i | .//span', $el );
    if ( ! $icons ) return false;
    foreach ( $icons as $icon ) {
        if ( ! ( $icon instanceof \DOMElement ) ) continue;
        $cls = strtolower( (string) $icon->getAttribute( 'class' ) );
        if ( $cls === '' ) continue;
        if ( preg_match( '/(?:^|[\s-])(?:shopping-)?(?:cart|bag|basket)(?:$|[\s\/-])/', $cls ) ) {
            return true;
        }
    }
    return false;
}

/**
 * EC-CPT-013 — front-end script enqueues. WC's wc-add-to-cart upgrades
 * the GET links to AJAX; wc-cart-fragments keeps the badge live.
 */
function wpconvert_wc_enqueue_cart_assets() {
    if ( function_exists( 'is_admin' ) && is_admin() ) return;
    if ( ! wpconvert_wc_cart_wiring_enabled() ) return;
    if ( ! function_exists( 'wp_enqueue_script' ) ) return;
    wp_enqueue_script( 'wc-add-to-cart' );
    wp_enqueue_script( 'wc-cart-fragments' );
}

/**
 * EC-CPT-013 — the rendered badge span. Single source of truth shared
 * by the footer injector and the cart-fragments filter so AJAX
 * replacements stay shape-identical.
 *
 * @param int $count
 * @return string
 */
function wpconvert_wc_render_cart_count_span( $count ) {
    $count = (int) $count;
    $hidden = $count > 0 ? '' : ' style="display:none"';
    return '<span class="wpc-wc-cart-count"' . $hidden . '>' . $count . '</span>';
}

/**
 * EC-CPT-013 — current cart item count, defensively.
 *
 * @return int
 */
function wpconvert_wc_get_cart_count() {
    try {
        if ( ! function_exists( 'WC' ) ) return 0;
        $wc = WC();
        if ( ! $wc || ! isset( $wc->cart ) || ! is_object( $wc->cart ) ) return 0;
        if ( ! method_exists( $wc->cart, 'get_cart_contents_count' ) ) return 0;
        return (int) $wc->cart->get_cart_contents_count();
    } catch ( \Throwable $e ) {
        return 0;
    }
}

/**
 * EC-CPT-013 — keep the badge live: WC's cart-fragments JS replaces
 * every element matching the fragment key after each AJAX add-to-cart.
 *
 * @param array $fragments
 * @return array
 */
function wpconvert_wc_cart_count_fragment( $fragments ) {
    if ( ! is_array( $fragments ) ) $fragments = array();
    if ( ! wpconvert_wc_cart_wiring_enabled() ) return $fragments;
    $fragments['span.wpc-wc-cart-count'] = wpconvert_wc_render_cart_count_span(
        wpconvert_wc_get_cart_count()
    );
    return $fragments;
}

/**
 * EC-CPT-013 — minimal badge CSS (wp_head, gated).
 */
function wpconvert_wc_print_cart_badge_css() {
    if ( function_exists( 'is_admin' ) && is_admin() ) return;
    if ( ! wpconvert_wc_cart_wiring_enabled() ) return;
    echo '<style id="wpc-wc-cart-badge-css">'
        . '.wpc-wc-cart-anchor{position:relative}'
        . '.wpc-wc-cart-count{position:absolute;top:-6px;right:-8px;min-width:16px;height:16px;'
        . 'padding:0 4px;border-radius:8px;background:#d63638;color:#fff;font-size:11px;'
        . 'font-weight:600;line-height:16px;text-align:center;pointer-events:none}'
        // EC-CPT-018 — WC form-field readability. Tailwind preflight sets
        // `input{color:inherit;padding:0}`, so on dark themes WooCommerce's
        // quantity/coupon/checkout inputs render WHITE text on their
        // default WHITE background (neon-elite: the qty number was
        // invisible). Scoped to .woocommerce form fields only.
        . '.woocommerce .quantity .qty,'
        . '.woocommerce form .form-row .input-text,'
        . '.woocommerce form .form-row select,'
        . '.woocommerce form .form-row textarea,'
        . '.woocommerce table.cart td.actions .coupon .input-text'
        . '{color:#1e1e1e;background-color:#fff;padding:.35em .5em;border:1px solid #c3c4c7;border-radius:4px}'
        // EC-CPT-020 — converted themes pin the navbar `position:fixed;top:0`,
        // but WooCommerce-rendered pages (single product, shop archive,
        // cart, checkout) have no top offset, so the header overlapped the
        // product title (neon-elite report). Clear it with top padding on
        // WC body classes only — the theme's own pages are untouched.
        . 'body.woocommerce-page,body.woocommerce{padding-top:7rem}'
        . '</style>' . "\n";
}

/**
 * EC-CPT-013 — badge injector (wp_footer, gated).
 *
 * Injected CLIENT-SIDE on purpose: the editor applies saved data-wpc-id
 * edits to the buffer with text-run regexes, and a buffer-injected count
 * span could be clobbered by a saved button-text edit. Appending after
 * render sidesteps that interplay entirely; the fragments filter keeps
 * the number live afterwards.
 */
function wpconvert_wc_print_cart_badge_js() {
    if ( function_exists( 'is_admin' ) && is_admin() ) return;
    if ( ! wpconvert_wc_cart_wiring_enabled() ) return;
    $count = wpconvert_wc_get_cart_count();
    ?>
<script id="wpc-wc-cart-badge-js">
(function () {
    var COUNT = <?php echo (int) $count; ?>;
    function hasNumericBadge(a) {
        var els = a.querySelectorAll('span,div,em,i,b,strong,sup');
        for (var i = 0; i < els.length; i++) {
            var t = (els[i].textContent || '').trim();
            if (t !== '' && /^\d+$/.test(t)) return true;
        }
        return false;
    }
    function isAddToCart(a) {
        // EC-CPT-019 — EC-CPT-017 turns product-card "Add" buttons into
        // <a class="add_to_cart_button"><svg class="lucide-shopping-cart">…
        // which this selector also matches. Adding the cart-COUNT badge to
        // every Add button showed "(2)" on each card (neon-elite report).
        // The header cart link is the only intended target; exclude any
        // add-to-cart anchor.
        if (a.classList && a.classList.contains('add_to_cart_button')) return true;
        if (a.getAttribute && a.getAttribute('data-product_id')) return true;
        var href = a.getAttribute ? (a.getAttribute('href') || '') : '';
        return href.indexOf('add-to-cart=') !== -1;
    }
    var svgs = document.querySelectorAll(
        'a svg.lucide-shopping-cart, a svg.lucide-shopping-bag'
    );
    var seen = [];
    for (var i = 0; i < svgs.length; i++) {
        var a = svgs[i].closest ? svgs[i].closest('a') : null;
        if (!a || seen.indexOf(a) !== -1) continue;
        seen.push(a);
        if (isAddToCart(a)) continue;
        if (a.querySelector('.wpc-wc-cart-count')) continue;
        if (hasNumericBadge(a)) continue;
        a.classList.add('wpc-wc-cart-anchor');
        var span = document.createElement('span');
        span.className = 'wpc-wc-cart-count';
        span.textContent = String(COUNT);
        if (!COUNT) span.style.display = 'none';
        a.appendChild(span);
    }
})();
</script>
    <?php
}

/* ── EC-CPT-013 — one-click cart/checkout page connect ─────────────── */

/**
 * Converted pages (created from routes.json by the theme) that can be
 * connected to WooCommerce: slug → page ID, skipping pages WC already
 * uses.
 *
 * @return array  e.g. array( 'cart' => 12, 'checkout' => 13 )
 */
function wpconvert_wc_connectable_pages() {
    if ( ! function_exists( 'get_page_by_path' ) ) return array();
    $out = array();
    $map = array(
        'cart'     => 'woocommerce_cart_page_id',
        'checkout' => 'woocommerce_checkout_page_id',
    );
    foreach ( $map as $slug => $option ) {
        $page = get_page_by_path( $slug );
        if ( ! $page || empty( $page->ID ) ) continue;
        $assigned = function_exists( 'get_option' ) ? (int) get_option( $option, 0 ) : 0;
        if ( $assigned === (int) $page->ID ) continue; // already connected
        $out[ $slug ] = (int) $page->ID;
    }
    return $out;
}

/**
 * EC-CPT-013 — connect the converted /cart and /checkout pages to
 * WooCommerce.
 *
 * Per page: back up post_content + _wp_page_template into
 * `_wpc_wc_connect_backup` meta (first connect wins — never overwrite an
 * existing backup), swap the content for the WC shortcode, clear the
 * page template (page-cart.php would shadow the shortcode), and point
 * WC's page-ID option at it. WC's previous page IDs are stored in
 * `wpconvert_wc_connect_state` so a disconnect restores exactly what WC
 * had before.
 *
 * @return array {connected, skipped_count}
 */
function wpconvert_wc_run_connect_pages() {
    $pages = wpconvert_wc_connectable_pages();
    $shortcodes = array(
        'cart'     => '[woocommerce_cart]',
        'checkout' => '[woocommerce_checkout]',
    );
    $options = array(
        'cart'     => 'woocommerce_cart_page_id',
        'checkout' => 'woocommerce_checkout_page_id',
    );

    $state = function_exists( 'get_option' )
        ? get_option( WPCONVERT_WC_CONNECT_STATE_OPTION, array() )
        : array();
    if ( ! is_array( $state ) ) $state = array();
    if ( ! isset( $state['pages'] ) || ! is_array( $state['pages'] ) ) {
        $state['pages'] = array();
    }

    $connected = array();
    $skipped = 0;

    foreach ( $pages as $slug => $pid ) {
        $page = function_exists( 'get_post' ) ? get_post( $pid ) : null;
        if ( ! $page ) { $skipped++; continue; }

        $existing_backup = function_exists( 'get_post_meta' )
            ? get_post_meta( $pid, '_wpc_wc_connect_backup', true )
            : '';
        if ( ! is_array( $existing_backup ) || empty( $existing_backup ) ) {
            $backup = array(
                'post_content' => is_object( $page ) ? (string) ( $page->post_content ?? '' ) : '',
                'template'     => function_exists( 'get_post_meta' )
                    ? (string) get_post_meta( $pid, '_wp_page_template', true )
                    : '',
            );
            if ( function_exists( 'update_post_meta' ) ) {
                update_post_meta( $pid, '_wpc_wc_connect_backup', $backup );
            }
        }

        if ( function_exists( 'wp_update_post' ) ) {
            wp_update_post( array( 'ID' => $pid, 'post_content' => $shortcodes[ $slug ] ) );
        }
        if ( function_exists( 'delete_post_meta' ) ) {
            delete_post_meta( $pid, '_wp_page_template' );
        } elseif ( function_exists( 'update_post_meta' ) ) {
            update_post_meta( $pid, '_wp_page_template', '' );
        }

        // First connect records WC's prior page ID for restore.
        if ( ! array_key_exists( $options[ $slug ], $state ) ) {
            $state[ $options[ $slug ] ] = function_exists( 'get_option' )
                ? (int) get_option( $options[ $slug ], 0 )
                : 0;
        }
        if ( function_exists( 'update_option' ) ) {
            update_option( $options[ $slug ], $pid, false );
        }
        $state['pages'][ $slug ] = $pid;
        $connected[ $slug ] = $pid;
    }

    if ( ! empty( $connected ) && function_exists( 'update_option' ) ) {
        update_option( WPCONVERT_WC_CONNECT_STATE_OPTION, $state, false );
    }

    return array(
        'connected'     => $connected,
        'skipped_count' => $skipped,
    );
}

/**
 * EC-CPT-018 — route connected cart/checkout pages to the plugin's
 * render template.
 *
 * The connect flow clears _wp_page_template, but WP's template hierarchy
 * STILL picks the theme's page-cart.php / page-checkout.php by SLUG —
 * and those converted templates are static snapshots that never call
 * the_content(), so the WC shortcode would never render (neon-elite:
 * the connected cart page kept showing the placeholder copy and never
 * the live cart). For pages recorded in the connect state we hand WP the
 * bundled wpc-wc-connected-page.php instead: theme header/footer around
 * the live shortcode output.
 *
 * @param string $template  Resolved template path from the hierarchy.
 * @return string
 */
function wpconvert_wc_connected_page_template( $template ) {
    try {
        if ( ! function_exists( 'is_page' ) || ! is_page() ) return $template;
        if ( ! wpconvert_cpt_should_run() ) return $template;
        if ( ! wpconvert_wc_is_active() ) return $template;

        $state = function_exists( 'get_option' )
            ? get_option( WPCONVERT_WC_CONNECT_STATE_OPTION, array() )
            : array();
        if ( ! is_array( $state ) || empty( $state['pages'] ) || ! is_array( $state['pages'] ) ) {
            return $template;
        }

        $page_id = function_exists( 'get_queried_object_id' ) ? (int) get_queried_object_id() : 0;
        if ( $page_id <= 0 ) return $template;
        if ( ! in_array( $page_id, array_map( 'intval', $state['pages'] ), true ) ) {
            return $template;
        }

        $plugin_template = __DIR__ . '/wpc-wc-connected-page.php';
        if ( ! file_exists( $plugin_template ) ) return $template;
        return $plugin_template;
    } catch ( \Throwable $e ) {
        return $template;
    }
}

/**
 * EC-CPT-013 — undo the connect: restore each page's original content +
 * template from the backup meta and re-point WC's page-ID options at
 * whatever they were before. Called from wpconvert_wc_run_remove() so
 * the bulk-remove leaves the site exactly as it was pre-import.
 *
 * @return int  Pages restored.
 */
function wpconvert_wc_restore_connected_pages() {
    $state = function_exists( 'get_option' )
        ? get_option( WPCONVERT_WC_CONNECT_STATE_OPTION, array() )
        : array();
    if ( ! is_array( $state ) || empty( $state ) ) return 0;

    $restored = 0;
    $pages = ( isset( $state['pages'] ) && is_array( $state['pages'] ) )
        ? $state['pages']
        : array();

    foreach ( $pages as $slug => $pid ) {
        $pid = (int) $pid;
        if ( $pid <= 0 ) continue;
        $backup = function_exists( 'get_post_meta' )
            ? get_post_meta( $pid, '_wpc_wc_connect_backup', true )
            : '';
        if ( ! is_array( $backup ) ) continue;
        if ( function_exists( 'wp_update_post' ) ) {
            wp_update_post( array(
                'ID'           => $pid,
                'post_content' => (string) ( $backup['post_content'] ?? '' ),
            ) );
        }
        $template = (string) ( $backup['template'] ?? '' );
        if ( $template !== '' && function_exists( 'update_post_meta' ) ) {
            update_post_meta( $pid, '_wp_page_template', $template );
        }
        if ( function_exists( 'delete_post_meta' ) ) {
            delete_post_meta( $pid, '_wpc_wc_connect_backup' );
        }
        $restored++;
    }

    foreach ( array( 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id' ) as $opt ) {
        if ( array_key_exists( $opt, $state ) && function_exists( 'update_option' ) ) {
            update_option( $opt, (int) $state[ $opt ], false );
        }
    }
    if ( function_exists( 'delete_option' ) ) {
        delete_option( WPCONVERT_WC_CONNECT_STATE_OPTION );
    }

    return $restored;
}

/**
 * EC-CPT-013 — AJAX endpoint: connect the cart/checkout pages.
 *
 * Security mirrors wpconvert_wc_ajax_remove_products() exactly:
 * tier gate → capability → nonce → WC-active, plus the SAME global lock
 * as the importer. wp_send_json_* stays OUTSIDE try/catch.
 */
function wpconvert_wc_ajax_connect_pages() {
    if ( ! wpconvert_cpt_should_run() ) {
        wp_send_json_error( 'tier-or-version', 403 );
    }
    if ( ! wpconvert_wc_current_user_can_import() ) {
        wp_send_json_error( 'capability', 403 );
    }
    if ( ! function_exists( 'check_ajax_referer' )
        || ! check_ajax_referer( 'wpconvert_cpt_nonce', 'nonce', false ) ) {
        wp_send_json_error( 'nonce', 403 );
    }
    if ( ! wpconvert_wc_is_active() ) {
        wp_send_json_error( 'wc-not-active', 403 );
    }

    if ( ! wpconvert_wc_acquire_import_lock() ) {
        wp_send_json_error(
            array( 'error' => 'locked', 'lock_holder' => wpconvert_wc_get_lock_holder() ),
            409
        );
    }

    $error_message = null;
    $result = null;
    try {
        $result = wpconvert_wc_run_connect_pages();
    } catch ( \Throwable $e ) {
        $error_message = $e->getMessage();
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_wc ajax_connect: ' . $error_message );
        }
    }
    wpconvert_wc_release_import_lock();

    if ( $error_message !== null ) {
        wp_send_json_error( array( 'error' => 'exception', 'message' => $error_message ), 500 );
    }
    wp_send_json_success( $result );
}

/* ─────────────────────────────────────────────
 * 13.9 WC FEATURE COMPAT + WP-CLI  (Ship 4c.6 / A9, A10)
 * ───────────────────────────────────────────── */

/**
 * A10 — declare HPOS (custom order tables) compatibility so WC doesn't
 * flag this plugin as incompatible. We only touch products, never
 * orders, so positive compatibility is safe.
 */
function wpconvert_wc_declare_feature_compat() {
    try {
        if ( ! class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) return;
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables', __FILE__, true
        );
    } catch ( \Throwable $e ) {
        // Declaration is best-effort.
    }
}

/**
 * A9 — register `wp wpconvert-wc` (list / import / status). Same gating
 * pattern as wpconvert_cpt_register_cli_commands().
 */
function wpconvert_wc_register_cli_commands() {
    if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;
    if ( ! wpconvert_cpt_should_run() ) return;
    try {
        if ( class_exists( '\\WP_CLI' ) ) {
            \WP_CLI::add_command( 'wpconvert-wc', 'WPConvert_WC_CLI' );
        }
    } catch ( \Throwable $e ) {
        // CLI command registration is best-effort.
    }
}

// Only define the class when WP_CLI is loaded (same guard as
// WPConvert_CPT_CLI above).
if ( defined( 'WP_CLI' ) && WP_CLI && ! class_exists( 'WPConvert_WC_CLI' ) ) {

    /**
     * Import WPConvert-detected products into WooCommerce via the CLI.
     */
    class WPConvert_WC_CLI {

        /**
         * List WC-intent candidates and their import status.
         *
         * ## OPTIONS
         *
         * [--format=<format>]
         * : table (default) or json.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert-wc list
         *     wp wpconvert-wc list --format=json
         */
        public function list( $args, $assoc_args ) {
            $rows = array();
            foreach ( wpconvert_wc_get_candidates() as $c ) {
                $sk = (string) ( $c['section_key'] ?? '' );
                $items = ( isset( $c['items'] ) && is_array( $c['items'] ) ) ? count( $c['items'] ) : 0;
                $imported = count( wpconvert_wc_imported_pids_for_candidate( $c ) );
                $rows[] = array(
                    'section_key' => substr( $sk, 0, 12 ),
                    'source'      => (string) ( $c['source_array'] ?? '' ),
                    'items'       => $items,
                    'imported'    => $imported,
                    'status'      => $imported >= $items && $items > 0 ? 'imported'
                        : ( $imported > 0 ? 'partial' : 'pending' ),
                );
            }
            if ( empty( $rows ) ) {
                \WP_CLI::log( 'No WooCommerce product candidates detected on this theme.' );
                return;
            }
            $format = isset( $assoc_args['format'] ) ? (string) $assoc_args['format'] : 'table';
            if ( $format === 'json' ) {
                \WP_CLI::log( json_encode( $rows ) );
                return;
            }
            \WP_CLI\Utils\format_items(
                'table', $rows,
                array( 'section_key', 'source', 'items', 'imported', 'status' )
            );
        }

        /**
         * Import detected products into WooCommerce. Idempotent — items
         * already imported are skipped; deleted products are re-imported.
         *
         * ## OPTIONS
         *
         * [--dry-run]
         * : Print what would happen without writing anything.
         *
         * [--limit=<n>]
         * : Process at most N items (default: all).
         *
         * [--offset=<n>]
         * : Skip the first N items (default: 0).
         *
         * ## EXAMPLES
         *
         *     wp wpconvert-wc import
         *     wp wpconvert-wc import --dry-run
         *     wp wpconvert-wc import --limit=5 --offset=10
         */
        public function import( $args, $assoc_args ) {
            if ( ! wpconvert_wc_is_active() ) {
                \WP_CLI::error( 'WooCommerce is not active. Install and activate it first.' );
            }
            $dry_run = isset( $assoc_args['dry-run'] );
            $offset = isset( $assoc_args['offset'] ) ? max( 0, (int) $assoc_args['offset'] ) : 0;
            $total = count( wpconvert_wc_get_import_queue() );
            $limit = isset( $assoc_args['limit'] )
                ? max( 1, (int) $assoc_args['limit'] )
                : max( 1, $total - $offset );

            if ( $total === 0 ) {
                \WP_CLI::log( 'No WooCommerce product candidates to import.' );
                return;
            }
            if ( ! $dry_run && ! wpconvert_wc_acquire_import_lock() ) {
                \WP_CLI::error( 'Another import is in progress (user #' . wpconvert_wc_get_lock_holder() . ').' );
            }
            try {
                $result = wpconvert_wc_run_import( $offset, $limit, array( 'dry_run' => $dry_run ) );
                if ( ! $dry_run ) {
                    wpconvert_wc_record_run_progress( $result, $offset );
                }
            } finally {
                if ( ! $dry_run ) {
                    wpconvert_wc_release_import_lock();
                }
            }
            \WP_CLI::log( sprintf(
                '%s%d imported, %d skipped, %d failed (processed %d of %d)',
                $dry_run ? '[dry-run] ' : '',
                (int) $result['created_count'],
                (int) $result['skipped_count'],
                count( $result['failed'] ),
                (int) $result['processed_count'],
                (int) $result['total_count']
            ) );
            foreach ( $result['failed'] as $f ) {
                \WP_CLI::warning( $f['name'] . ': ' . $f['reason'] );
            }
            if ( empty( $result['failed'] ) ) {
                \WP_CLI::success( $dry_run ? 'Dry-run complete.' : 'Import complete.' );
            }
        }

        /**
         * Remove every WPConvert-imported product (undo the import).
         * Only deletes products still carrying the `_wpc_wc_external_id`
         * marker meta — products you created yourself are kept. Clears
         * the tracking map so a fresh `import` starts clean.
         *
         * ## OPTIONS
         *
         * [--yes]
         * : Required confirmation — this permanently deletes the products.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert-wc remove --yes
         */
        public function remove( $args, $assoc_args ) {
            if ( ! wpconvert_wc_is_active() ) {
                \WP_CLI::error( 'WooCommerce is not active. Install and activate it first.' );
            }
            $map = wpconvert_wc_get_imports();
            if ( empty( $map ) ) {
                \WP_CLI::log( 'No imported products to remove.' );
                return;
            }
            if ( ! isset( $assoc_args['yes'] ) ) {
                \WP_CLI::error( 'This permanently deletes ' . count( $map )
                    . ' imported product(s). Re-run with --yes to confirm.' );
            }
            if ( ! wpconvert_wc_acquire_import_lock() ) {
                \WP_CLI::error( 'Another import is in progress (user #' . wpconvert_wc_get_lock_holder() . ').' );
            }
            try {
                $result = wpconvert_wc_run_remove();
            } finally {
                wpconvert_wc_release_import_lock();
            }
            \WP_CLI::log( sprintf(
                '%d removed, %d skipped (already gone or not ours)',
                (int) $result['removed_count'],
                (int) $result['skipped_count']
            ) );
            \WP_CLI::success( 'Remove complete. Run `wp wpconvert-wc import` to re-import.' );
        }

        /**
         * Connect the converted /cart and /checkout pages to WooCommerce.
         * The static content is replaced with the live WC cart/checkout
         * shortcode; the original design is backed up and restored by
         * `remove`. Idempotent — already-connected pages are skipped.
         *
         * ## OPTIONS
         *
         * [--yes]
         * : Required confirmation — this replaces the pages' static content.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert-wc connect --yes
         */
        public function connect( $args, $assoc_args ) {
            if ( ! wpconvert_wc_is_active() ) {
                \WP_CLI::error( 'WooCommerce is not active. Install and activate it first.' );
            }
            $pages = wpconvert_wc_connectable_pages();
            if ( empty( $pages ) ) {
                \WP_CLI::log( 'No converted cart/checkout pages to connect (none found, or already connected).' );
                return;
            }
            if ( ! isset( $assoc_args['yes'] ) ) {
                \WP_CLI::error( 'This replaces the static content of /'
                    . implode( ' and /', array_keys( $pages ) )
                    . ' with the live WooCommerce cart/checkout. Re-run with --yes to confirm.' );
            }
            if ( ! wpconvert_wc_acquire_import_lock() ) {
                \WP_CLI::error( 'Another import is in progress (user #' . wpconvert_wc_get_lock_holder() . ').' );
            }
            try {
                $result = wpconvert_wc_run_connect_pages();
            } finally {
                wpconvert_wc_release_import_lock();
            }
            foreach ( $result['connected'] as $slug => $pid ) {
                \WP_CLI::log( '/' . $slug . ' → page #' . (int) $pid . ' now renders the live WooCommerce ' . $slug . '.' );
            }
            \WP_CLI::success( 'Connect complete. Run `wp wpconvert-wc remove --yes` to undo.' );
        }

        /**
         * Show the import tracking state: stable-key → product-ID map,
         * currency mismatch, and last-run summary.
         *
         * ## EXAMPLES
         *
         *     wp wpconvert-wc status
         */
        public function status( $args, $assoc_args ) {
            $out = array(
                'wc_active' => wpconvert_wc_is_active(),
                'imports'   => wpconvert_wc_get_imports(),
                'currency_mismatch' => wpconvert_wc_detect_currency_mismatch(),
                'last_run'  => function_exists( 'get_option' )
                    ? get_option( WPCONVERT_WC_LAST_RUN_OPTION, array() )
                    : array(),
            );
            \WP_CLI::log( json_encode( $out ) );
        }
    }
}

/* ─────────────────────────────────────────────
 * 14. HOOK WIRING
 * ───────────────────────────────────────────── */

// Ship 4c.6 / A0 — plugin lifecycle hooks. Registered unconditionally
// (guarded internally) so WordPress can find them when it triggers
// activation / deactivation. uninstall.php sits next to this file and
// calls wpconvert_cpt_run_uninstall_purge() directly.
if ( function_exists( 'register_activation_hook' ) ) {
    register_activation_hook( __FILE__, 'wpconvert_cpt_on_activation' );
}
if ( function_exists( 'register_deactivation_hook' ) ) {
    register_deactivation_hook( __FILE__, 'wpconvert_cpt_on_deactivation' );
}

// Seed the empty option on first load. Cheap (single get_option),
// idempotent (add_option no-ops if it already exists).
if ( function_exists( 'add_action' ) ) {
    add_action( 'after_setup_theme', 'wpconvert_cpt_seed_options', 10 );
    // Priority 9 so we register before default-priority `init` callbacks.
    add_action( 'init', 'wpconvert_cpt_register_active_post_types', 9 );
    // Priority 99 so flush_rewrite_rules runs AFTER our registration.
    add_action( 'init', 'wpconvert_cpt_maybe_flush_rewrite_rules', 99 );
    add_action( 'admin_notices', 'wpconvert_cpt_admin_notice_version' );
    add_action( 'admin_notices', 'wpconvert_cpt_render_admin_notice' );
    add_action( 'admin_notices', 'wpconvert_cpt_render_welcome_notice' ); // Ship 4c.6 / C8
    add_action( 'admin_notices', 'wpconvert_wc_render_admin_notice' );    // Ship 4c.6 / A3
    add_action( 'admin_menu', 'wpconvert_cpt_register_admin_page' );      // Ship 4c.6 / C1
    add_action( 'wp_ajax_wpconvert_cpt_activate_and_import', 'wpconvert_cpt_ajax_activate_and_import' );
    add_action( 'wp_ajax_wpconvert_cpt_dismiss_notice', 'wpconvert_cpt_ajax_dismiss_notice' );
    add_action( 'wp_ajax_wpconvert_cpt_delete', 'wpconvert_cpt_ajax_delete' );                       // Ship 4c.6 / C3
    add_action( 'wp_ajax_wpconvert_cpt_resync', 'wpconvert_cpt_ajax_resync' );                       // Ship 4c.6 / C4
    add_action( 'wp_ajax_wpconvert_cpt_welcome_dismiss', 'wpconvert_cpt_ajax_welcome_dismiss' );     // Ship 4c.6 / C8
    add_action( 'wp_ajax_wpconvert_cpt_check_slug', 'wpconvert_cpt_ajax_check_slug' );               // Ship 4c.7 / B1
    // Ship 4c.6 / A5+A9+A10 — WooCommerce importer endpoints.
    add_action( 'wp_ajax_wpconvert_wc_import_products', 'wpconvert_wc_ajax_import_products' );
    add_action( 'wp_ajax_wpconvert_wc_remove_products', 'wpconvert_wc_ajax_remove_products' ); // EC-CPT-012 undo
    add_action( 'wp_ajax_wpconvert_wc_connect_pages', 'wpconvert_wc_ajax_connect_pages' );     // EC-CPT-013
    // EC-CPT-013 — front-end cart wiring (all gated internally).
    add_action( 'wp_enqueue_scripts', 'wpconvert_wc_enqueue_cart_assets' );
    add_action( 'wp_head', 'wpconvert_wc_print_cart_badge_css' );
    add_action( 'wp_footer', 'wpconvert_wc_print_cart_badge_js' );
    // EC-CPT-018 — connected cart/checkout pages render the WC shortcode
    // through the bundled template (page-{slug}.php would shadow it).
    if ( function_exists( 'add_filter' ) ) {
        add_filter( 'template_include', 'wpconvert_wc_connected_page_template', 99 );
        // EC-CPT-020 — let core/WC render theme-bundled featured images via
        // the theme URI (otherwise local-image products show "could not load").
        add_filter( 'wp_get_attachment_url', 'wpconvert_cpt_filter_attachment_url', 10, 2 );
        add_filter( 'wp_get_attachment_image_src', 'wpconvert_cpt_filter_attachment_image_src', 10, 4 );
        add_filter( 'wp_get_attachment_image_attributes', 'wpconvert_cpt_filter_attachment_image_attributes', 10, 2 );
    }
    add_action( 'wpconvert_wc_regenerate_thumbnails', 'wpconvert_wc_cron_regenerate_thumbnails' );
    add_action( 'before_woocommerce_init', 'wpconvert_wc_declare_feature_compat' );
    add_action( 'cli_init', 'wpconvert_wc_register_cli_commands' );
    add_action( 'cli_init', 'wpconvert_cpt_register_cli_commands' );
    // Ship 4a — meta boxes for editing _wpc_field_* post meta in wp-admin.
    add_action( 'add_meta_boxes', 'wpconvert_cpt_register_meta_boxes' );
    add_action( 'save_post', 'wpconvert_cpt_save_meta_box', 10, 1 );
    add_action( 'admin_enqueue_scripts', 'wpconvert_cpt_enqueue_admin_assets' );
    // EC-CPT-ORDER-001 — drag-and-drop reorder on the CPT post-list screen.
    add_action( 'admin_enqueue_scripts', 'wpconvert_cpt_enqueue_reorder_assets' );
    add_action( 'wp_ajax_wpconvert_cpt_reorder', 'wpconvert_cpt_ajax_reorder' );
    add_action( 'pre_get_posts', 'wpconvert_cpt_admin_order_by_menu_order' );
}

/* ─────────────────────────────────────────────
 * EC-CPT-ORDER-001 — drag-and-drop reordering
 *
 * The front-end loop swap renders CPT posts ordered by `menu_order ASC` (then
 * ID). Out of the box there was no UI to change menu_order, so users couldn't
 * reorder rooms (and assumed ordering was "by date"). These helpers add a
 * jQuery-UI drag handle on the CPT post-list screen that persists the new
 * menu_order via AJAX. 'page-attributes' support (added in
 * wpconvert_cpt_get_supports_array) also exposes a numeric "Order" field.
 * Everything is strictly scoped to WPConvert's own active CPTs.
 * ───────────────────────────────────────────── */

/**
 * Post-type slugs of all enabled WPConvert CPTs.
 * @return string[]
 */
function wpconvert_cpt_active_post_type_slugs() {
    $slugs = array();
    if ( ! function_exists( 'wpconvert_cpt_get_active_cpts' ) ) return $slugs;
    $active = wpconvert_cpt_get_active_cpts();
    if ( ! is_array( $active ) ) return $slugs;
    foreach ( $active as $cfg ) {
        if ( is_array( $cfg ) && ! empty( $cfg['enabled'] ) && ! empty( $cfg['post_type'] ) ) {
            $pt = (string) $cfg['post_type'];
            if ( function_exists( 'wpconvert_cpt_is_valid_slug' ) && wpconvert_cpt_is_valid_slug( $pt ) ) {
                $slugs[] = $pt;
            }
        }
    }
    return array_values( array_unique( $slugs ) );
}

/**
 * Enqueue the drag-reorder UI on edit.php for our CPTs only.
 */
function wpconvert_cpt_enqueue_reorder_assets( $hook ) {
    try {
        if ( ! wpconvert_cpt_should_run() ) return;
        if ( $hook !== 'edit.php' ) return;

        $screen_pt = '';
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            if ( $screen && isset( $screen->post_type ) ) $screen_pt = (string) $screen->post_type;
        }
        if ( $screen_pt === '' && isset( $_GET['post_type'] ) ) {
            $screen_pt = is_string( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
        }
        if ( $screen_pt === '' ) return;
        if ( ! in_array( $screen_pt, wpconvert_cpt_active_post_type_slugs(), true ) ) return;

        // Drag reorder only makes sense in the default unsorted/unfiltered view.
        // A search or an explicit column sort would make drag order meaningless.
        if ( ! empty( $_GET['s'] ) ) return;
        if ( isset( $_GET['orderby'] ) && $_GET['orderby'] !== '' && $_GET['orderby'] !== 'menu_order' ) return;
        // Paged views can't be reordered safely (would clobber other pages).
        if ( isset( $_GET['paged'] ) && (int) $_GET['paged'] > 1 ) return;

        // Dedicated handle so our inline JS always prints AFTER jquery-ui-sortable
        // (piggybacking inline on core handles can race / omit the library).
        if ( function_exists( 'wp_enqueue_script' ) ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_register_script( 'wpconvert-cpt-reorder', false, array( 'jquery', 'jquery-ui-sortable' ), '1.0.1', true );
            wp_enqueue_script( 'wpconvert-cpt-reorder' );
        }
        if ( function_exists( 'wp_enqueue_style' ) ) {
            wp_enqueue_style( 'dashicons' );
        }
        $nonce = function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'wpconvert_cpt_reorder' ) : '';
        $ajax  = function_exists( 'admin_url' ) ? admin_url( 'admin-ajax.php' ) : '';
        if ( function_exists( 'wp_localize_script' ) ) {
            wp_localize_script( 'wpconvert-cpt-reorder', 'WPC_CPT_REORDER', array(
                'ajax'   => $ajax,
                'nonce'  => $nonce,
                'saving' => 'Saving order…',
                'saved'  => 'Order saved',
                'failed' => 'Could not save order',
                'hint'   => 'Drag the ≡ grip on each row to reorder. Order is saved automatically.',
                'noSortable' => 'Reorder unavailable — jQuery UI Sortable did not load. Try reloading the page.',
            ) );
        }

        $css = <<<'CSS'
.wpc-cpt-reorderable #the-list tr.wpc-cpt-drag-placeholder { outline: 2px dashed #2271b1; background: #f0f6fc !important; visibility: visible !important; }
.wpc-cpt-reorderable #the-list tr.wpc-cpt-drag-placeholder td { border-top: 2px dashed #2271b1; }
.wpc-cpt-reorder-hint { display:inline-block; margin:6px 0; padding:4px 10px; border-radius:3px; background:#f0f6fc; color:#2271b1; font-size:12px; }
.wpc-cpt-reorder-hint.is-saving { background:#fcf9e8; color:#996800; }
.wpc-cpt-reorder-hint.is-saved { background:#edfaef; color:#007017; }
.wpc-cpt-reorder-hint.is-failed { background:#fcf0f1; color:#b32d2e; }
.wpc-cpt-drag-handle { cursor: move; color: #646970; margin-right: 6px; vertical-align: text-top; }
.wpc-cpt-drag-handle:hover { color: #2271b1; }
CSS;
        if ( function_exists( 'wp_enqueue_style' ) ) {
            wp_enqueue_style( 'wp-admin' );
        }
        if ( function_exists( 'wp_add_inline_style' ) ) {
            wp_add_inline_style( 'wp-admin', $css );
        }

        $js = <<<'JS'
jQuery(function($){
  if (typeof WPC_CPT_REORDER === 'undefined') return;
  var $list = $('#the-list');
  if (!$list.length || $list.find('> tr:not(.inline-edit-row)').length < 2) return;
  $('body').addClass('wpc-cpt-reorderable');
  var $hint = $('<div class="wpc-cpt-reorder-hint"></div>').text(WPC_CPT_REORDER.hint || 'Drag rows to reorder.');
  $('.wp-list-table').first().before($hint);
  if (!$.fn.sortable) {
    $hint.addClass('is-failed').text(WPC_CPT_REORDER.noSortable || 'Reorder unavailable.');
    return;
  }
  // EC-CPT-ORDER-001b: inject a visible ≡ grip. Without a dedicated handle,
  // cancel:'a,...' blocks drag on the title link (where users naturally grab).
  $list.find('> tr:not(.inline-edit-row)').each(function(){
    var $row = $(this);
    if ($row.find('.wpc-cpt-drag-handle').length) return;
    var $title = $row.find('td.column-title strong, td.column-primary strong').first();
    if (!$title.length) $title = $row.find('td').not('.check-column').first();
    if ($title.length) {
      $title.prepend('<span class="wpc-cpt-drag-handle dashicons dashicons-menu" title="Drag to reorder" aria-hidden="true"></span>');
    }
  });
  $list.sortable({
    axis: 'y',
    cursor: 'move',
    opacity: 0.85,
    items: '> tr:not(.inline-edit-row)',
    handle: '.wpc-cpt-drag-handle',
    cancel: 'input, button, select, textarea, .row-actions',
    tolerance: 'pointer',
    forcePlaceholderSize: true,
    placeholder: 'wpc-cpt-drag-placeholder',
    helper: function(e, tr){
      var $orig = tr.children();
      var $helper = tr.clone();
      $helper.children().each(function(i){ $(this).width($orig.eq(i).width()); });
      return $helper;
    },
    start: function(e, ui){
      ui.placeholder.height(ui.item.height());
      ui.placeholder.children().each(function(i){
        $(this).width(ui.item.children().eq(i).width());
      });
    },
    update: function(){
      var ids = [];
      $list.find('> tr:not(.inline-edit-row)').each(function(){
        var m = (this.id || '').match(/post-(\d+)/);
        if (m) ids.push(m[1]);
      });
      if (!ids.length) return;
      $hint.removeClass('is-saved is-failed').addClass('is-saving').text(WPC_CPT_REORDER.saving);
      $.post(WPC_CPT_REORDER.ajax, {
        action: 'wpconvert_cpt_reorder',
        nonce: WPC_CPT_REORDER.nonce,
        order: ids
      }).done(function(res){
        if (res && res.success) {
          $hint.removeClass('is-saving is-failed').addClass('is-saved').text(WPC_CPT_REORDER.saved);
        } else {
          $hint.removeClass('is-saving is-saved').addClass('is-failed').text(WPC_CPT_REORDER.failed);
        }
      }).fail(function(){
        $hint.removeClass('is-saving is-saved').addClass('is-failed').text(WPC_CPT_REORDER.failed);
      });
    }
  });
});
JS;
        if ( function_exists( 'wp_add_inline_script' ) ) {
            wp_add_inline_script( 'wpconvert-cpt-reorder', $js );
        }
    } catch ( \Throwable $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'wpconvert_cpt_enqueue_reorder_assets: ' . $e->getMessage() );
        }
    }
}

/**
 * Persist a new menu_order from the drag UI. Only touches our own CPT posts
 * that the current user may edit.
 */
function wpconvert_cpt_ajax_reorder() {
    try {
        if ( function_exists( 'check_ajax_referer' ) ) {
            check_ajax_referer( 'wpconvert_cpt_reorder', 'nonce' );
        }
        if ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_posts' ) ) {
            if ( function_exists( 'wp_send_json_error' ) ) wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
            return;
        }
        $order = ( isset( $_POST['order'] ) && is_array( $_POST['order'] ) ) ? $_POST['order'] : array();
        if ( empty( $order ) ) {
            if ( function_exists( 'wp_send_json_error' ) ) wp_send_json_error( array( 'message' => 'empty' ), 400 );
            return;
        }
        $slugs = wpconvert_cpt_active_post_type_slugs();
        $i = 0;
        $updated = 0;
        foreach ( $order as $pid ) {
            $pid = (int) $pid;
            if ( $pid <= 0 ) continue;
            $pt = function_exists( 'get_post_type' ) ? (string) get_post_type( $pid ) : '';
            if ( ! in_array( $pt, $slugs, true ) ) continue; // never touch non-WPConvert posts
            if ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_post', $pid ) ) continue;
            if ( function_exists( 'wp_update_post' ) ) {
                wp_update_post( array( 'ID' => $pid, 'menu_order' => $i ) );
                $updated++;
            }
            $i++;
        }
        if ( function_exists( 'wp_send_json_success' ) ) wp_send_json_success( array( 'updated' => $updated ) );
    } catch ( \Throwable $e ) {
        if ( function_exists( 'wp_send_json_error' ) ) wp_send_json_error( array( 'message' => 'error' ), 500 );
    }
}

/**
 * Make the admin post-list for our CPTs default to menu_order ASC so the drag
 * order is what the user sees (and matches the front-end loop). Respects an
 * explicit user column sort.
 */
function wpconvert_cpt_admin_order_by_menu_order( $query ) {
    try {
        if ( ! function_exists( 'is_admin' ) || ! is_admin() ) return;
        if ( ! is_object( $query ) || ! method_exists( $query, 'is_main_query' ) || ! $query->is_main_query() ) return;
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            if ( ! $screen || ! isset( $screen->base ) || $screen->base !== 'edit' ) return;
        }
        $pt = $query->get( 'post_type' );
        if ( is_array( $pt ) ) $pt = reset( $pt );
        if ( ! $pt || ! in_array( (string) $pt, wpconvert_cpt_active_post_type_slugs(), true ) ) return;
        // Respect an explicit user sort (clicking a sortable column header).
        if ( $query->get( 'orderby' ) ) return;
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
    } catch ( \Throwable $e ) {
        // Never break the admin list over an ordering tweak.
    }
}

// Ship 4b — front-end loop swap. Registered outside the
// `if ( function_exists( 'add_action' ) )` block above so it can be
// independently unhooked by themes that want to take over rendering
// (e.g. a child theme with single-{slug}.php / archive-{slug}.php
// templates that owns the loop). Tier gate + active-CPTs check + try/catch
// inside the callback means the default no-CPT case is a single strpos.
if ( function_exists( 'add_filter' ) ) {
    add_filter( 'wpconvert_editor_pre_apply_overrides', 'wpconvert_cpt_expand_loop_swap', 10, 1 );
    // Ship 4c.6 / A6 — WC swap runs AFTER the CPT swap (priority 11).
    // The two never touch the same section: a WC-intent candidate is
    // excluded from CPT activation, so its section_key can't be in
    // wp_options['wpconvert_cpts'].
    add_filter( 'wpconvert_editor_pre_apply_overrides', 'wpconvert_wc_expand_loop_swap', 11, 1 );
    // EC-CPT-013 — cart-link wiring runs LAST (priority 12), after both
    // swaps; attribute-only, gated on wpconvert_wc_cart_wiring_enabled().
    add_filter( 'wpconvert_editor_pre_apply_overrides', 'wpconvert_wc_wire_cart_links', 12, 1 );
    // EC-CPT-013 — keep the header cart-count badge live on AJAX add-to-cart.
    add_filter( 'woocommerce_add_to_cart_fragments', 'wpconvert_wc_cart_count_fragment', 10, 1 );
}
