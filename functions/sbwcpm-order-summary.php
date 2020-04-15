<?php
  /* created by Werner C. Bessinger @ Silverback Dev Studios */

  /* prevent direct access */
  if (!defined('ABSPATH')):
      exit;
  endif;

  add_action('wp_head', 'sb_pm_order_complete');
  function sb_pm_order_complete() {

      if (is_order_received_page()):

          /* enqueue front css */
          wp_enqueue_style('payme_css_front', SB_PM_UPSELL_URL . 'assets/front.css', [], null);

          /* get ajax url */
          $ajax_url = admin_url('admin-ajax.php');

          /* get order key */
          $order_key = $_GET['key'];

          /* get order id by key */
          $order_id = wc_get_order_id_by_order_key($order_key);

          /* instantiate order class */
          $order_data = new WC_Order($order_id);

          /* get order payment method */
          $payment_method = $order_data->get_payment_method();

          /* only make alterations to thank you page if payment method is payme */
          if ($payment_method == 'sb-wc-payme'):

              /* empty cart */
              WC()->cart->empty_cart();

              /* get PayMe QR code link */
              $qr_code_link = SB_PAYME_QR_URL;

              $scan_qr_code_text = pll__('<p style="margin-bottom:0px">Scan QR code with Payme</p>');

              /* qr only instructions */
              $instructions = pll__('To finalize payment for this order please scan the QR code provided. After you have completed payment, please click the Payment Completed button.');
              ?>

              <!-- sb_pm_qr_div -->
              <script type="text/javascript">
                    /* <![CDATA[ */

                    jQuery(document).ready(function ($) {

                       var pm_qr_img = '<?php echo $scan_qr_code_text; ?><img id="sb_pm_qr_img" src="<?php echo $qr_code_link; ?>" />';
                       var init_btn = '<button id="sb_pm_aj_send"><?php pll_e('Payment Completed'); ?></button>';
                       var ajax_url = '<?php echo $ajax_url; ?>';

                       var pm_div = '<div id="sb_pm_qr_div">' + pm_qr_img + '<span id="sb_pm_qr_instructions"><?php echo $instructions; ?></span>' + init_btn + '</div>';

                       $(pm_div).prependTo('.woocommerce');

                       //submit payment link input field data and insert value to order meta via ajax
                       $('#sb_pm_aj_send').click(function () {

                          //setup ajax data
                          var data = {
                             'action': 'sbwcpm_add_order_note',
                             'order_id': <?php echo $order_id; ?>
                          };

                          //submit ajax request
                          $.post(
                            ajax_url, data, function (response) {
                               var response = response;
                               window.alert(response.replace("0", ""));
                               location.replace('<?php echo home_url(); ?>');
                            });
                       });
                    });

                    /* ]]> */
              </script>

              <?php
          endif;
      endif;
  }

  /* update order pm payment link via ajax */
  add_action('wp_ajax_sbwcpm_add_order_note', 'sbwcpm_add_order_note');
  function sbwcpm_add_order_note() {

      /* setup $_POST vars */
      $order_id = $_POST['order_id'];

      $order_data = new WC_Order($order_id);

      $order_updated_txt = pll__('Thank you for your payment. We will be in touch soon!');

      if ($order_data):
          $order_data->add_order_note(__('PayMe payment made.'));
          WC()->cart->empty_cart();
          echo $order_updated_txt;
      endif;
  }
  