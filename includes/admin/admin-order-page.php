<?php

if (!class_exists('TRUNKRS_WC_AdminOrderPage')) {
  class TRUNKRS_WC_AdminOrderPage
  {
    public function __construct()
    {
      add_action('add_meta_boxes_shop_order', [$this, 'renderTrunkrsBox']);
    }

    public function renderTrunkrsBox($post)
    {
      if (!TRUNKRS_WC_Settings::isConfigured())
        return;

      $trunkrsOrder = new TRUNKRS_WC_Order($post->ID);

      if (!$trunkrsOrder->isTrunkrsOrder)
        return;

      $logoImg = sprintf(
        '<img class="tr-wc-admin-small-logo" alt="Trunkrs logo" src="%s" />',
        TRUNKRS_WC_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg')
      );

      if ($trunkrsOrder->isAnnounceFailed && !$trunkrsOrder->isCancelled) {
        add_meta_box(
          'wc_tr_shipment_side_box',
          $logoImg . __('Zending details', TRUNKRS_WC_Bootstrapper::DOMAIN),
          [$this, 'renderFailedSideBarContent'],
          'shop_order',
          'side',
          'default'
        );
        return;
      }

      add_meta_box(
        'wc_tr_shipment_side_box',
         $logoImg . __('Zending details', TRUNKRS_WC_Bootstrapper::DOMAIN),
        [$this, 'renderSideBarContent'],
        'shop_order',
        'side',
        'default'
      );
    }

    public function renderFailedSideBarContent($post) {
      $reannounceUrl = admin_url(sprintf(
        'admin-ajax.php?action=tr-wc_reannounce&orderId=%s',
        $post->ID
      ));

      ?>
      <ul class="wc-tr-shipment-details">
        <li>
          <p>
            <?php esc_html_e('Het aanmelden van de zending is mislukt. Controleer a.u.b. of de adresgegevens correct zijn en of wij op dit adres bezorgen.') ?>
          </p>
        </li>
      </ul>

      <ul class="wc-tr-shipment-actions failed">
        <li class="action-item">
          <a
            href="<?php echo esc_url($reannounceUrl) ?>"
            class="button button-primary"
            title="<?php esc_attr_e('Hiermee wordt geprobeerd de zending opnieuw aan Trunkrs aan te melden.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>"
          >
            <span class="dashicons dashicons-update-alt"></span>
            <?php esc_html_e('Opnieuw', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
          </a>
        </li>
      </ul>
      <?php
    }

    public function renderSideBarContent($post)
    {
      $trunkrsOrder = new TRUNKRS_WC_Order($post->ID);

      $classes = $trunkrsOrder->isCancelled ? 'failed' : '';

      ?>
      <script type="application/json" id="order_json">
        <?php json_encode($trunkrsOrder->order) ?>
      </script>

      <ul class="wc-tr-shipment-details <?php esc_attr_e($classes) ?>">
        <li>
          <p>
            <b>
              <?php esc_html_e('Trunkrs nummer', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </b>
            :&nbsp;
            <span class="tr-value"><?php esc_html_e($trunkrsOrder->trunkrsNr) ?></span>
          </p>
        </li>

        <li>
          <p>
            <b>
              <?php esc_html_e('Bezorgdatum', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </b>
            :&nbsp;
            <span class="tr-value"><?php esc_html_e($trunkrsOrder->getFormattedDate()) ?></span>
          </p>
        </li>

        <?php
        if ($trunkrsOrder->isCancelled) {
        ?>
        <li>
          <p>
            <b><?php esc_html_e('Geannuleerd', TRUNKRS_WC_Bootstrapper::DOMAIN) ?></b>
            :&nbsp;
            <span class="tr-canceled">
              <?php esc_html_e('Ja', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </span>
          </p>
        </li>
      <?php
      }
      ?>
      </ul>

      <ul class="wc-tr-shipment-actions <?php esc_attr_e($classes) ?>">
        <?php
        if (!$trunkrsOrder->isCancelled) {
          $cancelUrl = admin_url(sprintf(
            'admin-ajax.php?action=tr-wc_cancel&orderId=%s',
            $post->ID
          ));

          $downloadUrl = admin_url(sprintf(
            'admin-ajax.php?action=tr-wc_download-label&trunkrsNr=%s',
            $trunkrsOrder->trunkrsNr
          ));

          ?>
          <li class="action-item">
            <a
              href="<?php echo esc_url($cancelUrl) ?>"
              class="cancel-shipment" title="<?php esc_attr_e('Annuleert de zending op het Trunkrs platform.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>"
            >
              <?php esc_html_e('Zending annuleren', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </a>
          </li>

          <li class="action-item">
            <a
              target="_blank"
              href="<?php echo esc_url($downloadUrl) ?>"
              class="button button-primary"
              title="<?php esc_attr_e('Download het zending label.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>"
            >
              <span class="dashicons dashicons-printer"></span>
              <?php esc_html_e('Label', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>
            </a>
          </li>
          <?php
        } else {
          $reannounceUrl = admin_url(sprintf(
            'admin-ajax.php?action=tr-wc_reannounce&orderId=%s',
            $post->ID
          ));

          ?>
          <li class="action-item">
            <a
              href="<?php echo esc_url($reannounceUrl) ?>"
              class="button button-primary"
              title="<?php esc_attr_e('Hiermee wordt geprobeerd de zending opnieuw aan Trunkrs aan te melden.', TRUNKRS_WC_Bootstrapper::DOMAIN) ?>"
            >
              <span class="dashicons dashicons-update-alt"></span>
              <?php esc_html_e('Opnieuw') ?>
            </a>
          </li>
          <?php
        }
        ?>
      </ul>
      <?php
    }
  }
}

new TRUNKRS_WC_AdminOrderPage();


