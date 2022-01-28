<?php

if (!class_exists('TRUNKRS_WC_ShipmentTracking')) {
  class TRUNKRS_WC_ShipmentTracking
  {
    private const PROVIDER_NAME = 'Trunkrs';

    /**
     * Updates the shipment tracking details for the specified order.
     * @param $orderId int The order id of the order.
     * @param $trunkrsNr string The Trunkrs number.
     * @param $trackTraceUrl string The postal code.
     * @param $deliveryDate string The delivery date of the shipment.
     * @return void
     */
    public static function setTrackingInfo($orderId, $trunkrsNr, $trackTraceUrl, $deliveryDate)
    {
      $date = TRUNKRS_WC_Utils::parse8601($deliveryDate);

      if (function_exists('wc_st_add_tracking_number')) {
        wc_st_add_tracking_number(
          $orderId,
          $trunkrsNr,
          self::PROVIDER_NAME,
          $date->getTimestamp(),
          $trackTraceUrl
        );
      }
    }

    public function __construct()
    {
      add_filter('wc_shipment_tracking_get_providers', [$this, 'addTrunkrsProvider']);
    }

    public function addTrunkrsProvider(array $providers): array
    {
      $providers['Netherlands'][self::PROVIDER_NAME] = 'https://parcel.trunkrs.nl/barcode/%1$s';
      $providers['Belgium'][self::PROVIDER_NAME] = 'https://parcel.trunkrs.nl/barcode/%1$s';

      return $providers;
    }
  }
}

new TRUNKRS_WC_ShipmentTracking();
