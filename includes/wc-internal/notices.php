<?php
if (!class_exists('WC_TRUNKRS_Notices')) {
  class WC_TRUNKRS_Notices {
    public function __construct() {
      add_action('current_screen', [$this, 'checkNotices']);
    }

    public function checkNotices() {
      $screen = get_current_screen();
      $isTrunkrsAdminPage = $screen->id == 'woocommerce_page_tr-wc-settings';

      $shouldShowUnconfiguredNotice = !TR_WC_Settings::isConfigured() && !$isTrunkrsAdminPage;
      if ($shouldShowUnconfiguredNotice) {
        add_action('admin_notices', [$this, 'renderNotConfiguredNotice']);
      }
    }

    public function renderNotConfiguredNotice() {
      echo sprintf(
        '<div class="error trwc-error indigo">
        <span class="trwc-header">
          <img alt="Trunkrs Logo" src="%s"/>
          <h2>Trunkrs</h2>
        </span>
        <span class="trwc-content">
          <p>Dank je wel voor het installeren van de Trunkrs voor WooCommerce plugin, de plugin is nog niet klaar voor gebruik.<br/>Ga naar de <a href="%s">instellingen pagina</a> om de installatie af te ronden.</p>
        </span>
      </div>',
        WC_TRUNKRS_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg'),
        admin_url('admin.php?page=tr-wc-settings')
      );
    }
  }
}

new WC_TRUNKRS_Notices();
