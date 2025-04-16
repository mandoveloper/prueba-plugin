<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Gateway_Atix_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'atix_gateway';// your payment gateway name

    public function initialize() {
        $this->settings = get_option( 'woocommerce_atix_gateway_settings', [] );
        $this->gateway = new WC_Gateway_Atix();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        wp_register_script(
            'wc-atix-gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'js/checkout.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );
        if( function_exists( 'wp_set_script_translations' ) ) {            
            wp_set_script_translations( 'wc-atix-gateway-blocks-integration', 'woocommerce-atix', plugin_dir_url(__FILE__) . 'languages/');
            
        }
        return [ 'wc-atix-gateway-blocks-integration' ];
    }

    public function get_payment_method_data() {
        return [
            'title' => __('Pay with credit or debit card', 'woocommerce-atix'),
            'description' => __('When you confirm your purchase, we will redirect you to Atix site for secure payment.', 'woocommerce-atix'),
            'icon' => plugins_url('../assets/images/logos-credit-debit-cards-small.png', __FILE__)
        ];
    }

}
?>