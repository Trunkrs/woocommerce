<?php

if (!class_exists('TRUNKRS_WC_Subscriptions')) {
  class TRUNKRS_WC_Subscriptions
  {
    public function __construct()
    {
      add_action('woocommerce_subscription_renewal_payment_complete', [$this, 'renewalPaymentComplete']);
    }

    public function renewalPaymentComplete($subscription, $order)
    {
      if (!TRUNKRS_WC_Settings::getUseSubscriptionRenewals())
        return;

      $initialOrder = new TRUNKRS_WC_Order($subscription);

      if (!$initialOrder->isTrunkrsOrder) return;

      $newOrder = new TRUNKRS_WC_Order($order);
      $newOrder->announceShipment();
    }
  }
}

new TRUNKRS_WC_Subscriptions();
