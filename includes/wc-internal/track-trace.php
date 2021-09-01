<?php

if (!class_exists('TRUNKRS_WC_TrackTrace')) {
  class TRUNKRS_WC_TrackTrace
  {
    public function __construct()
    {
      add_action("woocommerce_email_before_order_table", [$this, "renderTrackTraceLink"], 10, 2);
      add_action("woocommerce_my_account_my_orders_actions", [$this, "renderTrackTraceLinkInAccount"], 10, 2);
    }

    public function renderTrackTraceLink($order, $isAdmin)
    {
      if ($isAdmin)
        return;
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;
      if (!TRUNKRS_WC_Settings::getUseTrackTraceLinks())
        return;

      $trunkrsOrder = new TRUNKRS_WC_Order($order);
      if (!$trunkrsOrder->isTrackTraceAvailable())
        return;

      $trackingLink = $trunkrsOrder->getTrackTraceLink();
      $deliveryDate = $trunkrsOrder->getFormattedDate();

      ?>
      <p>
        <?php
        esc_html_e(sprintf(
          __('Je hebt gekozen voor Trunkrs, Trunkrs bezorgd jouw bestelling op %s tussen 17 en 22 uur.', TRUNKRS_WC_Bootstrapper::DOMAIN),
          $deliveryDate
        ));
        ?>
        <br/>
        <a href="<?php esc_attr_e($trackingLink) ?>">
          <?php esc_html_e('Klik hier', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
        </a>
        <?php esc_html_e('om naar de Trunkrs track & trace te gaan', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
      </p>
      <?php
    }

    public function renderTrackTraceLinkInAccount(array $actions, $order): array
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return $actions;
      if (!TRUNKRS_WC_Settings::getUseAccountActions())
        return $actions;

      $trunkrsOrder = new TRUNKRS_WC_Order($order);
      if (!$trunkrsOrder->isTrackTraceAvailable())
        return $actions;

      $trackTraceLink = $trunkrsOrder->getTrackTraceLink();
      $actions['trwc_track_trace_' . $trackTraceLink] = [
        'url' => $trackTraceLink,
        'name' => apply_filters(
          'trunkrs_track_trace_account_button',
          __('Track & Trace', TRUNKRS_WC_Bootstrapper::DOMAIN)
        ),
      ];

      return $actions;
    }
  }
}

new TRUNKRS_WC_TrackTrace();
