<?php

/**
 * Plugin Name: Trunkrs for WooCommerce
 * Description: Add excellent consumer focused shipping to your WooCommerce store.
 * Author: Trunkrs
 * Author URI: https://trunkrs.nl
 * Version: 1.0.0
 * Requires at least: 3.5.1 & WooCommerce 3.0+
 * Requires PHP: 7.1
 * License: GPLv3
 *
 * @wordpress-plugin
 */

if (!class_exists('WC_TRUNKRS_Bootstrapper')) {
  class WC_TRUNKRS_Bootstrapper
  {
    const MIN_PHP_VERSION = '7.0';
    const DOMAIN = 'trunkrs-woocommerce';

    /**
     * @var string The semver version of the plugin.
     */
    public $version = '0.0.1';

    /**
     * @var WC_TRUNKRS_Bootstrapper The shared plugin instance.
     */
    private static $_instance;

    /**
     * @var string The plugin base name
     */
    public $pluginBasename;

    /**
     * @var string The plugin path
     */
    public $pluginPath;

    /**
     * @var string The plugin URL
     */
    public $pluginUrl;

    /**
     * @return WC_TRUNKRS_Bootstrapper
     */
    public static function instance()
    {
      if (is_null(self::$_instance)) {
        self::$_instance = new self();
      }

      return self::$_instance;
    }

    public function __construct()
    {
      $this->define('WC_TRUNKRS_VERSION', $this->version);

      $this->pluginBasename = plugin_basename(__FILE__);
      $this->pluginPath = rtrim(plugin_dir_path(__FILE__), '/\\');
      $this->pluginUrl = rtrim(plugins_url('/', __FILE__), '/\\');

      add_action('init', [$this, 'loadMain']);
    }

    public function notifyWooCommerce()
    {
      $error = sprintf(
        __("Trunkrs Shipping for WooCommerce requires you to have %sWooCommerce%s installed & configured!",
          self::DOMAIN
        ),
        '<a href="http://wordpress.org/extend/plugins/woocommerce/">',
        '</a>'
      );

      $message = sprintf(
        '<div class="error trwc-error">
                    <div class="trwc-header">
                        <img alt="Trunkrs Logo" src="%s" />
                        <h2>%s</h2>
                    </div>
                    <span class="trwc-content">
                        <p>%s</p>
                    </span>
               </div>',
        WC_TRUNKRS_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg'),
        __('Trunkrs', self::DOMAIN),
        $error
      );

      echo $message;
    }

    public function notifyPHPVersion()
    {
      $error = sprintf(
        __("Trunkrs shipping for WooCommerce requires PHP %s or higher.", self::DOMAIN),
        self::MIN_PHP_VERSION
      );
      $message = sprintf(
        '<div class="error trwc-error">
                  <span class="trwc-header">
                    <img alt="Trunkrs Logo" src="%s" />
                    <h2>%s</h2>
                  </span>
                  <span class="trwc-content">
                    <p>%s</p>
                    <p><a href="%s">%s</a></p>
                  </span>
                </div>',
        WC_TRUNKRS_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg'),
        __('Trunkrs', self::DOMAIN),
        $error,
        'http://docs.wpovernight.com/general/how-to-update-your-php-version/',
        __("How to update your PHP version", self::DOMAIN)
      );

      echo $message;
    }

    public function loadMain()
    {
      // Base resources
      require_once($this->pluginPath . '/includes/utils.php');
      require_once($this->pluginPath . '/includes/asset-loader.php');

      if (!$this->isPHPVersionMet(self::MIN_PHP_VERSION)) {
        add_action('admin_notices', [$this, 'notifyPHPVersion']);
        return;
      }

      if (!$this->isWooCommerceActive()) {
        add_action('admin_notices', [$this, 'notifyWooCommerce']);
        return;
      }

      // Load plugin
      $this->loadClasses();
    }

    private function define($name, $value)
    {
      if (!defined($name)) {
        define($name, $value);
      }
    }

    private function loadClasses()
    {
      // Autoload the vendor packages
      require_once($this->pluginPath . '/vendor/autoload.php');

      // Load internal classes
      $includePath = $this->pluginPath . '/includes';

      require_once($includePath . '/settings.php');
      require_once($includePath . '/api.php');

      require_once($includePath . '/wc-internal/trunkrs-order.php');
      require_once($includePath . '/wc-internal/orders.php');
      require_once($includePath . '/wc-internal/shipping-method.php');
      require_once($includePath . '/wc-internal/notices.php');

      require_once($includePath . '/admin/admin-page.php');
      require_once($includePath . '/admin/admin-order-page.php');
      require_once($includePath . '/admin/admin-endpoints.php');
    }

    private function isWooCommerceActive()
    {
      $blog_plugins = get_option('active_plugins', []);
      $site_plugins = get_site_option('active_sitewide_plugins', []);

      return in_array('woocommerce/woocommerce.php', $blog_plugins)
        || isset($site_plugins['woocommerce/woocommerce.php']);
    }

    private function isPHPVersionMet($version)
    {
      return version_compare(PHP_VERSION, $version, '>=');
    }
  }
}

function WC_TRUNKRS_Bootstrapper()
{
  return WC_TRUNKRS_Bootstrapper::instance();
}

function WooCommerce_Trunkrs()
{
  return WC_TRUNKRS_Bootstrapper();
}

WC_TRUNKRS_Bootstrapper();
