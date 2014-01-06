<?php
/*
Plugin Name: Easy Digital Downloads - PayPal Website Payments Pro and PayPal Express Gateway
Plugin URL: http://easydigitaldownloads.com/extension/paypal-pro-express
Description: Adds a payment gateway for PayPal Website Payments Pro and PayPal Express Gateway
Version: 1.3.5
Author: Mint Themes
Author URI: http://mintthemes.com
Contributors: benjaminprojas
*/

if ( !defined( 'EPPE_PLUGIN_DIR' ) ) {
  define( 'EPPE_PLUGIN_DIR', dirname( __FILE__ ) );
}

define( 'EDD_EPPE_STORE_API_URL', 'https://easydigitaldownloads.com' );
define( 'EDD_EPPE_PRODUCT_NAME', 'PayPal Pro and PayPal Express' );
define( 'EDD_EPPE_VERSION', '1.3.5' );


// Load the text domain
function eppe_load_textdomain() {

  // Set filter for plugin's languages directory
  $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';


  // Traditional WordPress plugin locale filter
  $locale        = apply_filters( 'plugin_locale',  get_locale(), 'eppe' );
  $mofile        = sprintf( '%1$s-%2$s.mo', 'eppe', $locale );

  // Setup paths to current locale file
  $mofile_local  = $lang_dir . $mofile;
  $mofile_global = WP_LANG_DIR . '/eppe/' . $mofile;

  if ( file_exists( $mofile_global ) ) {
    // Look in global /wp-content/languages/edd-paypal-pro-express folder
    load_textdomain( 'eppe', $mofile_global );
  } elseif ( file_exists( $mofile_local ) ) {
    // Look in local /wp-content/plugins/edd-paypal-pro-express/languages/ folder
    load_textdomain( 'eppe', $mofile_local );
  } else {
    // Load the default language files
    load_plugin_textdomain( 'eppe', false, $lang_dir );
  }

}
add_action( 'init', 'eppe_load_textdomain' );


// registers the gateway
function eppe_register_paypal_pro_express_gateway( $gateways ) {
  // Format: ID => Name
  $gateways['paypalpro'] = array( 'admin_label' => __( 'PayPal Pro', 'eppe' ), 'checkout_label' => __( 'Credit Card', 'eppe' ) );
  $gateways['paypalexpress'] = array( 'admin_label' => __( 'PayPal Express', 'eppe' ), 'checkout_label' => __( 'PayPal', 'eppe' ) );
  return $gateways;
}
add_filter( 'edd_payment_gateways', 'eppe_register_paypal_pro_express_gateway' );

add_action( 'edd_paypalexpress_cc_form', '__return_false' );

// processes the payment
function eppe_pro_process_payment( $purchase_data ) {
  $validate = eppe_validate_post_fields( $purchase_data['post_data'] );
  $parsed_return_query = eppe_parsed_return_query( $purchase_data['card_info'] );
  if ( $validate != true ) {
    edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] . '&' . http_build_query( $parsed_return_query ) );
  }

  global $edd_options;

  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalFunctions.php';
  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalPro.php';

  $credentials = eppe_api_credentials();

  foreach ( $credentials as $cred ) {
    if ( is_null( $cred ) ) {
      edd_set_error( 0, __( 'You must enter your API keys in settings', 'eppe' ) );
      edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] . '&' . http_build_query( $parsed_return_query ) );
    }
  }


  $paypalpro = new PayPalProGateway();

  $data = array(
    'credentials'       => array(
      'api_username'    => $credentials['api_username'],
      'api_password'    => $credentials['api_password'],
      'api_signature'   => $credentials['api_signature']
    ),
    'api_end_point'     => $credentials['api_end_point'],
    'card_data'         => array(
      'number'          => $purchase_data['card_info']['card_number'],
      'exp_month'       => $purchase_data['card_info']['card_exp_month'],
      'exp_year'        => $purchase_data['card_info']['card_exp_year'],
      'cvc'             => $purchase_data['card_info']['card_cvc'],
      'card_type'       => eppe_get_card_type( $purchase_data['card_info']['card_number'] ),
      'first_name'      => $purchase_data['user_info']['first_name'],
      'last_name'       => $purchase_data['user_info']['last_name'],
      'billing_address' => $purchase_data['card_info']['card_address'] . ' ' . $purchase_data['card_info']['card_address_2'],
      'billing_city'    => $purchase_data['card_info']['card_city'],
      'billing_state'   => $purchase_data['card_info']['card_state'],
      'billing_zip'     => $purchase_data['card_info']['card_zip'],
      'billing_country' => $purchase_data['card_info']['card_country'],
      'email'           => $purchase_data['post_data']['edd_email'],
    ),
    'subtotal'          => $purchase_data['subtotal'],
    'discount_amount'   => round( $purchase_data['discount'], 2 ),
    'fees'              => isset( $purchase_data['fees'] ) ? $purchase_data['fees'] : false,
    'tax'               => $purchase_data['tax'],
    'price'             => round( $purchase_data['price'], 2 ),
    'currency_code'     => $edd_options['currency'],
    'api_end_point'     => $credentials['api_end_point'],
    'cart_details'      => $purchase_data['cart_details'],
    'discount'          => $purchase_data['user_info']['discount']
  );

  //echo '<pre>'; print_r( $data ); echo '</pre>'; exit;

  $paypalpro->purchase_data( $data );

  $transaction  = $paypalpro->process_sale();

  $responsecode = strtoupper( $transaction['ACK'] );

  if ( $responsecode == 'SUCCESS' || $responsecode == 'SUCCESSWITHWARNING' || isset( $transaction['TRANSACTIONID'] ) ) {

    // setup the payment details
    $payment_data = array(
      'price'        => $purchase_data['price'],
      'date'         => $purchase_data['date'],
      'user_email'   => $purchase_data['post_data']['edd_email'],
      'purchase_key' => $purchase_data['purchase_key'],
      'currency'     => $edd_options['currency'],
      'downloads'    => $purchase_data['downloads'],
      'cart_details' => $purchase_data['cart_details'],
      'user_info'    => $purchase_data['user_info'],
      'status'       => 'pending'
    );

    // record this payment
    $payment = edd_insert_payment( $payment_data );
    edd_insert_payment_note( $payment, 'PayPal Pro Transaction ID: ' . $transaction['TRANSACTIONID'] );

    // complete the purchase
    edd_update_payment_status( $payment, 'publish' );
    edd_empty_cart();
    edd_send_to_success_page(); // this function redirects and exits itself

  } else {
    foreach ( $transaction as $key => $value ) {
      if ( substr( $key, 0, 11 ) == 'L_ERRORCODE' ) {
        $errorCode = substr( $key, 11 );
        $value = $transaction['L_ERRORCODE' . $errorCode];
        edd_set_error( $value, $transaction['L_SHORTMESSAGE' . $errorCode] . ' ' . $transaction['L_LONGMESSAGE' . $errorCode] );
        edd_record_gateway_error( __( 'PayPal Pro Error', 'edd' ), sprintf( __( 'PayPal Pro returned an error while processing a payment. Details: %s', 'eppe' ), json_encode( $transaction ) ) );
      }
    }
    edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] . '&' . http_build_query( $parsed_return_query ) );
  }

}
add_action( 'edd_gateway_paypalpro', 'eppe_pro_process_payment' );

function eppe_exp_process_payment( $purchase_data ) {
  global $edd_options;

  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalFunctions.php';
  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalExpress.php';

  $credentials = eppe_api_credentials();
  foreach ( $credentials as $cred ) {
    if ( is_null( $cred ) ) {
      edd_set_error( 0, __( 'You must enter your API keys in settings', 'eppe' ) );
      edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
    }
  }

  $paypalexpress = new PayPalExpressGateway();

  $return_url = get_permalink( $edd_options['success_page'] ) . '?payment-confirmation=paypalexpress';
  $cancel_url = function_exists( 'edd_get_failed_transaction_uri' ) ? edd_get_failed_transaction_uri() : get_permalink( $edd_options['purchase_page'] );

  $payment_data = array(
    'price'        => $purchase_data['price'],
    'date'         => $purchase_data['date'],
    'user_email'   => $purchase_data['user_email'],
    'purchase_key' => $purchase_data['purchase_key'],
    'currency'     => $edd_options['currency'],
    'downloads'    => $purchase_data['downloads'],
    'cart_details' => $purchase_data['cart_details'],
    'user_info'    => $purchase_data['user_info'],
    'status'       => 'pending'
  );

  // record the pending payment
  $payment     = edd_insert_payment( $payment_data );

  $paypal_data = array(
    'credentials'     => array(
      'api_username'  => $credentials['api_username'],
      'api_password'  => $credentials['api_password'],
      'api_signature' => $credentials['api_signature']
    ),
    'api_end_point'   => $credentials['api_end_point'],
    'urls'            => array(
      'return_url'    => $return_url,
      'cancel_url'    => $cancel_url
    ),
    'subtotal'        => $purchase_data['subtotal'],
    'discount_amount' => round( $purchase_data['discount'], 2 ),
    'fees'            => isset( $purchase_data['fees'] ) ? $purchase_data['fees'] : false,
    'tax'             => $purchase_data['tax'],
    'price'           => round( $purchase_data['price'], 2 ),
    'currency_code'   => $edd_options['currency'],
    'cart_details'    => $purchase_data['cart_details'],
    'payment_id'      => $payment,
    'first_name'      => $purchase_data['user_info']['first_name'],
    'last_name'       => $purchase_data['user_info']['last_name'],
    'discount'        => $purchase_data['user_info']['discount']
  );

  $paypalexpress->purchase_data( $paypal_data );

  $token = $paypalexpress->retrieve_token();

  $responsecode = strtoupper( $token['ACK'] );

  if ( $responsecode == 'SUCCESS' || $responsecode == 'SUCCESSWITHWARNING' ) {

    add_post_meta( $payment, '_edd_ppe_token', $token['TOKEN'] );

    $express_url = $credentials['express_checkout_url'] . urlencode( $token['TOKEN'] );
    wp_redirect( $express_url );
    exit;

  } else {

    // get rid of the pending purchase
    edd_update_payment_status( $payment, 'failed' );

    foreach ( $token as $key => $value ) {
      if ( substr( $key, 0, 11 ) == 'L_ERRORCODE' ) {
        $error_code = substr( $key, 11 );
        $value = $token['L_ERRORCODE' . $error_code];
        edd_set_error( $value, $token['L_SHORTMESSAGE' . $error_code] . ' ' . $token['L_LONGMESSAGE' . $error_code] );
      }
    }
    edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
  }

}
add_action( 'edd_gateway_paypalexpress', 'eppe_exp_process_payment' );

function eppe_exp_payment_confirm( $content ) {
  // $content is the normal page content
  // reset it to your form and whatever confirm text you need

  global $edd_options;
  $token = ( isset( $_GET['token'] ) ) ? $_GET['token'] : '';
  $payer_id = ( isset( $_GET['PayerID'] ) ) ? $_GET['PayerID'] : '';
  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalFunctions.php';
  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalExpress.php';
  $paypalexpress = new PayPalExpressGateway();

  $credentials = eppe_api_credentials();
  foreach ( $credentials as $cred ) {
    if ( is_null( $cred ) ) {
      edd_set_error( 0, __( 'You must enter your API keys in settings', 'eppe' ) );
      edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
    }
  }

  $paypalexpress->purchase_data( array(
    'credentials'     => array(
      'api_username'  => $credentials['api_username'],
      'api_password'  => $credentials['api_password'],
      'api_signature' => $credentials['api_signature']
    ),
    'api_end_point'   => $credentials['api_end_point']
  ) );

  $details = $paypalexpress->express_checkout_details( $token );
  if ( !did_action('wp_head') ) {
    return $content;
  } else {
    if ( isset( $_POST['confirmation'] ) && $_POST['confirmation'] == 'yes' ) {

      $sale = $paypalexpress->express_checkout( $token, $payer_id, $details['AMT'], $details['ITEMAMT'], $details['TAXAMT'], $details['CURRENCYCODE'] );

      $payment_id = $paypalexpress->get_purchase_id_by_token( $sale['TOKEN'] );

      if ( is_array( $sale ) && trim( $sale['ACK'] ) == 'Success' ) {
        edd_update_payment_status( $payment_id, 'publish' );
        edd_insert_payment_note( $payment_id, 'PayPal Express Transaction ID: ' . $sale['PAYMENTINFO_0_TRANSACTIONID'] );
        edd_empty_cart();
        return $content;

      } else {

        return $sale['L_LONGMESSAGE0'];

      }

    } else {
      ob_start(); ?>
        <p><?php _e( 'Please confirm your payment', 'eppe' ); ?></p>
        <div id="billing_info">
          <h1><strong><?php _e( 'Billing Information', 'eppe' ); ?></strong></h1>
          <p><strong><?php echo $details['FIRSTNAME'] ?> <?php echo $details['LASTNAME'] ?></strong><br />
          <?php _e( 'PayPal Status:', 'eppe' ); ?> <?php echo $details['PAYERSTATUS'] ?><br />
          <?php if ( isset( $details['PHONENUM'] ) ): ?>
            <?php _e( 'Phone:', 'eppe' ); ?> <?php echo $details['PHONENUM'] ?><br />
          <?php endif; ?>
          <?php _e( 'Email:', 'eppe' ); ?> <?php echo $details['EMAIL'] ?></p>
        </div>
        <table id="order_summary">
          <thead>
            <tr>
              <th><?php _e( 'Item Name', 'eppe' ); ?></th>
              <th><?php _e( 'Item Price', 'eppe' ); ?></th>
            </tr>
          </thead>
          <tfoot>
            <?php if( ! empty( $details['TAXAMT'] ) ) { ?>
            <tr>
              <th colspan="2" class="edd_cart_tax"><?php _e( 'Tax:', 'eppe' ); ?> <span class="edd_cart_tax_amount"><?php echo edd_currency_filter( edd_format_amount( $details['TAXAMT'] ) ); ?></span></th>
            </tr>
            <?php } ?>
            <tr>
              <th colspan="2" class="edd_cart_total"><?php _e( 'Total:', 'eppe' ); ?> <span class="edd_cart_amount"><?php echo edd_currency_filter( edd_format_amount( $details['AMT'] ) ); ?></span></th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            foreach ( $details as $key => $value ) {
              if ( substr( $key, 0, 23 ) == 'L_PAYMENTREQUEST_0_NAME' ) {
                $number = substr( $key, 23 );
                echo '<tr><td>' . $details['L_PAYMENTREQUEST_0_NAME' . $number] . '</td>';
                echo '<td>' . edd_currency_filter( $details['L_PAYMENTREQUEST_0_AMT' . $number] ) . '<td></tr>';
              }
            }
            ?>
          </tbody>
        </table>

        <form action="" method="post" id="edd-paypal-express-confirm">
          <input type="hidden" name="confirmation" value="yes" />
          <input type="submit" value="<?php _e( 'Confirm', 'eppe' ); ?>" />
       </form>
       <?php
      return ob_get_clean();
    }
  }
}
add_filter( 'edd_payment_confirm_paypalexpress', 'eppe_exp_payment_confirm' );


// mark a payment as failed if a user cancels from in PayPal
function eppe_failed_payment() {

  global $edd_options;

  if( is_admin() )
    return;

  if( ! is_page( $edd_options['failure_page'] ) )
    return;

  if( ! isset( $_GET['token'] ) )
    return;

  $token = ( isset( $_GET['token'] ) ) ? urldecode( $_GET['token'] ) : '';

  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalFunctions.php';
  require_once EPPE_PLUGIN_DIR . '/paypal/PayPalExpress.php';

  $paypalexpress = new PayPalExpressGateway();

  $payment_id = $paypalexpress->get_purchase_id_by_token( $token );

  $status     = get_post_field( 'post_status', $payment_id );

  if( $status != 'pending' )
    return;

  edd_update_payment_status( $payment_id, 'failed' );

  if( function_exists( 'edd_insert_payment_note' ) )
    edd_insert_payment_note( $payment_id, __( 'The user cancelled payment after going to PayPal', 'eppe' ) );

  edd_empty_cart();

}
add_action( 'template_redirect', 'eppe_failed_payment' );


// adds the settings to the Payment Gateways section
function eppe_add_settings( $settings ) {

  $eppe_settings = array(
    array(
      'id' => 'paypal_pro_express_settings',
      'name' => '<strong>' . __( 'PayPal Website Payment Pro and PayPal Express API Keys', 'eppe' ) . '</strong>',
      'desc' => __( 'Configure your PayPal Pro and PayPal Express settings', 'eppe' ),
      'type' => 'header'
    ),
    array(
      'id' => 'eppe_license_key',
      'name' => __( 'PayPal Pro / Express License Key', 'eppe' ),
      'desc' => __( 'Enter the license key you received with your purchase in order to get automatic updates', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'live_paypal_api_username',
      'name' => __( 'Live API Username', 'eppe' ),
      'desc' => __( 'Enter your live API username', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'live_paypal_api_password',
      'name' => __( 'Live API Password', 'eppe' ),
      'desc' => __( 'Enter your live API password', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'live_paypal_api_signature',
      'name' => __( 'Live API Signature', 'eppe' ),
      'desc' => __( 'Enter your live API signature', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'test_paypal_api_username',
      'name' => __( 'Test API Username', 'eppe' ),
      'desc' => __( 'Enter your test API username', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'test_paypal_api_password',
      'name' => __( 'Test API Password', 'eppe' ),
      'desc' => __( 'Enter your test API password', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    ),
    array(
      'id' => 'test_paypal_api_signature',
      'name' => __( 'Test API Signature', 'eppe' ),
      'desc' => __( 'Enter your test API signature', 'eppe' ),
      'type' => 'text',
      'size' => 'regular'
    )
  );

  return array_merge( $settings, $eppe_settings );
}
add_filter( 'edd_settings_gateways', 'eppe_add_settings' );

function eppe_api_credentials() {
  global $edd_options;

  if ( edd_is_test_mode() ) {
    $api_username         = isset( $edd_options['test_paypal_api_username'] ) ? $edd_options['test_paypal_api_username'] : null;
    $api_password         = isset( $edd_options['test_paypal_api_password'] ) ? $edd_options['test_paypal_api_password'] : null;
    $api_signature        = isset( $edd_options['test_paypal_api_signature'] ) ? $edd_options['test_paypal_api_signature'] : null;
    $api_end_point        = 'https://api-3t.sandbox.paypal.com/nvp';
    $express_checkout_url = 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=';
  } else {
    $api_username         = isset( $edd_options['live_paypal_api_username'] ) ? $edd_options['live_paypal_api_username'] : null;
    $api_password         = isset( $edd_options['live_paypal_api_password'] ) ? $edd_options['live_paypal_api_password'] : null;
    $api_signature        = isset( $edd_options['live_paypal_api_signature'] ) ? $edd_options['live_paypal_api_signature'] : null;
    $api_end_point        = 'https://api-3t.paypal.com/nvp';
    $express_checkout_url = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=';
  }
  $data = array(
    'api_username'        => $api_username,
    'api_password'        => $api_password,
    'api_signature'       => $api_signature,
    'api_end_point'       => $api_end_point,
    'express_checkout_url'=> $express_checkout_url,
  );
  return $data;
}
function eppe_parsed_return_query( $post_data ) {
  $post_data = array(
    'billing_address'   => $post_data['card_address'],
    'billing_address_2' => $post_data['card_address_2'],
    'billing_city'      => $post_data['card_city'],
    'billing_country'   => $post_data['card_country'],
    'billing_zip'       => $post_data['card_zip'],
    'card_cvc'          => $post_data['card_cvc'],
    'card_exp_month'    => $post_data['card_exp_month'],
    'card_exp_year'     => $post_data['card_exp_year'],
  );
  $post_data = array_filter( $post_data );
  return $post_data;
}
function eppe_validate_post_fields( $purchase_data ) {
  $validate = true;
  $number = 0;
  foreach ( $purchase_data as $k => $v ) {
    if ( $v == '' ) {
      switch ( $k ) {
        case 'card_address':
          $k = __( 'Billing Address', 'eppe' );
          break;
        case 'card_city':
          $k = __( 'Billing City', 'eppe' );
          break;
        case 'card_zip':
          $k = __( 'Billing Zip', 'eppe' );
          break;
        case 'card_number':
          $k = __( 'Credit Card Number', 'eppe' );
          break;
        case 'card_cvc':
          $k = __( 'CVC Code', 'eppe' );
          break;
        case 'card_exp_month':
          $k = __( 'Credit Card Expiration Month', 'eppe' );
          break;
        case 'card_exp_year':
          $k = __( 'Credit Card Expiration Year', 'eppe' );
          break;
        default:
          $k = false;
          break;
      }
      if ( $k != false ) {
        edd_set_error( $number, __( "Invalid $k", 'eppe' ) );
        $validate = false;
        $number++;
      }
    }
  }
  return $validate;
}


function eppe_get_card_type( $card_number ) {

  /*
  * mastercard: Must have a prefix of 51 to 55, and must be 16 digits in length.
  * Visa: Must have a prefix of 4, and must be either 13 or 16 digits in length.
  * American Express: Must have a prefix of 34 or 37, and must be 15 digits in length.
  * Discover: Must have a prefix of 6011, and must be 16 digits in length.
  */

  if ( preg_match( "/^5[1-5][0-9]{14}$/", $card_number ) )
    return "mastercard";

  if ( preg_match( "/^4[0-9]{12}([0-9]{3})?$/", $card_number ) )
    return "visa";

  if ( preg_match( "/^3[47][0-9]{13}$/", $card_number ) )
    return "amex";

  if ( preg_match( "/^6011[0-9]{12}$/", $card_number ) )
    return "discover";
}


function eppe_activate_license() {
  global $edd_options;

  if ( ! isset( $_POST['edd_settings_gateways'] ) )
    return;
  if ( ! isset( $_POST['edd_settings_gateways']['eppe_license_key'] ) )
    return;

  if ( get_option( 'eppe_license_key_active' ) == 'valid' )
    return;

  $license = sanitize_text_field( $_POST['edd_settings_gateways']['eppe_license_key'] );

  // data to send in our API request
  $api_params = array(
    'edd_action'=> 'activate_license',
    'license'   => $license,
    'item_name' => urlencode( EDD_EPPE_PRODUCT_NAME ) // the name of our product in EDD
  );

  // Call the custom API.
  $response = wp_remote_get( add_query_arg( $api_params, EDD_EPPE_STORE_API_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

  // make sure the response came back okay
  if ( is_wp_error( $response ) )
    return false;

  // decode the license data
  $license_data = json_decode( wp_remote_retrieve_body( $response ) );

  update_option( 'eppe_license_key_active', $license_data->license );

}
add_action( 'admin_init', 'eppe_activate_license' );


function eppe_updater() {

  if ( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
    // load our custom updater
    include dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';
  }


  global $edd_options;

  // retrieve our license key from the DB
  $eppe_license_key = isset( $edd_options['eppe_license_key'] ) ? trim( $edd_options['eppe_license_key'] ) : '';

  // setup the updater
  $edd_stripe_updater = new EDD_SL_Plugin_Updater( EDD_EPPE_STORE_API_URL, __FILE__, array(
      'version'   => EDD_EPPE_VERSION,   // current version number
      'license'   => $eppe_license_key, // license key (used get_option above to retrieve from DB)
      'item_name' => EDD_EPPE_PRODUCT_NAME, // name of this plugin
      'author'    => 'Mint Themes'  // author of this plugin
    )
  );
}
add_action( 'admin_init', 'eppe_updater' );