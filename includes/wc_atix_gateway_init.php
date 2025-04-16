<?php

function wc_atix_gateway_init() {
    add_filter( 'query_vars', function ( $vars ) {
        $vars[] = 'atix_redirect';
        return $vars;
    } );

    /* EXTENDS MAIN WOOCOMMERCE PAYMENT GATEWAY CLASS */
    class WC_Gateway_Atix extends WC_Payment_Gateway {
        public $instructions;
        public $testmode;
        public $apikey;
        public $apikey_usd;
        public $securityKey;
        public $functionmode;
        public $apiurl;
        public $finalStatus;
        public $sectionNameUrl;
        public $enableWallets;

        /** CONSTRUCTOR OF THIS CLASS **/
        public function __construct() {
            $this->id                 = 'atix_gateway';
            $this->has_fields         = true;
            $this->method_title       = __( 'Atix Payment Gateway', 'woocommerce-atix' );
            $this->method_description = __( 'Allows payments via payment gateway for Atix Payment Services.', 'woocommerce-atix' );
            $this->supports = array( 'products' );

            $this->init_form_fields();
            $this->init_settings();

            $this->enabled      = $this->get_option( 'enabled' );
            $this->title        = '';
            $this->description  = $this->get_option( 'description' );
            $this->instructions = '';
            $this->testmode     = 'yes' === $this->get_option( 'testmode' );
            $this->apikey       = $this->get_option( 'apikey' );
            $this->apikey_usd  = $this->get_option( 'apikey_usd' );
            $this->securityKey  = $this->get_option( 'security_key' );
            $this->functionmode = 'redirect';
            $this->apiurl = '';
            $this->finalStatus  = $this->get_option( 'final_status' );
            $this->sectionNameUrl  = $this->get_option( 'section_name_url' );
            $this->enableWallets  = $this->get_option( 'cash_wallets' );

            $this->load_hooks();
            if ( $this->testmode ) {
                $this->apiurl = BASE_URL_SANDBOX .'/GBCPE_Payment/';
            }
            else {
                $this->apiurl = BASE_URL_PROD .'/GBCPE_Payment/';
            }

            if ( $this->enableWallets == 'yes' ) {
                $this->icon = plugins_url('../assets/images/billeteras.png', __FILE__);
            }else{
                $this->icon = plugins_url('../assets/images/logos-credit-debit-cards-small.png', __FILE__);
            }
            
            // Actions
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ));
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            add_action( 'woocommerce_api_atix', array( $this, 'webhook' ) );
            // Customer Emails
            add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
        }


        /* MAIN GENERAL cURL REQUEST */
        public function api_curl_request($payload, $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-KEY: '.$this->apikey,
                'Content-Type: application/json'
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }


        public function api_get_token_info($token){
            $payload_data = '{
                "Tokenid": ["'.$token.'"]
            }';
            $result = $this->api_curl_request($payload_data, 'https://gateway.atix.com.pe/payment/v1/api/ResultTransactionByTokenId');
            $response = json_decode($result, true);
            return $response;
        }

        /* FIELDS TO CONFIGURE Atix PAYMENT SERVICES */
        public function init_form_fields() {
            $this->form_fields = apply_filters( 'wc_atix_form_fields', array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce-atix' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Atix Payment Services Gateway', 'woocommerce-atix' ),
                    'default' => 'yes'
                ),
                'description' => array(
                    'title'       => __( 'Description', 'woocommerce-atix' ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce-atix' ),
                    'default'     => __( 'When you confirm your purchase, we will redirect you to Atix site for secure payment.', 'woocommerce-atix' ),
                    'desc_tip'    => true,
                ),
                'apikey' => array(
                    'title'       => __( 'API Key PEN', 'woocommerce-atix' ),
                    'type'        => 'textarea',
                    'description' => __( 'Site unique APIKey PEN, it will validate and create the neccesary token for process the purchase.', 'woocommerce-atix' ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'apikey_usd' => array(
                    'title'       => __( 'API Key USD', 'woocommerce-atix' ),
                    'type'        => 'textarea',
                    'description' => __( 'Site unique APIKey USD, it will validate and create the neccesary token for process the purchase.', 'woocommerce-atix' ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'security_key' => array(
                    'title'       => __( 'Security key', 'woocommerce-atix' ),
                    'type'        => 'textarea',
                    'description' => __( 'Site unique Key, it will validate for webhook payment notificactions.', 'woocommerce-atix' ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'section_name_url' => array(
                    'title'       => __( 'Pay confirmation url', 'woocommerce-atix' ),
                    'type'        => 'text',
                    'description' => __( 'Enter the name of your checkout page, example: yourdomain/checkout/order-received/123/?key=wc_order_SwrcT2Wpokxz1  by default it will be "checkout".', 'woocommerce-atix' ),
                    'default'     => 'checkout',
                    'desc_tip'    => false,
                ),
                'final_status' => array(
                    'title'       => __('Final status', 'woocommerce-atix'),
                    'label'       => '',
                    'type'        => 'select',
                    'description' => __('Select the final state in which you want the transaction to end.', 'woocommerce-atix'),
                    'default'     => 'completed',
                    'desc_tip'    => true,
                    'options'     => array(
                        'completed' => __('completed', 'woocommerce-atix'),
                        'processing' => __('processing', 'woocommerce-atix'),
                    ),
                ),
                'cash_wallets' => array(
                    'title'       => __('Cash/Wallets', 'woocommerce-atix'),
                    'label'       => __('Cash and wallet payments enabled', 'woocommerce-atix'),
                    'type'        => 'checkbox',
                    'description' => __('Check the box to indicate that you have enabled cash and digital wallet payments.', 'woocommerce-atix'),
                    'default'     => 'no',
                    'desc_tip'    => true,
                ),
                'testmode' => array(
                    'title'       => __('Test Mode', 'woocommerce-atix'),
                    'label'       => __('Enable Test Mode', 'woocommerce-atix'),
                    'type'        => 'checkbox',
                    'description' => __('Select the box to put the payment gateway into test mode', 'woocommerce-atix'),
                    'default'     => 'yes',
                    'desc_tip'    => true,
                )
            ) );
        }


        /* ATIX PAYMENT GATEWAY MAIN SCRIPTS */
        public function payment_scripts() {
            wp_enqueue_script( 'woo_atix' );
            wp_enqueue_script( 'woo_atix_btn_sandbox' );
            wp_enqueue_script( 'woo_atix_btn_production' );
        }


        public function load_hooks()
        {
            add_action(
                'woocommerce_receipt_' . $this->id,
                function ($order) {
                    $this->render_order_form($order);
                }
            );
        }

        /*** Output for the order received page. ***/
        public function thankyou_page() {
            if(empty($_GET['token']) ) {
                if ($this->instructions) {
                    echo wpautop(wptexturize($this->instructions));
                }
            }
        }



        /*** Add content to the WC emails. **/
        public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
            if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
        }


        /*** Process the payment and return the result ***/
        public function process_payment( $order_id ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_reporting( E_ALL );
                ini_set( 'display_errors', 1 );
            }
            global $woocommerce;
            $order = wc_get_order($order_id);
            

            if($this->functionmode=='redirect') {
                return array(
                    'result' => 'success',
                    'redirect' => add_query_arg(
                        array(
                            'atix_redirect' => 'yes'
                        ),
                        $order->get_checkout_payment_url(true)
                    ),
                );
            }
        }

        public function webhook() {
            $order = wc_get_order( $_GET['id'] );
            $order->payment_complete();
            // $order->reduce_order_stock();
            wc_reduce_stock_levels($order);
            update_option('webhook_debug', $_GET);
        }

        public function render_order_form( $order_id ) {
            $isLacpsRedirect = get_query_var('atix_redirect');

            if ( $isLacpsRedirect == 'yes' ) {
                /**
                 * WooCommerce Order
                 *
                 * @var WC_Order $order
                 */
                $order = wc_get_order( $order_id );

                $global_currency = get_woocommerce_currency();
                $current_currency = $order->get_currency() != $global_currency 
                    ? $order->get_currency() 
                    : $global_currency;

                $_SESSION['orderId'] = (String)$order_id;

                $this->apikey = getApikeyByCurrency($current_currency);
                
                $ip_addr = WC_Geolocation::get_ip_address();
                $location = WC_Geolocation::geolocate_ip();
                $country = $location['country'];
                $navegador = get_browser(null, true);
                $email = $order->billing_email;
                $phone = $order->billing_phone;

                $array_xuser = '{"currency": "' . get_woocommerce_currency() . '", "country": "' . $country . '", "totalamount":' . $order->get_total() . ', "reference": "' . $order_id . '", "email": "' . $email . '", "phone": "' . $phone . '", "urlorigi": "' . home_url('/') . '", "mobile": "", "typeconection": "", "protocol": "", "navigator": "' . $navegador['browser'] . '"}';
                $array_xuser = str_replace(' ', '', $array_xuser);


                $array_data = json_encode($array_xuser);
                $payload_data = '{
                    "User": "wzzzGE38zPk5pUKWd7jhN",
                    "Password": "YkSzED4ty92BjMa2SXYsF",
                    "Apikey": "'. $this->apikey .'",
                    "Version": "V1.1",
                    "Data": ' . $array_data . '
                }';
                
                $debug = $this->testmode;
                // Por defecto será la url de SANDBOX
                $url =  BASE_URL_SANDBOX . '/GBCPE_AuthenticateUser';
                // Si es producción la url será la siguiente
                if(!$debug){
                    $url =  BASE_URL_PROD . '/GBCPE_AuthenticateUser';
                } 
                
                $response = wp_remote_post( $url, array(
                    'body'    => $payload_data,
                ));  
                $result  = json_decode( wp_remote_retrieve_body( $response ), true );

                header('Location: '.$result[0]['Url']);

            }
        }

    } // end \WC_Gateway_Atix class

}

add_action( 'plugins_loaded', 'wc_atix_gateway_init' );


/* --------------------------------------------------------------
    ADD Atix PAYMENT GATEWAY TO WOOCOMMERCE PAYMENT GATEWAYS
-------------------------------------------------------------- */
function wc_atix_add_to_gateways( $gateways ) {
    $gateways[] = 'WC_Gateway_Atix';
    return $gateways;
}

add_filter( 'woocommerce_payment_gateways', 'wc_atix_add_to_gateways' );