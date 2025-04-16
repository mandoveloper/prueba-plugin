<?php
add_filter( 'woocommerce_gateway_title', 'custom_atix_gateway_title', 10, 2 );
function custom_atix_gateway_title( $title, $payment_id ) {
    if ( $payment_id === 'atix_gateway' ) {
        // Mostrar logo solo en el checkout
        if ( is_checkout() && ! is_order_received_page() && ! is_admin() ) {
            $img = '<img src="' . plugins_url( '../assets/images/logoatix.svg', __FILE__ ) . '" alt="Atix" style="height: 20px; vertical-align: middle; margin-right: 8px; display: inline;" />';
            return '<span style="display: inline-flex">' . $img . '</span>';
        } else {
            return __('Payment Service Atix', 'woocommerce-atix');
        }
    }
    return $title;
}