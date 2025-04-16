<?php

function getApikeyByCurrency($currency_transaction){

    $atixGateway = new WC_Gateway_Atix();
    if( $currency_transaction == 'USD' ) {
        return $atixGateway->apikey_usd;
    }

    if( $currency_transaction == 'PEN' ) {
        return $atixGateway->apikey;
    }
    throw new Exception("Error currency undefined", 1);
}

function clean_cart_and_clean_session($order_id) {
    // Delete order session.
    WC()->session->set('order_awaiting_payment', null);

    // Clean cart.
    WC()->cart->empty_cart();
    WC()->session->set('cart', array());
}

function redirectPageToUrl($url){
    $url_base = home_url();
    $url_redireccion = $url_base . $url;

    die('<script type="text/javascript">window.location=\''.$url_redireccion.'\';</script>');
    exit;
}

function prepare_email_payment($order, $sent_to_admin, $plain_text, $email) {
    if (!$sent_to_admin && 'customer_completed_order' === $email->id) {
        echo '<p>Gracias por tu compra. Esperamos que disfrutes de tus productos.</p>';
    }
}
add_action('woocommerce_email_order_details', 'prepare_email_payment', 10, 4);