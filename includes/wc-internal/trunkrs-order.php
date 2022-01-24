<?php

if (!class_exists('TRUNKRS_WC_Order')) {
  class TRUNKRS_WC_Order
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
     * @var array|string|false The order meta-data related to the Trunkrs integration.
     */
    var $orderMeta;

    /**
     * @param $orderOrOrderId int|WC_Order The WooCommerce order instance.
     * @param bool $withMeta bool Flag whether to also parse meta data details.
     */
    public function __construct($orderOrOrderId, $withMeta = true)
    {
      $this->order = $orderOrOrderId instanceof WC_Order
        ? $orderOrOrderId
        : new WC_Order($orderOrOrderId);

      $this->init($withMeta);
    }

    private function isTrunkrsOrder() {
      if (!empty($this->orderMeta) && is_array($this->orderMeta)) {
        return true;
      }

      if (TRUNKRS_WC_Settings::getUseAllOrdersAreTrunkrsActions()) {
        return true;
      }

      if (TRUNKRS_WC_Settings::isRuleEngineEnabled()) {
        $ruleSet = new TRUNKRS_WC_RuleSet(TRUNKRS_WC_Settings::getOrderRuleSet());
        return $ruleSet->matchOrder($this);
      }

      $shippingItem = $this->order->get_items('shipping');
      foreach ($shippingItem as $item) {
        $shippingMethodId = $item->get_method_id();
        if ($shippingMethodId === TRUNKRS_WC_Bootstrapper::DOMAIN) {
          return true;
        }
      }

      return false;
    }

    private function init($withMeta)
    {
      $this->orderMeta = get_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, true);
      $this->isTrunkrsOrder = $this->isTrunkrsOrder();

      if (!$this->isTrunkrsOrder || !$withMeta) {
        return;
      }

      // When no meta is available stop processing
      if (empty($this->orderMeta) || !is_array($this->orderMeta))
        return;

      $this->trunkrsNr = $this->orderMeta['trunkrsNr'];
      $this->deliveryDate = $this->orderMeta['deliveryDate'];
      $this->isCancelled = $this->orderMeta['isCanceled'];
      $this->isAnnounceFailed = $this->orderMeta['isAnnounceFailed'];
    }

    /**
     * Formats the delivery date in a human-readable format.
     * @return string The formatted delivery date.
     */
    public function getFormattedDate(): string {
      if (empty($this->deliveryDate))
        return '';

      $date = TRUNKRS_WC_Utils::parse8601($this->deliveryDate);
      return $date->format('Y-m-d');
    }

    /**
     * Retrieves whether this order has an available Track&Trace link.
     * @return bool Flag whether track&trace link is available.
     */
    public function isTrackTraceAvailable(): bool {
      return $this->isTrunkrsOrder && !$this->isAnnounceFailed;
    }

    /**
     * Creates a track&trace link for this order's shipment.
     * @return string The track&trace link.
     */
    public function getTrackTraceLink(): string {
      $postalCode = $this->order->get_shipping_postcode();
      return TRUNKRS_WC_Settings::TRACK_TRACE_BASE_URL . $this->trunkrsNr . '/' . $postalCode;
    }

    /**
     * Announces the order as a new shipment to the Trunkrs API.
     */
    public function announceShipment() {
      $shippingItems = $this->order->get_items('shipping');

      foreach ($shippingItems as $item) {
        $deliveryDate = TRUNKRS_WC_Utils::getMetaDataValue($item, self::DELIVERY_DATE_KEY);

        $reference = sprintf('%s-%s', uniqid(), $this->order->get_id());
        $shipment = TRUNKRS_WC_Api::announceShipment($this->order, $reference, $deliveryDate);

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
      $isSuccess = TRUNKRS_WC_Api::cancelShipment($this->trunkrsNr);

      if ($isSuccess) {
        $this->order->add_order_note(sprintf(
          __("De Trunkrs zending '%s' is geannuleerd.", TRUNKRS_WC_Bootstrapper::DOMAIN),
          $this->trunkrsNr
        ));

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

      $currentValue = get_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, true);
      if (empty($currentValue) || !is_array($currentValue))
        add_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, $metaValue);
      else
        update_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, $metaValue);
    }
  }
}
