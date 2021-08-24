<?php

if (!class_exists('WC_TRUNKRS_Utils')) {
  class WC_TRUNKRS_Utils
  {
    public const DOMAIN = 'trunkrs-woocommerce';

    private const SHIPPING_DETAILS = 'tr-wc_shipping-details';

    private static $rootUrl = null;

    /**
     * Finds and entry in an array based on the specified predicate.
     * @param array $array The array to find the entry in.
     * @param callable $predicate The predicate for finding the right element.
     * @return mixed|null
     */
    public static function findInArray(array $array, callable $predicate)
    {
      foreach ($array as $entry)
        if ($predicate($entry))
          return $entry;

      return null;
    }

    /**
     * Retrieves the base url for assets.
     * @return string The base url for assets.
     */
    public static function getBaseUrl()
    {
      if (is_null(self::$rootUrl)) {
        $pathParts = explode('/', rtrim(dirname(__FILE__), '/'));
        array_pop($pathParts);
        self::$rootUrl = implode('/', $pathParts) . '/something.php';
      }

      return plugins_url('/build', self::$rootUrl);
    }

    /**
     * Creates a full asset path for use in HTML.
     * @param $filePath string The path to the file.
     * @return string The full path for the file.
     */
    public static function createAssetUrl($filePath)
    {
      if (is_null(self::$rootUrl)) {
        $pathParts = explode('/', rtrim(dirname(__FILE__), '/'));
        array_pop($pathParts);
        self::$rootUrl = implode('/', $pathParts) . '/something.php';
      }

      return plugins_url(
        '/build/' . ltrim($filePath, '/'),
        self::$rootUrl
      );
    }

    /**
     * Parses the ISO 8601 date string into DateTime object.
     * @param $dateString string The ISO-8601 date string.
     * @return DateTime The parsed ISO-8601 date time value.
     */
    public static function parse8601($dateString)
    {
      $result = DateTime::createFromFormat(
        'Y-m-d\TH:i:s.v\Z',
        $dateString
      );

      $result->setTimezone(new DateTimeZone('Europe/Amsterdam'));

      return $result;
    }

    /**
     * Parses the ISO 8601 date only string into DateTime object.
     * @param $dateString string The ISO-8601 date string.
     * @return DateTime The parsed ISO-8601 date time value.
     */
    public static function parse8601Date($dateString)
    {
      $result = DateTime::createFromFormat(
        'Y-m-d',
        $dateString
      );

      $result->setTimezone(new DateTimeZone('Europe/Amsterdam'));

      return $result;
    }

    /**
     * Gets the metadata value from the shipping item.
     * @param mixed $shippingItem The WC_Order_Item_Shipping item to find the metadata within.
     * @param string $key The metadata item key to find.
     * @return mixed|null
     */
    public static function getMetaDataValue($shippingItem, string $key)
    {
      $metaItem = self::findInArray($shippingItem->get_meta_data(), function ($metaItem) use ($key) {
        return $metaItem->get_data()['key'] === $key;
      });

      if (is_null($metaItem))
        return null;

      return $metaItem->get_data()['value'];
    }
  }
}
