<?php

function createShippingMethodClass()
{
  if (!class_exists('TRUNKRS_WC_ShippingMethod')) {
    class TRUNKRS_WC_ShippingMethod extends WC_Shipping_Method
    {
      public const DELIVERY_DATE_KEY = 'deliveryDate';
      public const CUT_OFF_TIME_KEY = 'cutOffTime';

      private static function getRateType(string $deliveryDate): string
      {
        $todayString = date('Y-m-d');
        return $todayString === $deliveryDate ? 'same' : 'next';
      }

      public static function renderLabel($label, $method)
      {
        $isTrunkrs = substr($method->id, 0, strlen(TRUNKRS_WC_Bootstrapper::DOMAIN)) === TRUNKRS_WC_Bootstrapper::DOMAIN;
        if (!$isTrunkrs) {
          return $label;
        }

        $type = explode('_', $method->id)[1];
        $cutOffTime = TRUNKRS_WC_Utils::parse8601($method->meta_data[self::CUT_OFF_TIME_KEY]);

        $description = null;
        switch ($type) {
          case 'same';
            $description = sprintf(
              __('Plaats je bestelling voor <b>%s</b> om het vandaag te ontvangen!', TRUNKRS_WC_Bootstrapper::DOMAIN),
              esc_html(date_i18n('H:i', $cutOffTime->getTimestamp() + $cutOffTime->getOffset()))
            );
            break;
          case 'next':
            $deliveryDate = TRUNKRS_WC_Utils::parse8601Date($method->meta_data[self::DELIVERY_DATE_KEY]);
            $today = new DateTime("today");
            $diff = $today->diff($deliveryDate);
            $diffDays = (integer)$diff->format("%R%a");

            $deliveryDesc = $diffDays === 1
              ? __('morgen', TRUNKRS_WC_Bootstrapper::DOMAIN)
              : __('op', TRUNKRS_WC_Bootstrapper::DOMAIN) . ' ' . date_i18n('D dS', $deliveryDate->getTimestamp() + $deliveryDate->getOffset());

            $description = sprintf(
              __('Plaats je bestelling voor <b>%s</b> om het %s te ontvangen!', TRUNKRS_WC_Bootstrapper::DOMAIN),
              esc_html(date_i18n('D dS H:i', $cutOffTime->getTimestamp() + $cutOffTime->getOffset())),
              esc_html($deliveryDesc)
            );
            break;
        }

        $logoUrl = TRUNKRS_WC_Settings::getUseDark()
          ? TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-light.svg')
          : TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg');

        return sprintf(
          '<span class="tr-wc-checkout-container">
                    <span class="tr-wc-checkout-title-container">
                        <img class="tr-wc-checkout-logo" alt="Trunkrs logo" src="%s" />
                        <p class="tr-wc-checkout-title"><b>Trunkrs</b>: %s</p>
                    </span>
                    <p class="tr-wc-checkout-description">%s</p>
                  </span>
                ',
          esc_url($logoUrl),
          esc_html(wc_price($method->cost)),
          esc_html($description),
        );
      }

      public function __construct()
      {
        $this->id = TRUNKRS_WC_Bootstrapper::DOMAIN;

        $this->method_title = __('Trunkrs Same-day', TRUNKRS_WC_Bootstrapper::DOMAIN);
        $this->method_description = __('Trunkrs same and next day delivery.', TRUNKRS_WC_Bootstrapper::DOMAIN);

        $this->enabled = 'yes';
        $this->title = $this->settings['title'] ?? __('Trunkrs', TRUNKRS_WC_Bootstrapper::DOMAIN);

        $this->init();
      }

      public function init()
      {
        $this->init_form_fields();
        $this->init_settings();

        add_action(
          'woocommerce_update_options_shipping_' . $this->id,
          [$this, 'process_admin_options']
        );
      }

      /**
       * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
       *
       * @access public
       * @param mixed $package
       * @return void
       */
      public function calculate_shipping($package = [])
      {
        if (!TRUNKRS_WC_Settings::isConfigured()) {
          return;
        }

        $apiRates = TRUNKRS_WC_Api::getShippingRates([
          'orderValue' => $package['contents_cost'],
          'country' => $package['destination']['country'],
        ]);

        if (empty($apiRates)) {
          return;
        }

        foreach ($apiRates as $apiRate) {
          $deliveryTimestamp = TRUNKRS_WC_Utils::parse8601($apiRate->deliveryDate)->getTimestamp();
          $deliveryDate = date('Y-m-d', $deliveryTimestamp);

          $woocommerceRate = [
            'id' => $this->id . '_' . self::getRateType($deliveryDate),
            'label' => $this->title,
            'cost' => $apiRate->price,
            'meta_data' => [
              self::CUT_OFF_TIME_KEY => $apiRate->announceBefore,
              self::DELIVERY_DATE_KEY => $deliveryDate,
            ],
          ];

          $this->add_rate($woocommerceRate);
        }
      }
    }
  }
}

function addTrunkrsShippingMethod($methods)
{
  $methods[] = 'TRUNKRS_WC_ShippingMethod';
  return $methods;
}

add_filter('woocommerce_cart_shipping_method_full_label', 'TRUNKRS_WC_ShippingMethod::renderLabel', 10, 2);
add_action('woocommerce_shipping_init', 'createShippingMethodClass');
add_filter('woocommerce_shipping_methods', 'addTrunkrsShippingMethod');
