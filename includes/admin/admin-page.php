<?php

if (!class_exists('TRUNKRS_WC_AdminPage')) {
  class TRUNKRS_WC_AdminPage
  {
    const ADMIN_MENU_SLUG = 'tr-wc-settings';

    public function __construct()
    {
      add_action('admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage()
    {
      add_submenu_page(
        "woocommerce",
        __("Trunkrs", TRUNKRS_WC_Bootstrapper::DOMAIN),
        __("Trunkrs", TRUNKRS_WC_Bootstrapper::DOMAIN),
        "manage_options",
        self::ADMIN_MENU_SLUG,
        [$this, 'renderAdminPageHtml']
      );
    }

    public function renderAdminPageHtml()
    {
      global $wp_version;
      $theme = wp_get_theme();

      ?>
        <script id="__<?php esc_attr_e(self::ADMIN_MENU_SLUG) ?>__" type="application/json">
          <?php
          echo wp_json_encode([
            'isConfigured' => TRUNKRS_WC_Settings::isConfigured(),
            'isBigTextEnabled' => TRUNKRS_WC_Settings::isBigCheckoutTextEnabled(),
            'isDarkLogo' => TRUNKRS_WC_Settings::getUseDark(),
            'isEmailLinksEnabled' => TRUNKRS_WC_Settings::getUseTrackTraceLinks(),
            'isAccountTrackTraceEnabled' => TRUNKRS_WC_Settings::getUseAccountActions(),
            'isAllOrdersAreTrunkrsEnabled' => TRUNKRS_WC_Settings::getUseAllOrdersAreTrunkrsActions(),
            'isOrderRulesEnabled' => TRUNKRS_WC_Settings::isRuleEngineEnabled(),
            'isSubRenewalsEnabled' => TRUNKRS_WC_Settings::getUseSubscriptionRenewals(),
            'orderRules' => TRUNKRS_WC_Settings::getOrderRuleSet(),
            'details' => TRUNKRS_WC_Settings::getIntegrationDetails(),
            'metaBag' => [
              'php_version' => phpversion(),
              'php_extensions' => get_loaded_extensions(),
              'wp_version' => $wp_version,
              'wc_version' => WC_VERSION,
              'theme_name' => $theme->get('Name'),
              'theme_version' => $theme->get('Version'),
            ]
          ])
          ?>
        </script>

        <div id="<?php esc_attr_e(self::ADMIN_MENU_SLUG) ?>"></div>
        <div id="portal-root"></div>
      <?php
    }
  }
}

new TRUNKRS_WC_AdminPage();

