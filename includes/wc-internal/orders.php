<?php

if (!class_exists('WC_TRUNKRS_Orders')) {
  class WC_TRUNKRS_Orders
  {
    public function __construct()
    {
      add_action('untrashed_post', [$this, 'untrashOrder']);
      add_action('woocommerce_order_refunded', [$this, 'cancelOrder']);
      add_action('woocommerce_order_status_cancelled', [$this, 'cancelOrder']);
      add_action('wp_trash_post', [$this, 'deleteOrder']);
      add_action('woocommerce_order_status_processing', [$this, 'createOrder']);
    }

    public function untrashOrder(string $wpPostId)
    {
      if (!TR_WC_Settings::isConfigured())
        return;

      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $this->createOrder($wpPostId);
    }

    public function createOrder(string $orderId)
    {
      if (!TR_WC_Settings::isConfigured())
        return;
      $trunkrsOrder = new TR_WC_Order($orderId);

      if (!$trunkrsOrder->isTrunkrsOrder)
        return;

      $trunkrsOrder->announceShipment();
    }

    public function deleteOrder(string $wpPostId)
    {
      if (!TR_WC_Settings::isConfigured())
        return;

      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $this->cancelOrder($wpPostId);
    }

    public function cancelOrder(string $orderId)
    {
      if (!TR_WC_Settings::isConfigured())
        return;

      $order = new TR_WC_Order($orderId);

      if ($order->isTrunkrsOrder) {
        $order->cancelShipment();
      }
    }
  }
}

new WC_TRUNKRS_Orders();
