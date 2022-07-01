<?php

if (!class_exists('TRUNKRS_WC_Order')) {
  class TRUNKRS_WC_Order
  {
    public const TYCHE_DELIVERY_DATE_KEY = 'Delivery Date';

    public const TYCHE_DELIVERY_DATE_PREFIX = '_orddd';
    public const TYCHE_DELIVERY_DATE_TIMESTAMP_POSTFIX = 'timestamp';
    public const YITH_DELIVER_DATE_META = 'ywcdd_order_delivery_date';
    public const YITH_SHIPPING_DATE_META = 'ywcdd_order_shipping_date';
    public const SELF_DELIVERY_DATE_META = '_tr_delivery_date_ts';

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
    public function __construct($orderOrOrderId, bool $withMeta = true, bool $withLogging = false)
    {
      $this->order = $orderOrOrderId instanceof WC_Order
        ? $orderOrOrderId
        : new WC_Order($orderOrOrderId);

      $this->init($withMeta, $withLogging);
    }

    private function isTrunkrsOrder(bool $withLogging)
    {
      if (!empty($this->orderMeta) && is_array($this->orderMeta)) {
        return true;
      }

      if (TRUNKRS_WC_Settings::getUseAllOrdersAreTrunkrsActions()) {
        return true;
      }

      if (TRUNKRS_WC_Settings::isRuleEngineEnabled()) {
        $ruleSet = new TRUNKRS_WC_RuleSet(TRUNKRS_WC_Settings::getOrderRuleSet());
        return $ruleSet->matchOrder($this, $withLogging);
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

    private function init(bool $withMeta, bool $withLogging = false)
    {
      $this->orderMeta = get_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, true);
      $this->isTrunkrsOrder = $this->isTrunkrsOrder($withLogging);

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

    private function getTychePluginMeta()
    {
      $meta = TRUNKRS_WC_Utils::findInArray(
        $this->order->get_meta_data(),
        function ($metaItem) {
          $data = $metaItem->get_data();
          $startsWithPrefix = substr($data['key'], 0, strlen(self::TYCHE_DELIVERY_DATE_PREFIX)) === self::TYCHE_DELIVERY_DATE_PREFIX;
          $endsWithPostFix = substr($data['key'], -strlen(self::TYCHE_DELIVERY_DATE_TIMESTAMP_POSTFIX)) === self::TYCHE_DELIVERY_DATE_TIMESTAMP_POSTFIX;

          return $startsWithPrefix && $endsWithPostFix;
        }
      );

      return is_null($meta) ? null : $meta->get_data();
    }

    private function getDeliveryDate($item)
    {
      $deliveryDatePlugin = $this->getTychePluginMeta();

      if (isset($deliveryDatePlugin)) {
        $parsed = DateTime::createFromFormat('U', $deliveryDatePlugin['value']);
        if ($parsed !== false) {
          return TRUNKRS_WC_Utils::format8601Date($parsed);
        }
      }

      $deliveryDatePlugin = $this->order->get_meta(self::YITH_DELIVER_DATE_META);
      if (isset($deliveryDatePlugin)) {
        $parsed = DateTime::createFromFormat('Y-m-d', $deliveryDatePlugin);
        if ($parsed !== false) {
          return TRUNKRS_WC_Utils::format8601Date($parsed);
        }
      }

      $deliveryDateSelf = $this->order->get_meta(self::SELF_DELIVERY_DATE_META);
      if (isset($deliveryDateSelf)) {
        $parsed = DateTime::createFromFormat('U', $deliveryDateSelf);
        if ($parsed !== false) {
          return TRUNKRS_WC_Utils::format8601Date($parsed);
        }
      }

      return !isset($item)
        ? null
        : $item->get_meta(self::DELIVERY_DATE_KEY);
    }

    private function setDeliveryDate() {
      $dateValue = TRUNKRS_WC_Utils::parse8601($this->deliveryDate);
      $dateValue->setTime(12, 0, 0);

      $tycheMeta = $this->getTychePluginMeta();
      if (!empty($tycheMeta)) {
        $dateString = $dateValue->format('d F, Y');
        $dateStamp = $dateValue->getTimestamp();

        update_post_meta($this->order->get_id(), self::TYCHE_DELIVERY_DATE_KEY, $dateString);
        update_post_meta($this->order->get_id(), $tycheMeta['key'], $dateStamp);
      }

      $yithMeta = $this->order->get_meta(self::YITH_DELIVER_DATE_META);
      if (!empty($yithMeta)) {
        $dateString = $dateValue->format('Y-m-d');

        update_post_meta($this->order->get_id(), self::YITH_DELIVER_DATE_META, $dateString);
        update_post_meta($this->order->get_id(), self::YITH_SHIPPING_DATE_META, $dateString);
      }

      $selfMeta = $this->order->get_meta(self::SELF_DELIVERY_DATE_META);
      if (!empty($selfMeta)) {
        $timestamp = $dateValue->getTimestamp();
        update_post_meta($this->order->get_id(), self::SELF_DELIVERY_DATE_META, $timestamp);
      }
    }

    /**
     * Checks whether this order can be announced in its current state.
     * @return bool Value representing whether this order can be announced.
     */
    public function isAnnounceable(): bool
    {
      return (!isset($this->trunkrsNr) || $this->isAnnounceFailed) && !$this->isCancelled;
    }

    /**
     * Formats the delivery date in a human-readable format.
     * @return string The formatted delivery date.
     */
    public function getFormattedDate(): string
    {
      if (empty($this->deliveryDate))
        return '';

      $date = TRUNKRS_WC_Utils::parse8601($this->deliveryDate);
      return $date->format('Y-m-d');
    }

    /**
     * Retrieves whether this order has an available Track&Trace link.
     * @return bool Flag whether track&trace link is available.
     */
    public function isTrackTraceAvailable(): bool
    {
      return $this->isTrunkrsOrder && !$this->isAnnounceFailed;
    }

    /**
     * Creates a track&trace link for this order's shipment.
     * @return string The track&trace link.
     */
    public function getTrackTraceLink(): string
    {
      $postalCode = $this->order->get_shipping_postcode();
      return TRUNKRS_WC_Settings::TRACK_TRACE_BASE_URL . $this->trunkrsNr . '/' . $postalCode;
    }

    /**
     * Announces the order as a new shipment to the Trunkrs API.
     */
    public function announceShipment(bool $force = false)
    {
      if (!$force && !$this->isAnnounceable()) return;

      $shippingItem = TRUNKRS_WC_Utils::firstInIterable($this->order->get_items('shipping'));
      $deliveryDate = $this->getDeliveryDate($shippingItem);

      $reference = $this->order->get_order_key();
      $shipment = TRUNKRS_WC_Api::announceShipment($this->order, $reference, $deliveryDate);

      if (isset($shippingItem)) {
        $shippingItem->delete_meta_data(self::DELIVERY_DATE_KEY);
        $shippingItem->delete_meta_data(self::CUT_OFF_TIME_KEY);

        $shippingItem->save_meta_data();
      }

      if (is_null($shipment)) {
        $this->isAnnounceFailed = true;
        $this->save();
        return;
      }

      $this->trunkrsNr = $shipment->trunkrsNumber;
      $this->deliveryDate = $shipment->deliveryWindow->date;
      $this->isCancelled = false;
      $this->isAnnounceFailed = false;

      TRUNKRS_WC_ShipmentTracking::setTrackingInfo(
        $this->order->get_id(),
        $this->trunkrsNr,
        $this->getTrackTraceLink(),
        $this->deliveryDate
      );

      $this->save();
    }

    /**
     * Cancels the shipment attached to this order.
     */
    public function cancelShipment()
    {
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

      if (!$this->isAnnounceFailed) {
        $this->setDeliveryDate();
      }

      $currentValue = get_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, true);
      if (empty($currentValue) || !is_array($currentValue))
        add_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, $metaValue);
      else
        update_post_meta($this->order->get_id(), TRUNKRS_WC_Bootstrapper::DOMAIN, $metaValue);
    }
  }
}
