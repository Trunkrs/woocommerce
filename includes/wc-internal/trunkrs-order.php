<?php

if (!class_exists('TR_WC_Settings')) {
  class TR_WC_Order
  {
    public const DELIVERY_DATE_KEY = 'deliveryDate';
    public const CUT_OFF_TIME_KEY = 'cutOffTime';

    /**
     * @var WC_Order The order of the order.
     */
    var $order;

    /**
     * @var bool Flag whether this is a Trunkrs order.
     */
    var $isTrunkrsOrder = false;

    /**
     * @var string The Trunkrs number.
     */
    var $trunkrsNr;

    /**
     * @var string The delivery date in ISO-8601 format.
     */
    var $deliveryDate;

    /**
     * @var bool Flag whether this shipment has been canceled.
     */
    var $isCancelled;

    /**
     * @var bool Flag whether the announcement of this shipment has failed.
     */
    var $isAnnounceFailed = true;

    /**
     * @param $orderId int The WooCommerce order instance.
     * @param bool $withMeta bool Flag whether to also parse meta data details.
     */
    public function __construct($orderId, $withMeta = true)
    {
      $this->order = new WC_Order($orderId);
      $this->init($withMeta);
    }

    private function init($withMeta)
    {
      $shippingItem = $this->order->get_items('shipping');

      foreach ($shippingItem as $item) {
        $shippingMethodId = $item->get_method_id();
        if ($shippingMethodId !== WC_TRUNKRS_Utils::DOMAIN) {
          continue;
        }

        $this->isTrunkrsOrder = true;
        if (!$withMeta)
          return;

        $meta = get_post_meta($this->order->get_id(), WC_TRUNKRS_Utils::DOMAIN, true);
        if (empty($meta) || !is_array($meta))
          return;

        $this->trunkrsNr = $meta['trunkrsNr'];
        $this->deliveryDate = $meta['deliveryDate'];
        $this->isCancelled = $meta['isCanceled'];
        $this->isAnnounceFailed = $meta['isAnnounceFailed'];
      }
    }

    /**
     * Formats the delivery date in a human-readable format.
     * @return string The formatted delivery date.
     */
    public function getFormattedDate(): string {
      if (empty($this->deliveryDate))
        return '';

      $date = WC_TRUNKRS_Utils::parse8601($this->deliveryDate);
      return $date->format('Y-m-d');
    }

    /**
     * Announces the order as a new shipment to the Trunkrs API.
     */
    public function announceShipment() {
      $shippingItems = $this->order->get_items('shipping');

      foreach ($shippingItems as $item) {
        $deliveryDate = WC_TRUNKRS_Utils::getMetaDataValue($item, self::DELIVERY_DATE_KEY);

        $reference = sprintf('%s-%s', uniqid(), $this->order->get_id());
        $shipment = WC_TRUNKRS_API::announceShipment($this->order, $reference, $deliveryDate);

        $item->delete_meta_data(self::DELIVERY_DATE_KEY);
        $item->delete_meta_data(self::CUT_OFF_TIME_KEY);

        $item->save_meta_data();

        if (is_null($shipment)) {
          $this->isAnnounceFailed = true;
          $this->save();
          return;
        }

        $this->trunkrsNr = $shipment->trunkrsNumber;
        $this->deliveryDate = $shipment->deliveryWindow->date;
        $this->isCancelled = false;
        $this->isAnnounceFailed = false;
        $this->save();
      }
    }

    /**
     * Cancels the shipment attached to this order.
     */
    public function cancelShipment() {
      $isSuccess = WC_TRUNKRS_API::cancelShipment($this->trunkrsNr);

      if ($isSuccess) {
        $this->order->add_order_note(__("The Trunkrs shipment '$this->trunkrsNr' has been cancelled."));

        $this->isCancelled = true;
        $this->save();
      }
    }

    /**
     * Saves the metadata details into the database.
     */
    public function save()
    {
      $metaValue = [
        'trunkrsNr' => $this->trunkrsNr,
        'deliveryDate' => $this->deliveryDate,
        'isCanceled' => $this->isCancelled,
        'isAnnounceFailed' => $this->isAnnounceFailed,
      ];

      $currentValue = get_post_meta($this->order->get_id(), WC_TRUNKRS_Utils::DOMAIN, true);
      if (empty($currentValue) || !is_array($currentValue))
        add_post_meta($this->order->get_id(), WC_TRUNKRS_Utils::DOMAIN, $metaValue);
      else
        update_post_meta($this->order->get_id(), WC_TRUNKRS_Utils::DOMAIN, $metaValue);
    }
  }
}
