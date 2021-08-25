<?php

if (!class_exists('WC_TR_Emails')) {
  class WC_TR_Emails
  {
    public function __construct()
    {
      add_action("woocommerce_email_before_order_table", [$this, "renderTrackTraceLink"], 10, 1);
      add_action("woocommerce_my_account_my_orders_actions", [$this, "renderTrackTraceLinkInAccount"], 10, 2);
    }

    public function renderTrackTraceLink($order)
    {
      if (!TR_WC_Settings::isConfigured())
        return;
      if (!TR_WC_Settings::getUseTrackTraceLinks())
        return;

      $trunkrsOrder = new TR_WC_Order($order);
      if (!$trunkrsOrder->isTrackTraceAvailable())
        return;

      $trackingLink = $trunkrsOrder->getTrackTraceLink();
      $deliveryDate = $trunkrsOrder->getFormattedDate();

      $defaultEmailValue = sprintf(
        __('Je hebt gekozen voor Trunkrs als je bezorgdienst. Trunkrs bezorgd jouw bestelling op %s tussen 17 en 22 uur.')
        . '<br /><a href="%s">%s</a> ' . __('om naar de Trunkrs track & trace te gaan'),
        $deliveryDate,
        $trackingLink,
        __('Klik hier')
      );

      echo apply_filters(
        'trunkrs_email_link_text',
        $defaultEmailValue,
        $trunkrsOrder,
      );
    }

    public function renderTrackTraceLinkInAccount(array $actions, $order): array {
      if (!TR_WC_Settings::isConfigured())
        return $actions;
      if (!TR_WC_Settings::getUseAccountActions())
        return $actions;

      $trunkrsOrder = new TR_WC_Order($order);
      if (!$trunkrsOrder->isTrackTraceAvailable())
        return $actions;

      $trackTraceLink = $trunkrsOrder->getTrackTraceLink();
      $actions['trwc_track_trace_' . $trackTraceLink] = [
        'url'  => $trackTraceLink,
        'name' => apply_filters(
          'trunkrs_track_trace_account_button',
          __('Track & Trace')
        ),
      ];

      return $actions;
    }
  }
}

new WC_TR_Emails();
