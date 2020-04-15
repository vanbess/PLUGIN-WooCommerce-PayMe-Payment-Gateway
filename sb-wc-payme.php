<?php

  /*
   * Plugin Name: Silverback WooCommerce PayMe Payment Gateway
   * Plugin URI: https://silverbackdev.co.za
   * Description: Adds PayMe/HSBC Hong Kong to WooCommerce as a payment option.
   * Author: Werner C. Bessinger
   * Version: 1.0.0
   * Author URI: https://silverbackdev.co.za
   */

  /* PREVENT DIRECT ACCESS */
  if (!defined('ABSPATH')):
      exit;
  endif;
  // plugin init
  function sb_payme_init() {

      // define plugin path constants
      define('SB_PM_UPSELL_PATH', plugin_dir_path(__FILE__));
      define('SB_PM_UPSELL_URL', plugin_dir_url(__FILE__));
      define('SB_PM_UPSELL_VERSION', '1.0.0');

      /* check of WooCommerce payment gateway class exists before doing anything else */
      if (!class_exists('WC_Payment_Gateway')):
          return;
      endif;
      
            
      
      /* conditional display of PayMe for Hong Kong only */
      require SB_PM_UPSELL_PATH.'functions/sbwcpm-conditional-display.php';
      
      /* thank you page edits */
      require SB_PM_UPSELL_PATH.'functions/sbwcpm-order-summary.php';
      
      /* include core SB Adyen classes */
      require SB_PM_UPSELL_PATH . 'classes/SBPayMe.php';

      /* add each core SB Adyen class to WooCommerce */
      function register_sb_payme_gateway($methods) {
          $methods[] = 'SBPayMe';
          return $methods;
      }

      add_filter('woocommerce_payment_gateways', 'register_sb_payme_gateway');

      /* add custom SB Adyen WooCommerce payment gateways settings link */
      function sb_payme_settings_link($links) {
          $plugin_links = [
              '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . esc_attr__('Settings', 'sb-wc-payme') . '</a>',
          ];

          return array_merge($plugin_links, $links);
      }

      add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sb_payme_settings_link');
  }

  add_action('plugins_loaded', 'sb_payme_init', 0);
  