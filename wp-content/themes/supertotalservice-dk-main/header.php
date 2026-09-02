<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <!-- EC-PLAYGROUND-001: Head-after-wp_head — scroll-reveal + duplicated Vite CSS can keep .scroll-fade-up at opacity:0
         (IntersectionObserver never runs in static WP; Playground/theme preview also reorder stylesheets).
         This block is in the HTML after wp_head() so it wins over bundled assets for culture/Embla carousels. -->
    <style id="wpconvert-scroll-carousel-critical">
      .scroll-fade-up:has([aria-roledescription="carousel"]),
      .scroll-fade-up:has([data-wpconvert-carousel]),
      .scroll-fade-up:has([data-wpc-carousel="true"]),
      /* EC-BLOG-146: Blog category strip uses horizontal scroll + chevrons, not a carousel — IO scroll-reveal
       * still hid the bar at opacity:0 (looks like a “carousel flash”). */
      .scroll-fade-up:has([data-wpconvert-blog-filter-bar]) {
        opacity: 1 !important;
        transform: none !important;
        visibility: visible !important;
      }
      [role="region"][aria-roledescription="carousel"],
      [data-wpconvert-carousel="aria"],
      [data-wpc-carousel="true"] {
        opacity: 1 !important;
        visibility: visible !important;
      }
      /* EC-CAROUSEL-028: Carousel tracks must never wrap — generic site .flex flex-wrap:wrap
         in bundled style.css breaks multi-slide carousels into a stacked grid. */
      [data-wpconvert-flex-carousel="track"],
      [role="region"][aria-roledescription="carousel"] .overflow-hidden > div,
      .wpc-carousel-track {
        flex-wrap: nowrap !important;
      }
      /* EC-CAROUSEL-035: slide widths were computed by JS from the capture viewport and
         baked inline, so a 3-up desktop carousel stayed 3-up (squashed) on phones.
         Restore the mobile-first ladder the React component had: 1 up, 2 up, N up. */
      [data-wpconvert-flex-carousel-per-view] > [data-wpconvert-flex-carousel="track"] > * {
        width: 100% !important;
        flex-shrink: 0 !important;
      }
      @media (min-width: 640px) {
        [data-wpconvert-flex-carousel-per-view="2"] > [data-wpconvert-flex-carousel="track"] > *,
        [data-wpconvert-flex-carousel-per-view="3"] > [data-wpconvert-flex-carousel="track"] > *,
        [data-wpconvert-flex-carousel-per-view="4"] > [data-wpconvert-flex-carousel="track"] > *,
        [data-wpconvert-flex-carousel-per-view="5"] > [data-wpconvert-flex-carousel="track"] > *,
        [data-wpconvert-flex-carousel-per-view="6"] > [data-wpconvert-flex-carousel="track"] > * {
          width: 50% !important;
        }
      }
      @media (min-width: 1024px) {
        [data-wpconvert-flex-carousel-per-view="2"] > [data-wpconvert-flex-carousel="track"] > * { width: 50% !important; }
        [data-wpconvert-flex-carousel-per-view="3"] > [data-wpconvert-flex-carousel="track"] > * { width: 33.3333% !important; }
        [data-wpconvert-flex-carousel-per-view="4"] > [data-wpconvert-flex-carousel="track"] > * { width: 25% !important; }
        [data-wpconvert-flex-carousel-per-view="5"] > [data-wpconvert-flex-carousel="track"] > * { width: 20% !important; }
        [data-wpconvert-flex-carousel-per-view="6"] > [data-wpconvert-flex-carousel="track"] > * { width: 16.6667% !important; }
      }
    </style>
    <!-- EC-NAV-097: Mobile sticky bottom CTA — mobile-first: hidden by default, show only max-width 1023px.
         Desktop-only @media (min-width:1024px) hide can lose to later rules; base display:none wins on large screens. -->
    <style id="wpconvert-mobile-sticky-cta-critical">
      #wpconvert-mobile-sticky-cta {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
      }
      @media (max-width: 1023px) {
        #wpconvert-mobile-sticky-cta {
          display: block !important;
          visibility: visible !important;
          pointer-events: auto !important;
          position: fixed !important;
          left: 0 !important;
          right: 0 !important;
          bottom: 0 !important;
          z-index: 50 !important;
        }
      }
    </style>
    <!-- EC-NAV-100: --wpconvert-header-bar-height from captured header (row vs logo Tailwind classes). -->
    <style id="wpconvert-header-metrics-critical">
      :root {
        --wpconvert-header-bar-height: 4rem;
      }
    </style>
    <!-- EC-NAV-334: Re-assert the preserved off-canvas drawer's source position (absolute) within its
         own breakpoint so the generic in-flow nav-normalization rule can't force it to relative. -->
    <style id="wpconvert-native-drawer-position-restore">
      @media (max-width: 760px) {
        header nav.site-nav:not(#wpconvert-mobile-nav):not(#wpconvert-mobile-nav-backdrop) {
          position: absolute !important;
        }
      }
    </style>
    
    <!-- EC-NAV-098: Tall logo in short row + flex-wrap CTA stacking — min-height uses EC-NAV-100 variable -->
    <style id="wpconvert-header-nowrap-critical">
      header .container.flex.items-center.justify-between,
      header .container.flex {
        flex-wrap: nowrap !important;
        flex-direction: row !important;
      }
      header .container.flex.h-16,
      header .container.flex.min-h-16 {
        min-height: var(--wpconvert-header-bar-height) !important;
      }
    </style>
    <!-- EC-NAV-099e: Nav cluster wrapper uses display:contents so nav+CTA stay direct flex children of
         .container (three-way justify-between). Bundled Tailwind may purge .lg:contents — this matches bp. -->
    <style id="wpconvert-header-nav-cluster-contents-critical">
      [data-wpconvert-merge-nav-cluster] {
        display: none !important;
      }
      @media (min-width: 640px) {
        [data-wpconvert-merge-nav-cluster="sm"] {
          display: contents !important;
        }
      }
      @media (min-width: 768px) {
        [data-wpconvert-merge-nav-cluster="md"] {
          display: contents !important;
        }
      }
      @media (min-width: 1024px) {
        [data-wpconvert-merge-nav-cluster="lg"] {
          display: contents !important;
        }
      }
      @media (min-width: 1280px) {
        [data-wpconvert-merge-nav-cluster="xl"] {
          display: contents !important;
        }
      }
    </style>
    
    <!-- EC-NAV-179: Flat nav anchors must not wrap inside a min-content flex row.
         Framer NoCodeExport (and similar static exporters) give the nav container
         width:min-content and rely on RichTextContainer wrapper divs to keep each link
         on one line. The flat menu walker emits bare anchor elements so WordPress
         owns the links — which means "Chi siamo" collapses to two lines without
         this rule. Scoped to #wpconvert-flat-nav / [data-wpconvert-merge-nav-cluster]
         so Radix, Tailwind group-hover, Elementor mega, and dropdown submenus are
         untouched. -->
    <style id="wpconvert-flat-nav-nowrap-critical">
      /* EC-NAV-179b: target BOTH the menu_id span (when items_wrap preserves it)
         AND the stable .wpconvert-flat-nav-items marker class, so the rule keeps
         matching even if a future consumer overrides menu_id. Using > a picks up
         the walker-emitted bare anchors regardless of whether the span is
         display:contents or a real box. */
      #wpconvert-flat-nav > a,
      .wpconvert-flat-nav-items > a,
      [data-wpconvert-merge-nav-cluster] #wpconvert-flat-nav > a,
      [data-wpconvert-merge-nav-cluster] .wpconvert-flat-nav-items > a {
        white-space: nowrap;
        flex: 0 0 auto;
        width: auto;
      }
    </style>
    
    
    
    <script id="wpconvert-parallax-scroll">
      // EC-ANIM-012: Scroll-driven parallax translateX handler.
      // Replaces Framer Motion useScroll+useTransform for horizontal parallax text sections.
      (function() {
        function initParallax() {
          var els = document.querySelectorAll('[data-wpconvert-parallax-x]');
          if (!els.length) return;
          var entries = [];
          els.forEach(function(el) {
            var parts = el.getAttribute('data-wpconvert-parallax-x').split(',');
            entries.push({ el: el, start: parseFloat(parts[0]), end: parseFloat(parts[1]) });
          });
          var ticking = false;
          function update() {
            var vh = window.innerHeight;
            entries.forEach(function(e) {
              var parent = e.el.closest('section') || e.el.parentElement;
              var rect = parent.getBoundingClientRect();
              var progress = Math.max(0, Math.min(1, (vh - rect.top) / (vh + rect.height)));
              var x = e.start + (e.end - e.start) * progress;
              e.el.style.transform = 'translateX(' + x + '%)';
            });
            ticking = false;
          }
          window.addEventListener('scroll', function() {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
          }, { passive: true });
          update();
        }
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initParallax);
        } else {
          initParallax();
        }
      })();
      // EC-ANIM-011c: Overlap fix handled at build time by demoting z-10 on centered pulse cards.
    </script>
    
    <!-- CRITICAL: Super-aggressive popup hiding for WordPress admin previews -->
    <style id="wpconvert-popup-hide-critical">
      [data-wpconvert-popup-disabled="true"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: absolute !important;
        left: -99999px !important;
        top: -99999px !important;
        width: 0 !important;
        height: 0 !important;
        max-width: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        clip: rect(0,0,0,0) !important;
        z-index: -99999 !important;
        transform: scale(0) !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      [data-wpconvert-popup-disabled="true"] * {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
      }
      /* Block any JavaScript from showing popups */
      [data-wpconvert-popup-disabled="true"][style*="display: block"],
      [data-wpconvert-popup-disabled="true"][style*="display:block"],
      [data-wpconvert-popup-disabled="true"][class*="open"],
      [data-wpconvert-popup-disabled="true"][class*="show"],
      [data-wpconvert-popup-disabled="true"][class*="active"] {
        display: none !important;
      }
      /* Mobile navigation - HIDDEN by default, only visible when .open class is added */
      /* Uses CSS variables to match site theme (dark/light mode) */
      #wpconvert-mobile-nav {
        display: none !important; /* Hidden by default - JavaScript will show it */
        position: fixed !important; /* !important needed: framework CSS sets nav:not(.sticky):not(.fixed) to relative !important */
        top: 0 !important;
        bottom: 0 !important;
        width: 280px;
        max-width: 85vw;
        z-index: 99999 !important; /* MUST be higher than backdrop (99998) */
        /* Theme-aware colors using CSS variables with fallbacks */
        /* EC-NAV-DROPDOWN-005: emit surface colors format-aware (EC-NAV-199), same
           reasoning as the dropdown panel below. On complete-color (oklch/hex/rgb)
           themes hsl(var(--background)) is invalid-at-computed-value-time, so the
           injected mobile-nav drawer would render with a TRANSPARENT background —
           the menu appears "see-through" / hidden over page content. */
        background: hsl(var(--background, 0 0% 100%));
        color: hsl(var(--foreground, 0 0% 10%));
        overflow-y: auto;
        transition: transform 0.3s ease-in-out;
        /* Glassmorphism effect for modern sites */
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }
      /* Dark theme detection - if body/html has dark class or data attribute */
      .dark #wpconvert-mobile-nav,
      [data-theme="dark"] #wpconvert-mobile-nav,
      html.dark #wpconvert-mobile-nav {
        background: hsl(var(--background, 222 47% 11%));
        color: hsl(var(--foreground, 210 40% 98%));
      }
      
      /* Ensure menu content is clickable and ABOVE backdrop */
      /* Note: Do NOT set position on #wpconvert-mobile-nav here - it must stay fixed */
      #wpconvert-mobile-nav .nav-inner,
      #wpconvert-mobile-nav a,
      #wpconvert-mobile-nav button {
        pointer-events: auto !important;
        position: relative;
        z-index: 1;
      }
      /* Slide from RIGHT (default - for right-side hamburger buttons) */
      #wpconvert-mobile-nav.slide-right {
        right: 0;
        left: auto;
        border-left: 1px solid hsl(var(--border, 0 0% 80%) / 0.3);
        box-shadow: -4px 0 15px rgba(0, 0, 0, 0.2);
        transform: translateX(100%);
      }
      /* Slide from LEFT (for left-side hamburger buttons) */
      #wpconvert-mobile-nav.slide-left {
        left: 0;
        right: auto;
        border-right: 1px solid hsl(var(--border, 0 0% 80%) / 0.3);
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
        transform: translateX(-100%);
      }
      /* Only show mobile nav when explicitly opened via JavaScript */
      /* Higher specificity to override slide-right/slide-left transforms */
      #wpconvert-mobile-nav.open,
      #wpconvert-mobile-nav.slide-right.open,
      #wpconvert-mobile-nav.slide-left.open {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateX(0) !important;
      }
      #wpconvert-mobile-nav .nav-inner {
        padding: 1rem;
        padding-top: 3.5rem;
      }
      #wpconvert-mobile-nav .close-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        color: inherit;
      }
      #wpconvert-mobile-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0;
      }
      #wpconvert-mobile-nav li {
        margin: 0;
        border-bottom: 1px solid hsl(var(--border, 0 0% 80%) / 0.15);
      }
      #wpconvert-mobile-nav li:last-child {
        border-bottom: none;
      }
      /* EC-NAV-413: these are the drawer's LINK-LIST rules. A mirrored header CTA
         is a button, and its own captured fill/ink are plain utility classes
         (0,1,0) — they lose to these ID-scoped declarations, which would repaint
         a brand-blue pill in the drawer's body text colour and grey it out on
         tap. Excluding the CTA class keeps the button looking like the button it
         is; the class never appears on sites with no header CTAs. */
      #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta) {
        display: block;
        padding: 0.875rem 0.5rem;
        text-decoration: none;
        color: hsl(var(--foreground, 0 0% 10%));
        transition: background-color 0.2s;
      }
      #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta):hover {
        background: hsl(var(--accent, 0 0% 95%) / 0.5);
      }
      .dark #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta),
      [data-theme="dark"] #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta) {
        color: hsl(var(--foreground, 210 40% 98%));
      }
      .dark #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta):hover,
      [data-theme="dark"] #wpconvert-mobile-nav a:not(.wpconvert-drawer-cta):hover {
        background: hsl(var(--accent, 217 33% 17%) / 0.5);
      }
      /* Current menu item highlighting */
      #wpconvert-mobile-nav .current-menu-item > a,
      #wpconvert-mobile-nav .current_page_item > a {
        color: hsl(var(--primary, 217 91% 60%));
        background: hsl(var(--primary, 217 91% 60%) / 0.1);
      }
      /* EC-NAV-267: Render dropdown submenus INLINE inside the mobile overlay.
         The global desktop dropdown rules (.menu-item-has-children > ul) set the
         submenu to display:none + position:absolute + background:white and only
         reveal it on :hover/:focus-within. Inside the mobile slide-in menu that
         leaks as a floating WHITE CARD that merely flashes on touch (the touch
         :hover) while the parent <a> navigates away before children are reachable.
         Scope an ID-specificity + !important override so submenus stack inline
         (flat, always visible) like the original mobile menu — children become
         directly tappable and the white box / hover-flash disappears. */
      #wpconvert-mobile-nav li.menu-item-has-children > ul,
      #wpconvert-mobile-nav .menu-item-has-children > .sub-menu,
      #wpconvert-mobile-nav .menu-item-has-children > .submenu,
      #wpconvert-mobile-nav .sub-menu,
      #wpconvert-mobile-nav .submenu {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        top: auto !important;
        left: auto !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        min-width: 0 !important;
        max-width: none !important;
        width: auto !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      /* Indent nested items so the hierarchy reads clearly in the flat list. */
      #wpconvert-mobile-nav .sub-menu a,
      #wpconvert-mobile-nav .submenu a {
        padding-left: 1.5rem !important;
        font-size: 0.95em;
        opacity: 0.9;
      }
      body.mobile-nav-open {
        overflow: hidden;
      }
      /* Mobile nav backdrop - hidden by default, MUST be below the menu */
      #wpconvert-mobile-nav-backdrop {
        display: none !important;
        position: fixed !important; /* !important needed: framework CSS sets nav/div to relative !important */
        inset: 0 !important;
        background: rgba(0, 0, 0, 0.4);
        z-index: 99998 !important; /* Below menu (99999) but above everything else */
      }
      body.mobile-nav-open #wpconvert-mobile-nav-backdrop {
        display: block !important;
      }
      /* Universal Search Modal - works for any site with search icon */
      #wpconvert-search-modal {
        display: none;
        position: fixed !important;
        inset: 0;
        z-index: 100000;
        padding: 1rem;
        padding-top: 15vh;
      }
      #wpconvert-search-modal.open {
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      #wpconvert-search-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
      }
      #wpconvert-search-container {
        position: relative;
        width: 100%;
        max-width: 32rem;
        /* EC-NAV-DROPDOWN-005: format-aware surface — hsl(var(--background)) is invalid
           on complete-color (oklch) themes, which left the Ctrl+K search modal panel
           transparent (unreadable results over the dimmed page). */
        background: hsl(var(--background, 0 0% 100%));
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: wpconvert-search-slide-down 0.2s ease-out;
      }
      @keyframes wpconvert-search-slide-down {
        from { opacity: 0; transform: translateY(-10px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
      }
      .dark #wpconvert-search-container {
        background: hsl(var(--background, 222 47% 11%));
      }
      #wpconvert-search-header {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid hsl(var(--border, 0 0% 80%) / 0.3);
      }
      #wpconvert-search-icon {
        flex-shrink: 0;
        margin-right: 0.75rem;
        color: hsl(var(--muted-foreground, 0 0% 45%));
      }
      #wpconvert-search-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 1rem;
        color: hsl(var(--foreground, 0 0% 10%));
      }
      #wpconvert-search-input::placeholder {
        color: hsl(var(--muted-foreground, 0 0% 45%));
      }
      .dark #wpconvert-search-input {
        color: hsl(var(--foreground, 210 40% 98%));
      }
      #wpconvert-search-close {
        flex-shrink: 0;
        padding: 0.5rem;
        margin-left: 0.5rem;
        background: none;
        border: none;
        cursor: pointer;
        color: hsl(var(--muted-foreground, 0 0% 45%));
        border-radius: 0.375rem;
        transition: background-color 0.2s;
      }
      #wpconvert-search-close:hover {
        background: hsl(var(--accent, 0 0% 95%) / 0.5);
      }
      #wpconvert-search-results {
        max-height: 50vh;
        overflow-y: auto;
        padding: 0.5rem;
      }
      #wpconvert-search-results:empty::after {
        content: "Type to search...";
        display: block;
        padding: 1rem;
        text-align: center;
        color: hsl(var(--muted-foreground, 0 0% 45%));
        font-size: 0.875rem;
      }
      .wpconvert-search-result {
        display: block;
        padding: 0.75rem 1rem;
        text-decoration: none;
        color: hsl(var(--foreground, 0 0% 10%));
        border-radius: 0.375rem;
        transition: background-color 0.2s;
      }
      .wpconvert-search-result:hover {
        background: hsl(var(--accent, 0 0% 95%) / 0.5);
      }
      .wpconvert-search-result-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
      }
      .wpconvert-search-result-url {
        font-size: 0.75rem;
        color: hsl(var(--muted-foreground, 0 0% 45%));
      }
      .dark .wpconvert-search-result {
        color: hsl(var(--foreground, 210 40% 98%));
      }
      #wpconvert-search-hint {
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        color: hsl(var(--muted-foreground, 0 0% 45%));
        border-top: 1px solid hsl(var(--border, 0 0% 80%) / 0.3);
      }
      #wpconvert-search-hint kbd {
        display: inline-block;
        padding: 0.125rem 0.375rem;
        margin: 0 0.125rem;
        font-size: 0.7rem;
        font-family: inherit;
        background: hsl(var(--muted, 0 0% 95%));
        border-radius: 0.25rem;
        border: 1px solid hsl(var(--border, 0 0% 80%) / 0.5);
      }
      /* Gradient text fix - ensures background-clip works correctly */
      [style*="background"][style*="text-fill-color"] {
        -webkit-background-clip: text !important;
        background-clip: text !important;
      }
      /* CSS-only dropdown menus for converted React SPAs */
      /* Works with Tailwind's group/group-hover pattern */
      /* EC-NAV-072: Exclude .wpconvert-dropdown-panel — those have their own rules
         and transform: scaleY(1) would clobber -translate-x-1/2 centering on mega menus */
      /* EC-HERO-CAROUSEL-016: Exclude [data-wpc-slide] carousel slides. The stacked-opacity
         hero lives in a ".relative ... group" container and its hidden slides are DIRECT
         children carrying opacity-0 (and sometimes invisible). Without this exclusion,
         hovering the hero matched ".relative.group:hover > div[class*=opacity-0]" and forced
         EVERY slide to opacity:1 !important, stacking all 5 — the last slide in the DOM
         (the snapshot captured-active slide) then painted on top, so the carousel froze
         on that one frame for as long as the pointer stayed over it. The carousel runtime
         owns slide visibility, so this nav-dropdown reveal must never touch its slides. */
      .group:hover > div[class*="scale-y-0"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]),
      .group:focus-within > div[class*="scale-y-0"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]),
      .relative.group:hover > div[class*="invisible"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]),
      .relative.group:focus-within > div[class*="invisible"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]),
      .relative.group:hover > div[class*="opacity-0"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]),
      .relative.group:focus-within > div[class*="opacity-0"]:not(.wpconvert-dropdown-panel):not([data-wpc-slide]) {
        opacity: 1 !important;
        transform: scaleY(1) !important;
        visibility: visible !important;
      }
      /* Dropdown triggers */
      .group > button,
      .relative.group > button {
        cursor: pointer;
      }
      /* Keep dropdown visible while hovering on dropdown content */
      .group > div:hover:not(.wpconvert-dropdown-panel),
      .relative.group > div:hover:not(.wpconvert-dropdown-panel) {
        opacity: 1 !important;
        transform: scaleY(1) !important;
        visibility: visible !important;
      }
      /* WPConvert injected dropdown panels.
         EC-NAV-DROPDOWN-002: the outer .wpconvert-dropdown-wrapper now
         owns position + opacity + transition + the hoverable pt-2 gap
         between trigger and panel. The inner .wpconvert-dropdown-panel
         only carries visible surface styling. Hovering the parent
         .group keeps the wrapper visible, and the wrapper's bottom
         padding stays inside the hover box — so traversing the gap
         from trigger to panel never drops :hover. */
      .wpconvert-dropdown-wrapper {
        position: absolute;
        left: 0;
        top: 100%;
        padding-top: 0.5rem;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
        z-index: 50;
      }
      .group:hover > .wpconvert-dropdown-wrapper,
      .group:focus-within > .wpconvert-dropdown-wrapper,
      .wpconvert-dropdown-wrapper:hover {
        opacity: 1 !important;
        visibility: visible !important;
      }
      .wpconvert-dropdown-panel {
        min-width: 12rem;
        border-radius: 0.375rem;
        /* EC-NAV-DROPDOWN-004: emit the panel surface format-aware (EC-NAV-199).
           On Tailwind v4 / Lovable / new-shadcn projects --background / --border are
           COMPLETE colors (oklch/hex/rgb/var-chain); hsl(var(--background)) → hsl(oklch(…))
           is invalid-at-computed-value-time, so the panel background resets to transparent
           and (being later in the cascade than .bg-background) wins → see-through mega
           dropdown over the hero. cssVarColorExpr keeps hsl() for legacy HSL-triplet themes. */
        background: hsl(var(--background, 0 0% 100%));
        border: 1px solid var(--border, white);
        /* shadow-card equivalent: 1px ring + soft shadow defines edge
           against hero photos / colored backgrounds. */
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.06), 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      }
      /* EC-NAV-072: Mega menu centering is now handled by Tailwind
         utilities on the wrapper itself (left-1/2 + -translate-x-1/2).
         The defensive rule here just documents the contract for the
         inner panel class — visible styling is unchanged. */
      .wpconvert-mega-menu {
        /* outer .wpconvert-dropdown-wrapper provides centering */
      }
      /* EC-NAV-141: Flat dropdown with many links (no category grandchildren) — 2-column flow */
      .wpconvert-dropdown-panel.wpconvert-dropdown-panel--flat-mega {
        min-width: min(520px, 92vw);
        max-width: 92vw;
      }
      .wpconvert-dropdown-flat-mega-inner {
        column-count: 2;
        column-gap: 2.5rem;
      }
      .wpconvert-dropdown-flat-mega-inner > a {
        break-inside: avoid;
        -webkit-column-break-inside: avoid;
        page-break-inside: avoid;
      }
      .wpconvert-dropdown-panel a {
        display: block;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: hsl(var(--foreground, 0 0% 10%));
        text-decoration: none;
        transition: background-color 0.2s;
      }
      .wpconvert-dropdown-panel a:hover {
        background: color-mix(in oklab, var(--accent, white) 50%, transparent);
      }
      .dark .wpconvert-dropdown-panel {
        background: hsl(var(--card, 222 47% 11%));
      }
      .dark .wpconvert-dropdown-panel a {
        color: hsl(var(--foreground, 210 40% 98%));
      }
      .dark .wpconvert-dropdown-panel a:hover {
        background: color-mix(in oklab, var(--accent, white) 50%, transparent);
      }
      /* EC-NAV-DROPDOWN-001: Ensure Tailwind group-hover utilities are always available.
         Bundled CSS from JIT-compiled Tailwind may not include these if they weren't used
         in the original source — but our dropdown walker/fallback menus rely on them.
         NOTE: this whole style block is emitted inside a JavaScript template literal, so
         a single colon escape would be silently dropped during interpolation. We write
         a double backslash to preserve the literal backslash that CSS requires to
         escape the colon in compound class names like .group-hover:opacity-100. */
      .group:hover .group-hover\:opacity-100 { opacity: 1; }
      .group:hover .group-hover\:visible { visibility: visible; }
      .group:hover .group-hover\:scale-y-100 { transform: scaleY(1); }
      /* EC-NAV-DROPDOWN-006: the reveal fallbacks above are only half the
         contract — the HIDE side (.opacity-0 / .invisible) is equally
         bundle-dependent. Ship the base utilities too so a panel that opted
         into .invisible is actually hidden. */
      .invisible { visibility: hidden; }
      .visible { visibility: visible; }
      /* WPC Mobile Menu Panel States (for preserved mobile menus) */
      [data-wpc-menu-panel] {
        /* Original styling preserved via classes */
      }
      [data-wpc-menu-panel].is-open {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: translateX(0) !important;
      }
      [data-wpc-menu-overlay] {
        display: none;
        position: fixed !important;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99998;
      }
      [data-wpc-menu-overlay].is-open {
        display: block;
      }
      body.wpc-menu-open {
        overflow: hidden;
      }
      .wpc-mobile-menu {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .wpc-mobile-menu li {
        margin: 0;
      }
      .wpc-mobile-menu a {
        display: block;
        padding: 0.75rem 1rem;
        text-decoration: none;
      }
    </style>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    
    <!-- Universal Search Modal -->
    <div id="wpconvert-search-modal" role="dialog" aria-modal="true" aria-label="Search">
        <div id="wpconvert-search-backdrop"></div>
        <div id="wpconvert-search-container">
            <div id="wpconvert-search-header">
                <svg id="wpconvert-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                <input type="text" id="wpconvert-search-input" placeholder="Search pages..." autocomplete="off" />
                <button id="wpconvert-search-close" type="button" aria-label="Close search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line></svg>
                </button>
            </div>
            <div id="wpconvert-search-results"></div>
            <div id="wpconvert-search-hint">Press <kbd>Esc</kbd> to close &bull; <kbd>Ctrl</kbd>+<kbd>K</kbd> to open</div>
        </div>
    </div>
    
    <!-- Original mobile menu preserved with wp_nav_menu() integration -->
    
    
    
    <header class="site-header">
      <div class="container">
        <a class="brand" href="<?php echo home_url('/'); ?>" aria-label="STS ApS – Gå til forsiden">
          <?php 
            $wpc_logo_html = '';
            // EC-LOGO-006: Prefer the captured theme brand SVG/PNG. Playground and
            // fresh WP installs often set a generic custom_logo that overrides the
            // stylized wordmark with a blank/plain attachment.
            $wpc_theme_logo = get_template_directory() . '/assets/images/logo-sts-rgb.png';
            if (file_exists($wpc_theme_logo)) {
                $wpc_logo_html = '<img src="' . get_template_directory_uri() . '/assets/images/logo-sts-rgb.png" alt="STS ApS logo" class="custom-logo" style="max-height: 48px; width: auto; height: auto;" data-wpc-id="wpc_21e169e71a" data-wpc-editable="image" />';
            }
            if (!$wpc_logo_html && has_custom_logo()) {
                $custom_logo_id = get_theme_mod('custom_logo');
                // EC-LOGO-005c: require a real attachment with a resolvable src — not
                // just a truthy theme_mod (broken imports echo empty or 404 <img>).
                if ($custom_logo_id && wp_attachment_is_image($custom_logo_id)) {
                    $wpc_logo_src = wp_get_attachment_image_src($custom_logo_id, 'full');
                    if (is_array($wpc_logo_src) && !empty($wpc_logo_src[0])) {
                        $wpc_logo_html = wp_get_attachment_image($custom_logo_id, 'full', false, array(
                            'class' => 'custom-logo max-h-12 w-auto',
                            'alt'   => esc_attr(get_bloginfo('name')),
                            'style' => 'max-height: 48px; width: auto; height: auto;',
                            'data-wpc-id' => 'wpc_21e169e71a',
                            'data-wpc-editable' => 'image'
                        ));
                    }
                }
            }
            if ($wpc_logo_html) {
                echo $wpc_logo_html;
            } else {
                echo '<img src="' . get_template_directory_uri() . '/assets/images/logo-sts-rgb.png" alt="STS ApS logo" class="custom-logo" style="max-height: 48px; width: auto; height: auto;" data-wpc-id="wpc_21e169e71a" data-wpc-editable="image" />';
            }
        ?>
        </a>
        <button class="nav-toggle" type="button" aria-label="Åbn menu" aria-expanded="false" data-state="closed">☰</button>
        <nav class="site-nav" aria-label="Primær navigation"><?php
      // Flat nav menu - converted from React/Vite site
      $menu_data = json_decode(@file_get_contents(get_template_directory() . '/assets/data/menus.json'), true);
      $nav_classes = !empty($menu_data['primary']['navContainerClasses']) ? $menu_data['primary']['navContainerClasses'] : 'site-nav';
      
      if (has_nav_menu('primary') && class_exists('WPConvert_Flat_Menu_Walker')) {
          wp_nav_menu([
              'theme_location' => 'primary',
              'container'      => false,
              'menu_id'        => 'wpconvert-flat-nav',
              'menu_class'     => esc_attr($nav_classes),
              // EC-NAV-179b: items_wrap used to be '%3$s' — which stripped the <ul>
              // wrapper and silently dropped the menu_id from the rendered DOM.
              // Without the id, the EC-NAV-179 nowrap critical-CSS (#wpconvert-flat-nav > a)
              // matched nothing. We now emit a transparent wrapper (<span
              // style="display:contents">) that (a) preserves the menu_id in the DOM,
              // (b) does not participate in its own layout box (children become flex
              // children of the parent <nav>), and (c) does not introduce an invalid
              // <ul><a></ul> semantic (WP walker emits bare <a>, not <li>).
              'items_wrap'     => '<span id="%1$s" class="wpconvert-flat-nav-items" style="display:contents">%3$s</span>',
              'fallback_cb'    => 'wpconvert_fallback_menu_flat',
              'depth'          => 2,
              'walker'         => new WPConvert_Flat_Menu_Walker()
          ]);
      } else {
          wpconvert_fallback_menu_flat();
      }
      ?></nav>
      </div>
    </header>
    
    <main id="main-content" class="site-main">