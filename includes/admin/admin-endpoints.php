<?php

if (!class_exists('WC_TR_AdminEndpoints')) {
  class TR_WC_AdminEndpoints
  {
    const DOWNLOAD_LABEL_ACTION = 'tr-wc_download-label';
    const CANCEL_ACTION = 'tr-wc_cancel';
    const RE_ANNOUNCE_ACTION = 'tr-wc_reannounce';
    const REGISTER_ACTION = 'tr-wc_register-plugin';
    const UPDATE_USE_DARK_ACTION = 'tr-wc_update-use-dark';

    public function __construct()
    {
      add_action('wp_ajax_' . self::REGISTER_ACTION, [$this, 'executeRegisterEndpoint']);
      add_action('wp_ajax_' . self::UPDATE_USE_DARK_ACTION, [$this, 'executeUpdateUseDarkEndpoint']);

      add_action('wp_ajax_' . self::DOWNLOAD_LABEL_ACTION, [$this, 'executeDownloadLabelEndpoint']);
      add_action('wp_ajax_' . self::CANCEL_ACTION, [$this, 'executeCancelEndpoint']);
      add_action('wp_ajax_' . self::RE_ANNOUNCE_ACTION, [$this, 'executeAnnounceEndpoint']);
    }

    public function executeRegisterEndpoint()
    {
      if (TR_WC_Settings::isConfigured()) {
        status_header(409);
        wp_die();
        return;
      }

      $accessToken = $_POST['accessToken'];
      $integrationId = $_POST['integrationId'];
      $orgId = $_POST['organizationId'];
      $orgName = $_POST['organizationName'];

      TR_WC_Settings::setConfigured(true);
      TR_WC_Settings::setAccessToken($accessToken);
      TR_WC_Settings::setIntegrationDetails([
        'integrationId' => $integrationId,
        'organizationId' => $orgId,
        'organizationName' => $orgName,
      ]);

      status_header(204);
      wp_die();
    }

    public function executeUpdateUseDarkEndpoint()
    {
      $useDark = $_POST['isDarkLogo'] === 'true';

      TR_WC_Settings::setUseDark($useDark);

      status_header(204);
      wp_die();
    }

    public function executeDownloadLabelEndpoint() {
      $trunkrsNr = $_GET['trunkrsNr'];
      $labelUrl = WC_TRUNKRS_API::getLabel($trunkrsNr);

      wp_redirect($labelUrl);
      exit;
    }

    public function executeCancelEndpoint() {
      $orderId = $_GET['orderId'];
      $order = new TR_WC_Order($orderId);

      $order->cancelShipment();

      wp_redirect(admin_url(sprintf(
        'post.php?post=%s&action=edit',
        $orderId
      )));
      exit;
    }

    public function executeAnnounceEndpoint() {
      $orderId = $_GET['orderId'];
      $order = new TR_WC_Order($orderId);

      $order->announceShipment();

      wp_redirect(admin_url(sprintf(
        'post.php?post=%s&action=edit',
        $orderId
      )));
      exit;
    }
  }
}

new TR_WC_AdminEndpoints();


