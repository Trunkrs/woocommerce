<?php
if (!class_exists('TRUNKRS_WC_Notices')) {
  class TRUNKRS_WC_Notices
  {
    public function __construct()
    {
      add_action('current_screen', [$this, 'checkNotices']);
    }

    public function checkNotices()
    {
      $screen = get_current_screen();
      $isTrunkrsAdminPage = $screen->id == 'woocommerce_page_tr-wc-settings';

      $shouldShowUnconfiguredNotice = !TRUNKRS_WC_Settings::isConfigured() && !$isTrunkrsAdminPage;
      if ($shouldShowUnconfiguredNotice) {
        add_action('admin_notices', [$this, 'renderNotConfiguredNotice']);
      }
    }

    public function renderNotConfiguredNotice()
    {
      $logoUrl = TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg');

      ?>
      <div class="error trwc-error indigo">
        <span class="trwc-header">
          <img alt="Trunkrs Logo" src="<?php echo esc_url($logoUrl) ?>"/>
          <h2>Trunkrs</h2>
        </span>
        <span class="trwc-content">
          <p>
            <?php esc_html_e('Dank je wel voor het installeren van de Trunkrs voor WooCommerce plugin, de plugin is nog niet klaar voor gebruik.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            <br/>
            <?php esc_html_e('Ga naar de', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=tr-wc-settings')) ?>">
              <?php esc_html_e('instellingen pagina', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </a>
            <?php esc_html_e('om de installatie af te ronden.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
          </p>
        </span>
      </div>
      <?php
    }
  }
}

new TRUNKRS_WC_Notices();
