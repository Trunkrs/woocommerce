<?php

if (!class_exists('WC_TRUNKRS_AdminOrderPage')) {
  class WC_TRUNKRS_AdminOrderPage
  {
    public function __construct()
    {
      add_action('add_meta_boxes_shop_order', [$this, 'renderTrunkrsBox']);
    }

    public function renderTrunkrsBox($post)
    {
      $trunkrsOrder = new TR_WC_Order($post->ID);

      if (!$trunkrsOrder->isTrunkrsOrder)
        return;

      $logoImg = sprintf(
        '<img class="tr-wc-admin-small-logo" alt="Trunkrs logo" src="%s" />',
        WC_TRUNKRS_Utils::createAssetUrl('icons/trunkrs-small-indigo.svg')
      );

      if ($trunkrsOrder->isAnnounceFailed && !$trunkrsOrder->isCancelled) {
        add_meta_box(
          'wc_tr_shipment_side_box',
          $logoImg . __('Shipment details'),
          [$this, 'renderFailedSideBarContent'],
          'shop_order',
          'side',
          'default'
        );
        return;
      }

      add_meta_box(
        'wc_tr_shipment_side_box',
         $logoImg . __('Shipment details'),
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
            <?php echo __('The announcement for this shipment has failed. Please check whether the address details are correct and that we deliver to this address.') ?>
          </p>
        </li>
      </ul>

      <ul class="wc-tr-shipment-actions failed">
        <?php
        echo sprintf(
          '<li class="action-item">
                    <a href="%1s" class="button button-primary" title="%3$s">
                        <span class="dashicons dashicons-update-alt"></span>
                        %2$s
                    </a>
                 </li>',
          $reannounceUrl,
          __('Re-try'),
          __('This will try to announce the shipment again to Trunkrs.')
        );
        ?>
      </ul>
      <?php
    }

    public function renderSideBarContent($post)
    {
      $trunkrsOrder = new TR_WC_Order($post->ID);

      $classes = $trunkrsOrder->isCancelled ? 'failed' : '';

      ?>
      <ul class="wc-tr-shipment-details <?php echo $classes ?>">
        <li>
          <p>
            <?php
              echo sprintf(
                '<b>%s</b>: <span class="tr-value">%s</span>',
                __('Trunkrs number'),
                $trunkrsOrder->trunkrsNr
              )
            ?>
          </p>
        </li>

        <li>
          <p>
            <?php
            echo sprintf(
              '<b>%s</b>: <span class="tr-value">%s</span>',
                __('Delivery date'),
                $trunkrsOrder->getFormattedDate()
              )
            ?>
          </p>
        </li>

        <?php
          if ($trunkrsOrder->isCancelled) {
            echo sprintf(
              '<li><p><b>%s</b>: <span class="tr-canceled">%s</span></p></li>',
              __('Canceled'),
              __('Yes')
            );
          }
        ?>
      </ul>

      <ul class="wc-tr-shipment-actions <?php echo $classes ?>">
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

          echo sprintf(
            '<li class="action-item">
                    <a href="%1s" class="cancel-shipment" title="%3$s">
                        %2$s
                    </a>
                 </li>',
            $cancelUrl,
            __('Cancel shipment'),
            __('Cancels the shipment on the Trunkrs platform.')
          );

          echo sprintf(
            '<li class="action-item">
                    <a href="%1s" target="_blank" class="button button-primary" title="%3$s">
                        <span class="dashicons dashicons-printer"></span>
                        %2$s
                    </a>
                 </li>',
            $downloadUrl,
            __('Label'),
            __('Download the shipment label.')
          );
        } else {
          $reannounceUrl = admin_url(sprintf(
            'admin-ajax.php?action=tr-wc_reannounce&orderId=%s',
            $post->ID
          ));

          echo sprintf(
            '<li class="action-item">
                    <a href="%1s" class="button button-primary" title="%3$s">
                        <span class="dashicons dashicons-update-alt"></span>
                        %2$s
                    </a>
                 </li>',
            $reannounceUrl,
            __('Re-create'),
            __('This will try to announce the shipment again to Trunkrs.')
          );
        }
        ?>
      </ul>
      <?php
    }
  }
}

new WC_TRUNKRS_AdminOrderPage();


