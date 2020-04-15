<?php

  /* created by Werner C. Bessinger @ Silverback Dev Studios */

  /* prevent direct access */
  if (!defined('ABSPATH')):
      exit;
  endif;

function sbwcpm_filter_gateway($available_gateways) {

      if (is_admin()):
          return $available_gateways;
      endif;

      /* get user billing country */
      $billing_country = WC()->customer->get_billing_country();

      /* iDEAL conditional display (NL only) */
      if (isset($available_gateways['sb-wc-payme']) && $billing_country != 'HK') :
          unset($available_gateways['sb-wc-payme']);
      endif;
 
      return $available_gateways;
  }
  
  add_filter('woocommerce_available_payment_gateways', 'sbwcpm_filter_gateway');