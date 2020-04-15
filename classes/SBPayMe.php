<?php

  /**
   * Renders PayMe payment option for WooCommerce
   *
   * @extends WC_Gateway_BACS
   * @version 1.0.0
   * @author Werner C. Bessinger @ Silverback Dev
   */
  class SBPayMe extends WC_Gateway_BACS {
      /**
       * Class constructor
       * Here we set the payment gateway id, 
       * gateway title and description in the backend, 
       * tab title, supports and a bunch of other stuff
       */
      public function __construct() {

          /* method id */
          $this->id = 'sb-wc-payme';

          /* method title */
          $this->method_title = esc_attr__('PayMe', 'sb-wc-payme');

          /* method description */
          $this->method_description = esc_attr__('PayMe payments.', 'sb-wc-payme');

          /* gateway icon */
          $this->icon = SB_PM_UPSELL_URL . 'images/payme-logo.png';

          /* display frontend title */
          $this->title = $this->get_option('title');
          
          /* define payme qr code link global */
          define('SB_PAYME_QR_URL', $this->get_option('sb_payme_qr_code'));

          /* gateway has fields */
          $this->has_fields = true;

          /* init gateway form fields */
          $this->settings_fields();

          /* load our settings */
          $this->init_settings();

          /* save gateway settings when save settings button clicked IF user is admin */
          if (is_admin()) {
              add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
          }
      }

      /**
       * Set up WooCommerce payment gateway settings fields
       */
      public function settings_fields() {

          $this->form_fields = [
              /* enable/disable */
              'enabled'          => [
                  'title'   => esc_attr__('Enable / Disable', 'sb-wc-payme'),
                  'label'   => esc_attr__('Enable this payment gateway', 'sb-wc-payme'),
                  'type'    => 'checkbox',
                  'default' => 'no',
              ],
              'title'            => [
                  'title'       => __('Title', 'sb-wc-payme'),
                  'type'        => 'text',
                  'description' => __('Add your custom payment method title here if required.', 'sb-wc-payme'),
                  'desc_tip'    => true,
                  'default'     => __('PayMe', 'sb-wc-payme'),
              ],
              'sb_payme_qr_code' => [
                  'title'       => __('PayMe QR Code', 'sb-wc-payme'),
                  'type'        => 'text',
                  'desc_tip'    => true,
                  'description' => __('Add the link to your PayMe QR code here.', 'sb-wc-payme'),
              ],
          ];
      }

      /* render payment fields */
      public function payment_fields() {

          /* nothing to see here */

          /* enqueue styles */
          add_action('wp_enqueue_scripts', [__CLASS__, $this->styles_js()]);
      }

      /* process payment */
      public function process_payment($order_id) {

          /* create new customer order */
          $order = new WC_Order($order_id);

          /* set initial order status */
          $order->update_status('on-hold', __('Awaiting PayMe payment', 'sb-wc-payme'));

          $return_url = $this->get_return_url($order);

          // return result and redirect
          return [
              'result'   => 'success',
              'redirect' => $return_url
          ];
      }

      /* any styles related to front-end display is dropped in here */
      public function styles_js() {
          ?>

          <!-- styles -->
          <style>
            #payment > ul > li.wc_payment_method.payment_method_sb-wc-payme > label > img {
                width: 200px;
                position: relative;
                bottom: 3px;
                left: 5px;
            }
          </style>

          <?php
      }

  }
  