    </main>
    
    
    <footer class="site-footer" aria-label="Sidefod">
  <div class="container footer-grid">
    <div>
      <h3 data-wpc-id="wpc_90e3906884" data-wpc-editable="heading">STS ApS</h3>
      <p data-wpc-id="wpc_f9a8b4222e" data-wpc-editable="text">Professionel service til erhverv, ejendomme og byggeprojekter i hele Danmark.</p>
      <div class="badges">
        <span class="badge">ISO 9001</span>
        <span class="badge">INSTA 800</span>
        <span class="badge">Autoriseret</span>
      </div>
    </div>
    <div>
      <h3 data-wpc-id="wpc_b757531d27" data-wpc-editable="heading">Kontakt</h3>
      <ul>
        <li>📞 <a href="tel:+4536302525" data-wpc-id="wpc_8607fa328d" data-wpc-editable="link">+45 36 30 25 25</a></li>
        <li>✉️ <a href="mailto:mail@st-service.dk" data-wpc-id="wpc_176e90f3d2" data-wpc-editable="link">mail@st-service.dk</a></li>
        <li>📍 Krondalvej 8, 2610 Rødovre</li>
      </ul>
    </div>
    <div>
      <h3 data-wpc-id="wpc_7f94eb9cf7" data-wpc-editable="heading">Genveje</h3>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/service/index/')); ?>" data-wpc-id="wpc_e5202b3c25" data-wpc-editable="link">Alle ydelser</a></li>
        <li><a href="<?php echo esc_url(home_url('/hvem-er-sts/index/')); ?>" data-wpc-id="wpc_ed804c37ae" data-wpc-editable="link">Om STS ApS</a></li>
        <li><a href="<?php echo esc_url(home_url('/kontakt/index/')); ?>" data-wpc-id="wpc_ee492be61b" data-wpc-editable="link">Kontakt os</a></li>
        <li><a href="<?php echo esc_url(home_url('/handelsbetingelser/index/')); ?>" data-wpc-id="wpc_618d90d57d" data-wpc-editable="link">Handelsbetingelser</a></li>
      </ul>
    </div>
    <div>
      <h3 data-wpc-id="wpc_3fea6f788f" data-wpc-editable="heading">Åbningstider</h3>
      <ul data-wpc-id="wpc_101cebc934" data-wpc-editable="list">
        <li>Man – Fre: 07:00–16:00</li>
        <li>Weekend: Efter aftale</li>
        <li>24/7 beredskab til erhverv</li>
      </ul>
    </div>
  </div>
  <div class="container footer-copy">
    <div class="footer-theme-controls">
      <button type="button" class="theme-subtle-toggle" data-theme-toggle="" aria-label="Skift tema" title="Skift tema" data-wpc-id="wpc_4f6397fcd7" data-wpc-editable="button">Tema: Auto</button>
    </div>
    <p data-wpc-id="wpc_f63a94fad2" data-wpc-editable="text">© 2025 STS ApS – CVR: 32 27 03 95</p>
  </div>
</footer>
    
    <!-- Sticky/Fixed CTA Elements -->
    <aside id="floating-contact-rail" class="floating-contact-rail" aria-label="Hurtig kontakt"><a class="floating-contact-btn" href="tel:+4536302525" aria-label="Ring til STS ApS"><span class="floating-contact-icon" aria-hidden="true">📞</span><span class="floating-contact-text">36 30 25 25</span></a><a class="floating-contact-btn" href="mailto:mail@st-service.dk" aria-label="Send mail til STS ApS"><span class="floating-contact-icon" aria-hidden="true">✉</span><span class="floating-contact-text">Send mail</span></a></aside>

    
    <?php wp_footer(); ?>
    <?php
    $wpc_hc_path = get_template_directory() . '/assets/data/wpconvert-hero-chrome.json';
    if ( is_readable( $wpc_hc_path ) ) {
      $wpc_hc = json_decode( (string) file_get_contents( $wpc_hc_path ), true );
      if ( is_array( $wpc_hc ) && ! empty( $wpc_hc['hex'] ) && preg_match( '/^#[0-9a-fA-F]{3,8}$/', (string) $wpc_hc['hex'] ) ) {
        $wpc_hex = esc_attr( $wpc_hc['hex'] );
        echo '<style id="wpconvert-hero-chrome-after-footer">html,body,.site-main,#primary.content-area,#primary.content-area>section:first-of-type,.site-main #primary.content-area>section:first-of-type,.site-main>div:first-of-type>section:first-of-type,body.home #primary.content-area>section:first-of-type,body.page-template-default.home #primary.content-area>section:first-of-type{background-color:' . $wpc_hex . ' !important;}</style>';
      }
    }
    ?>
</body>
</html>