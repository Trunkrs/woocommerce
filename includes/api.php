<?php

if (!class_exists('WC_TRUNKRS_API')) {
  if (!class_exists('WP_Http')) {
    include_once(ABSPATH . WPINC . '/class-http.php');
  }

  class WC_TRUNKRS_API
  {
    private const RatesBaseResource = 'shipping-rates';
    private const ShipmentResource = 'shipments';
    private const LabelsResource = 'labels';

    private static function makeQueriedResource(string $resource, array $query): string
    {
      $isQueryAlreadyUsed = strpos($resource, '?');
      $startDelimiter = $isQueryAlreadyUsed !== false ? '&' : '?';

      $count = 0;
      $queryString = '';
      foreach ($query as $key => $value) {
        $delimiter = $count === 0 ? $startDelimiter : '&';
        $queryString .= $delimiter . $key . '=' . urlencode($value);
        $count += 1;
      }

      return $resource . $queryString;
    }

    private static function makeRequest(string $method, string $resource, array $payload = null, int $timeout = 3500)
    {

      $accessToken = TR_WC_Settings::getAccessToken();
      $requestArgs = [
        'timeout' => $timeout,
        'method' => $method,
        'user-agent' => sprintf('Trunkrs/Woocommerce v%s', WC_TRUNKRS_VERSION),
        'headers' => [
          'Authorization' => sprintf('Bearer %s', $accessToken),
          'Content-Type' => 'application/json; charset=utf-8'
        ],
      ];

      $isMethodWithPayload = $method === 'POST' || $method === 'PUT' || $method === 'PATCH';

      if (!is_null($payload) && $isMethodWithPayload) {
        $requestArgs['body'] = json_encode($payload);
      } elseif (!is_null($payload)) {
        $resource = self::makeQueriedResource($resource, $payload);
      }

      $requestUrl = TR_WC_Settings::getResourceUrl($resource);
      $httpRequest = new WP_Http();
      return $httpRequest->request($requestUrl, $requestArgs);
    }

    /**
     * Announce the shipment to the Trunkrs platform.
     * @param $order WC_Order The WooCommerce order object.
     * @param $reference string The shipment reference.
     * @param $deliveryDate string|null Optional delivery date for the shipment.
     * @return stdClass|null The announcement result.
     */
    public static function announceShipment($order, string $reference, $deliveryDate)
    {
      $companyName = $order->get_shipping_company();
      $addressLine2 = $order->get_shipping_address_2();
      $singleShipmentBody = [
        'reference' => $reference,
        'recipient' => [
          'name' => sprintf(
            '%s %s',
            $order->get_shipping_first_name(),
            $order->get_shipping_last_name()
          ),
          'email' => $order->get_billing_email(),
          'phoneNumber' => $order->get_billing_phone(),
          'location' => [
            'address' => empty($addressLine2)
              ? $order->get_shipping_address_1()
              : $order->get_shipping_address_1() . ' ' . $addressLine2,
            'postalCode' => $order->get_shipping_postcode(),
            'city' => $order->get_shipping_city(),
            'country' => $order->get_shipping_country(),
          ]
        ],
      ];

      if (!empty($deliveryDate)) {
        $singleShipmentBody['intendedDeliveryDate'] = $deliveryDate;
      }
      if (!empty($companyName)) {
        $singleShipmentBody['recipient']['companyName'] = $companyName;
      }

      $response = self::makeRequest(
        'POST',
        self::ShipmentResource,
        ['shipments' => [$singleShipmentBody]],
        6000
      );

      if (is_wp_error($response) || $response['response']['code'] > 201) {
        return null;
      }

      $response = json_decode($response['body']);
      $shipment = WC_TRUNKRS_Utils::findInArray($response->success, function ($shipmentResult) use ($reference) {
        return $shipmentResult->overriddenReference === $reference;
      });

      return $shipment;
    }

    /**
     * Retrieves the current shipping rates for the integration.
     * @return array The current shipping rates.
     */
    public static function getShippingRates(array $orderDetails): array
    {
      $response = self::makeRequest(
        'GET',
        self::RatesBaseResource,
        $orderDetails,
      );

      if (is_wp_error($response) || $response['response']['code'] !== 200) {
        return [];
      }

      $rates = json_decode($response['body']);
      // For now only take the first one
      return empty($rates) ? $rates : [$rates[0]];
    }

    /**
     * Retrieves the label data for the
     * @param string $trunkrsNr The Trunkrs number of the shipment.
     * @return mixed|null The label data.
     */
    public static function getLabel(string $trunkrsNr)
    {
      $response = self::makeRequest(
        'GET',
        self::LabelsResource . '/' . $trunkrsNr,
        null,
        6000
      );

      if (is_wp_error($response) || $response['response']['code'] !== 200) {
        return null;
      }

      $response = json_decode($response['body']);
      return $response->url;
    }

    /**
     * Cancels the shipment with on the Trunkrs platform.
     * @param string $trunkrNr The trunkrs number of the shipment.
     * @return bool Flag reflecting whether action has been successful.
     */
    public static function cancelShipment(string $trunkrNr): bool
    {
      $response = self::makeRequest(
        'DELETE',
        self::ShipmentResource . '/' . $trunkrNr,
        null,
        6000
      );

      return !is_wp_error($response)
        && $response['response']['code'] < 300;
    }
  }
}

