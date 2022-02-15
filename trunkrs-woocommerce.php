<?php

/**
 * Plugin Name: Trunkrs for WooCommerce
 * Description: Add excellent consumer focused shipping to your WooCommerce store.
 * Author: Trunkrs
 * Author URI: https://trunkrs.nl
 * Version: 1.2.5
 * Requires at least: 3.6 & WooCommerce 3.0+
 * Requires PHP: 7.1
 * License: GPLv3
 * Text Domain: trunkrs-for-woocommerce
 *
 * @wordpress-plugin
 */

if (!class_exists('TRUNKRS_WC_Bootstrapper')) {
  class TRUNKRS_WC_Bootstrapper
  {
    private const MIN_PHP_VERSION = '7.0';
    public const DOMAIN = 'trunkrs-for-woocommerce';

    /**
     * @var string The semver version of the plugin.
     */
    public $version = '1.2.5';

    /**
     * @var TRUNKRS_WC_Bootstrapper The shared plugin instance.
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
     * @return TRUNKRS_WC_Bootstrapper
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


      add_action('plugins_loaded', [$this, 'loadTranslations']);
      add_action('init', [$this, 'loadMain']);
      register_activation_hook(__FILE__, [$this, 'loadTables']);
    }

    public function notifyWooCommerce()
    {
     ?>
      <div class="error trwc-error">
        <div class="trwc-header">
          <img alt="Trunkrs Logo" src="<?php echo esc_url(TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg')) ?>" />
          <h2>Trunkrs</h2>
        </div>
        <span class="trwc-content">
          <p>
            <?php esc_html_e("Voor het gebruik van Trunkrs voor WooCommerce moet je", self::DOMAIN) ?>
            <a href="http://wordpress.org/extend/plugins/woocommerce/"> WooCommerce </a>
            <?php esc_html_e("geinstalleerd hebben!", self::DOMAIN) ?>
          </p>
        </span>
      </div>
      <?php
    }

    public function notifyPHPVersion()
    {
      ?>
      <div class="error trwc-error">
        <span class="trwc-header">
          <img alt="Trunkrs Logo" src="<?php echo esc_url(TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg')) ?>"/>
          <h2>Trunkrs</h2>
        </span>
        <span class="trwc-content">
          <p>
            <?php
            esc_html(sprintf(
              __("Trunkrs voor WooCommerce heeft PHP %s of hoger nodig.", self::DOMAIN),
              self::MIN_PHP_VERSION
            ))
            ?>
          </p>
          <p>
            <a href="http://docs.wpovernight.com/general/how-to-update-your-php-version">
              <?php esc_html_e("Hoe kan ik mijn PHP versie updaten.", self::DOMAIN) ?>
            </a>
          </p>
        </span>
      </div>
      <?php
    }

    public function loadMain()
    {
      // Base resources
      require_once($this->pluginPath . '/includes/utils.php');
      require_once($this->pluginPath . '/includes/asset-loader.php');

      // Load the translations
      $this->loadTranslations();

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
      $includePath = $this->pluginPath . '/includes';
      require_once($includePath . '/index.php');
    }

    public function loadTranslations() {
      $locale = get_locale();
      $wpLangDir = trailingslashit(WP_LANG_DIR);
      $locale = explode('_', $locale)[0];

      $wpMainPluginFile = $wpLangDir . 'trunkrs-for-woocommerce/' . TRUNKRS_WC_Bootstrapper::DOMAIN . '-' . $locale . '.mo';
      load_textdomain(TRUNKRS_WC_Bootstrapper::DOMAIN, $wpMainPluginFile);

      $wpTextDomainFile = $wpLangDir . 'plugins/' . TRUNKRS_WC_Bootstrapper::DOMAIN . '-' . $locale . '.mo';
      load_textdomain(TRUNKRS_WC_Bootstrapper::DOMAIN, $wpTextDomainFile);

      $pluginTextDomainFile = dirname(plugin_basename(__FILE__)) . '/languages';
      load_plugin_textdomain(TRUNKRS_WC_Bootstrapper::DOMAIN, false, $pluginTextDomainFile);
    }

    public function loadTables() {
      require_once($this->pluginPath . '/includes/init-db.php');
      TRUNKRS_WC_InitDB::init();
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

function TRUNKRS_WC_Bootstrapper()
{
  return TRUNKRS_WC_Bootstrapper::instance();
}

function WooCommerce_Trunkrs()
{
  return TRUNKRS_WC_Bootstrapper();
}

TRUNKRS_WC_Bootstrapper();
