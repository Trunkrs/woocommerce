<?php

if (!class_exists('WC_TRUNKRS_AdminPage')) {
  class WC_TRUNKRS_AdminPage
  {
    const ADMIN_MENU_SLUG = 'tr-wc-settings';
    const DOMAIN = 'trunkrs-woocommerce';

    public function __construct()
    {
      add_action('admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage()
    {
      add_submenu_page(
        "woocommerce",
        __("Trunkrs", self::DOMAIN),
        __("Trunkrs", self::DOMAIN),
        "manage_options",
        self::ADMIN_MENU_SLUG,
        [$this, 'renderAdminPageHtml']
      );
    }

    public function renderAdminPageHtml()
    {
      global $wp_version;

      $theme = wp_get_theme();
      $appData = sprintf(
        '<script id="__%s__" type="application/json">%s</script>',
        self::ADMIN_MENU_SLUG,
        json_encode([
          'isConfigured' => TR_WC_Settings::isConfigured(),
          'isDarkLogo' => TR_WC_Settings::getUseDark(),
          'isEmailLinksEnabled' => TR_WC_Settings::getUseTrackTraceLinks(),
          'isAccountTrackTraceEnabled' => TR_WC_Settings::getUseAccountActions(),
          'details' => TR_WC_Settings::getIntegrationDetails(),
          'metaBag' => [
            'php_version' => phpversion(),
            'php_extensions' => get_loaded_extensions(),
            'wp_version' => $wp_version,
            'wc_version' => WC_VERSION,
            'theme_name' => $theme->get('Name'),
            'theme_version' => $theme->get('Version'),
          ]
        ])
      );

      $appMountPoint = sprintf('<div id="%s"></div>', self::ADMIN_MENU_SLUG);

      echo $appData . $appMountPoint;
    }
  }
}

new WC_TRUNKRS_AdminPage();

