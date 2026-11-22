<?php

if (!class_exists('TRUNKRS_WC_AdminEndpoints')) {
  class TRUNKRS_WC_AdminEndpoints
  {
    const DOWNLOAD_LABEL_ACTION = 'tr-wc_download-label';
    const CANCEL_ACTION = 'tr-wc_cancel';
    const RE_ANNOUNCE_ACTION = 'tr-wc_reannounce';
    const GET_ORDER_LOGS = 'tr-wc_get-order-logs';

    const REGISTER_ACTION = 'tr-wc_register-plugin';

    const UPDATE_USE_DARK_ACTION = 'tr-wc_update-use-dark';
    const UPDATE_USE_TNT_LINKS_ACTION = 'tr-wc_update-use-tnt-links';
    const UPDATE_USE_TNT_ACCOUNT_ACTION = 'tr-wc_update-use-tnt-account';
    const UPDATE_USE_ALL_ORDERS_ARE_TRUNKRS = 'tr-wc_update-use-all-orders-are-trunkrs';
    const UPDATE_USE_BIG_CHECKOUT_TEXT = 'tr-wc_update-use-big-checkout-text';
    const UPDATE_USE_ORDER_RULES = 'tr-wc_update-use-order-rules';
    const UPDATE_ORDER_RULES = 'tr-wc_update-order-rules';
    const UPDATE_USE_SUB_RENEWALS = 'tr-wc_update-use-sub-renewals';

    public function __construct()
    {
      add_action('wp_ajax_' . self::REGISTER_ACTION, [$this, 'executeRegisterEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_DARK_ACTION, [$this, 'executeUpdateUseDarkEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_TNT_LINKS_ACTION, [$this, 'executeUpdateUseTnTLinksEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_TNT_ACCOUNT_ACTION, [$this, 'executeUpdateUseTnTAccountEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_ALL_ORDERS_ARE_TRUNKRS, [$this, 'executeUpdateUseAllOrdersAreTrunkrsEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_BIG_CHECKOUT_TEXT, [$this, 'executeUpdateUseBigCheckoutText']);
      add_action('wp_ajax_' . self::UPDATE_USE_ORDER_RULES, [$this, 'executeUpdateUseOrderRules']);
      add_action('wp_ajax_' . self::UPDATE_ORDER_RULES, [$this, 'executeUpdateOrderRuleSet']);
      add_action('wp_ajax_' . self::UPDATE_USE_SUB_RENEWALS, [$this, 'executeUpdateUseSubRenewals']);

      add_action('wp_ajax_' . self::DOWNLOAD_LABEL_ACTION, [$this, 'executeDownloadLabelEndpoint']);
      add_action('wp_ajax_' . self::CANCEL_ACTION, [$this, 'executeCancelEndpoint']);
      add_action('wp_ajax_' . self::RE_ANNOUNCE_ACTION, [$this, 'executeAnnounceEndpoint']);
      add_action('wp_ajax_' . self::GET_ORDER_LOGS, [$this, 'executeFindAuditLogEntries']);
    }

    public function executeRegisterEndpoint()
    {
      $this->authorize('manage_woocommerce');

      if (TRUNKRS_WC_Settings::isConfigured()) {
        status_header(409);
        wp_die();
        return;
      }

      $accessToken = sanitize_text_field($_POST['accessToken']);
      $integrationId = sanitize_text_field($_POST['integrationId']);
      $orgId = sanitize_text_field($_POST['organizationId']);
      $orgName = sanitize_text_field($_POST['organizationName']);

      TRUNKRS_WC_Settings::setConfigured(true);
      TRUNKRS_WC_Settings::setAccessToken($accessToken);
      TRUNKRS_WC_Settings::setIntegrationDetails([
        'integrationId' => $integrationId,
        'organizationId' => $orgId,
        'organizationName' => $orgName,
      ]);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseDarkEndpoint()
    {
      $this->authorize('manage_woocommerce');

      $useDark = sanitize_text_field($_POST['isDarkLogo']) === 'true';

      TRUNKRS_WC_Settings::setUseDark($useDark);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseTnTLinksEndpoint()
    {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isEmailLinksEnabled']) === 'true';

      TRUNKRS_WC_Settings::setUseEmailLink($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseTnTAccountEndpoint()
    {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isAccountTrackTraceEnabled']) === 'true';

      TRUNKRS_WC_Settings::setUseAccountActions($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseAllOrdersAreTrunkrsEndpoint()
    {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isAllOrdersAreTrunkrsEnabled']) === 'true';

      TRUNKRS_WC_Settings::setUseAllOrdersAreTrunkrs($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseBigCheckoutText() {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isUseBigTextEnabled']) === 'true';

      TRUNKRS_WC_Settings::setIsBigCheckoutTextEnabled($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseOrderRules() {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isOrderRulesEnabled']) === 'true';

      TRUNKRS_WC_Settings::setIsRuleEngineEnabled($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseSubRenewals() {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['isSubRenewalsEnabled']) === 'true';

      TRUNKRS_WC_Settings::setUseSubscriptionRenewals($value);

      status_header(204);
      wp_die();
    }

    public function executeUpdateOrderRuleSet() {
      $this->authorize('manage_woocommerce');

      $value = sanitize_text_field($_POST['orderRules']);

      TRUNKRS_WC_Settings::setRules($value);

      status_header(204);
      wp_die();
    }

    public function executeFindAuditLogEntries() {
      $this->authorize('edit_shop_orders');

      $entries = TRUNKRS_WC_AuditLog::findLatestAuditLogs();

      wp_send_json($entries);
      wp_die();
    }

    public function executeDownloadLabelEndpoint() {
      $this->authorize('edit_shop_orders');

      $trunkrsNr = sanitize_text_field($_GET['trunkrsNr']);
      $labelUrl = TRUNKRS_WC_Api::getLabel($trunkrsNr);

      wp_redirect($labelUrl);
      exit;
    }

    public function executeCancelEndpoint() {
      $this->authorize('edit_shop_orders');

      $orderId = sanitize_text_field($_GET['orderId']);
      $order = new TRUNKRS_WC_Order($orderId);

      $order->cancelShipment();

      wp_redirect(admin_url(sprintf(
        'post.php?post=%s&action=edit',
        $orderId
      )));
      exit;
    }

    public function executeAnnounceEndpoint() {
      $this->authorize('edit_shop_orders');

      $orderId = sanitize_text_field($_GET['orderId']);
      $order = new TRUNKRS_WC_Order($orderId);

      $order->announceShipment(true);

      wp_redirect(admin_url(sprintf(
        'post.php?post=%s&action=edit',
        $orderId
      )));
      exit;
    }

    private function authorize($capability)
    {
      $isValidNonce = check_ajax_referer(TRUNKRS_WC_Bootstrapper::ADMIN_NONCE_ACTION, false, false);

      if (!$isValidNonce || !current_user_can($capability)) {
        wp_die('', '', ['response' => 403]);
      }
    }
  }
}

new TRUNKRS_WC_AdminEndpoints();


