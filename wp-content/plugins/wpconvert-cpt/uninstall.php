<?php
/**
 * WPConvert CPT uninstall handler (Ship 4c.6 / A0).
 *
 * WordPress invokes this file when the user clicks "Delete" on the
 * plugin in the Plugins screen. By the time we run, the plugin's other
 * PHP files are NOT loaded, so we can't reuse any plugin functions —
 * we have to do the work inline.
 *
 * Default behavior: PRESERVE all user data (CPT registration options,
 * imported posts, post meta). The user might have reinstalled by
 * accident; data loss on uninstall would be a user-hostile surprise.
 *
 * Opt-in purge: the user sets wp_options['wpconvert_cpts_purge_on_uninstall']
 * to 1 (via the Tools → WPConvert CPTs Diagnostics tab) BEFORE
 * uninstalling. Then this file deletes the plugin's own options +
 * transients. Even on purge we do NOT trash CPT posts — those are
 * user content; deleting them silently is unacceptable.
 *
 * Safety: WordPress sets WP_UNINSTALL_PLUGIN constant when calling this
 * file. We exit early if it isn't set (defense against direct access).
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$purge = (int) get_option( 'wpconvert_cpts_purge_on_uninstall', 0 );
if ( ! $purge ) {
    // Default safe path: leave everything intact. Reinstalling the
    // plugin picks up where the user left off.
    return;
}

// Capture the manifest cache transient key BEFORE we delete the option
// that holds it.
$known_transient_key = (string) get_option( 'wpconvert_cpts_manifest_cache_key', '' );

$option_keys = array(
    'wpconvert_cpts',
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

if ( $known_transient_key !== '' ) {
    delete_transient( $known_transient_key );
}
