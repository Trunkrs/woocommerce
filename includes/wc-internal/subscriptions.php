<?php

if (!class_exists('TRUNKRS_WC_Subscriptions')) {
  class TRUNKRS_WC_Subscriptions
  {
    public function __construct()
    {
      add_action('woocommerce_subscription_renewal_payment_complete', [$this, 'renewalPaymentComplete']);
    }

    public function renewalPaymentComplete($order)
    {
      if (!TRUNKRS_WC_Settings::getUseSubscriptionRenewals())
        return;

      $newOrder = new TRUNKRS_WC_Order($order, true, true);
      if (!$newOrder->isTrunkrsOrder) return;

      $newOrder->announceShipment(true);
    }
  }
}

new TRUNKRS_WC_Subscriptions();
