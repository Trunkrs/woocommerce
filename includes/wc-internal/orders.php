<?php

if (!class_exists('TRUNKRS_WC_Orders')) {
  class TRUNKRS_WC_Orders
  {
    public function __construct()
    {
      add_action('untrashed_post', [$this, 'untrashOrder']);
      add_action('woocommerce_order_refunded', [$this, 'cancelOrder']);
      add_action('woocommerce_order_status_cancelled', [$this, 'cancelOrder']);
      add_action('woocommerce_order_status_changed', [$this, 'orderStatusChanged']);
      add_action('wp_trash_post', [$this, 'deleteOrder']);
      add_action('woocommerce_checkout_order_processed', [$this, 'createOrder']);
      add_action('save_post_shop_order', [$this, 'orderStatusChanged']);
    }

    public function untrashOrder(string $wpPostId)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;

      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $trunkrsOrder = new TRUNKRS_WC_Order($wpPostId, true, true);
      if (!$trunkrsOrder->isTrunkrsOrder)
        return;
      $trunkrsOrder->announceShipment(true);
    }

    public function createOrder(string $orderId)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;
      $trunkrsOrder = new TRUNKRS_WC_Order($orderId, true, true);

      if (!$trunkrsOrder->isTrunkrsOrder)
        return;

      $trunkrsOrder->announceShipment();
    }

    public function deleteOrder(string $wpPostId)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;

      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $this->cancelOrder($wpPostId);
    }

    public function cancelOrder(string $orderId)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;

      $order = new TRUNKRS_WC_Order($orderId);

      if ($order->isTrunkrsOrder) {
        $order->cancelShipment();
      }
    }

    public function orderStatusChanged(string $orderId)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;

      $order = new TRUNKRS_WC_Order($orderId, true, true);

      if ($order->isTrunkrsOrder && $order->isAnnounceable()) {
        $order->announceShipment();
      }
    }
  }
}

new TRUNKRS_WC_Orders();
