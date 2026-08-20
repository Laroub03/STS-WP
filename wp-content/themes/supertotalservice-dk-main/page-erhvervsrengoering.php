<?php
/*
Template Name: STS ApS | Erhvervsrengoering
*/
get_header(); ?>

<div class="page-erhvervsrengoering">
    <div class="page-content">
        <div class="section-hero"><section class="page-hero" id="hero" data-wpc-id="wpc_48761853c0" data-wpc-editable="section">
        <div class="container">
          <span class="eyebrow" data-wpc-id="wpc_6c4b63bab8" data-wpc-editable="text">Erhvervsrengoering</span>
          <h1 data-wpc-id="wpc_b541756527" data-wpc-editable="heading">Erhvervsrengoering</h1>
          <p data-wpc-id="wpc_09c519647d" data-wpc-editable="text">Kontakt os for at høre mere om vores service og få et konkret tilbud.</p>
        </div>
      </section></div>
<div class="grid-layout-container section-content"><section class="section" id="content" data-wpc-id="wpc_1afdb023ca" data-wpc-editable="section">
        <div class="container content-grid">
          <div class="info-card">
            <span class="eyebrow" data-wpc-id="wpc_ac6714103f" data-wpc-editable="text">Vores tilgang</span>
            <h2 data-wpc-id="wpc_5b13943aea" data-wpc-editable="heading">STS serviceydelser</h2>
            <p data-wpc-id="wpc_2bd40b3d13" data-wpc-editable="text">Vi arbejder professionelt og leverer løsninger, der er nemme at samarbejde med.</p>
            <ul class="list-check" data-wpc-id="wpc_58f2cd95dd" data-wpc-editable="list">
              <li>Hurtig respons</li><li>Fleksibel service</li><li>Pålidelig levering</li>
            </ul>
          </div>
          <div class="hero-card">
            <img class="service-detail-main-image" src="<?php echo get_template_directory_uri(); ?>/assets/images/rengoering.jpg" alt="Erhvervsrengøring" data-wpc-id="wpc_0dafa19ee5" data-wpc-editable="image">
          </div>
        </div>
      </section></div>
<div class="section-content"><section class="section" id="content" data-wpc-id="wpc_c38043b144" data-wpc-editable="section">
        <div class="container">
          <div class="quote-band">
            <p data-wpc-id="wpc_7e17ba8dd3" data-wpc-editable="text">Vi sætter standarden for ansvarlig, professionel og praktisk service i hvert projekt.</p>
            <strong data-wpc-id="wpc_a1fb5a31cf" data-wpc-editable="text">Et stærkt samarbejde med kvalitet i fokus</strong>
          </div>
        </div>
      </section></div>
<div class="section-cta"><section class="section" id="cta" data-wpc-id="wpc_7a7f000d96" data-wpc-editable="section">
        <div class="container form-card">
          <div>
            <span class="eyebrow" data-wpc-id="wpc_283cc79aa1" data-wpc-editable="text">Kontakt os</span>
            <h2 data-wpc-id="wpc_33b1400936" data-wpc-editable="heading">Få et hurtigt og uforpligtende tilbud.</h2>
            <p data-wpc-id="wpc_0b217cc7e0" data-wpc-editable="text">Fortæl os om din opgave, så vender vi hurtigt tilbage med et konkret svar.</p>
          </div>
          <form data-wpconvert-form="needs-wiring" data-wpconvert-form-id="/erhvervsrengoering-form-0" action="" method="post">
            <label for="name" data-wpc-id="wpc_2fb26177b2" data-wpc-editable="text">Navn</label>
            <input id="name" name="customer_name" type="text" placeholder="Dit navn">
            <label for="email" data-wpc-id="wpc_ed6f4d1a7e" data-wpc-editable="text">E-mail</label>
            <input id="email" name="email" type="email" placeholder="din@mail.dk">
            <label for="phone" data-wpc-id="wpc_87abfe2553" data-wpc-editable="text">Telefon</label>
            <input id="phone" name="phone" type="tel" placeholder="+45 12 34 56 78">
            <label for="message" data-wpc-id="wpc_7b350247d0" data-wpc-editable="text">Beskrivelse</label>
            <textarea id="message" name="message" rows="5" placeholder="Skriv kort om din opgave..."></textarea>
            <button class="btn btn-primary" type="submit" data-wpc-id="wpc_d156043dbd" data-wpc-editable="button">Send forespørgsel</button>
          </form>
        </div>
      </section></div>
    </div>
</div>
<script data-wpc-unwired-form-safetynet="1">
(function(){
  if (typeof document === "undefined") return;
  // EC-FORM-003: capture-phase submit interceptor for unwired forms.
  // Runs BEFORE the wpconvert-forms plugin's bubble-phase handler so
  // pages without the plugin do not reload + flicker their iframes.
  document.addEventListener("submit", function(ev){
    var form = ev.target;
    if (!form || typeof form.getAttribute !== "function") return;
    var marker = form.getAttribute("data-wpconvert-form");
    if (marker !== "needs-wiring") return;
    ev.preventDefault();
    form.setAttribute("data-wpc-form-blocked", "1");
    try {
      if (window.console && console.warn) {
        console.warn("[WPConvert] Form submit blocked: install the wpconvert-forms plugin to wire this form. (form id: " + (form.id || form.getAttribute("data-wpconvert-form-id") || "<unknown>") + ")");
      }
    } catch (_) {}
  }, true);
})();
</script>
<?php get_footer(); ?>
