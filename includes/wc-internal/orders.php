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
      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $this->createOrder($wpPostId);
    }

    public function createOrder(string $orderId)
    {
      $trunkrsOrder = new TR_WC_Order($orderId);

      if (!$trunkrsOrder->isTrunkrsOrder)
        return;

      $trunkrsOrder->announceShipment();
    }

    public function deleteOrder(string $wpPostId)
    {
      $type = get_post_type($wpPostId);
      if ($type !== 'shop_order') {
        return;
      }

      $this->cancelOrder($wpPostId);
    }

    public function cancelOrder(string $orderId)
    {
      $order = new TR_WC_Order($orderId);

      if ($order->isTrunkrsOrder) {
        $order->cancelShipment();
      }
    }
  }
}

new WC_TRUNKRS_Orders();
