<?php

function wc_atix_gateway_plugin_links( $links ) {
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=atix_gateway' ) . '">' . __( 'Configure', 'woocommerce-atix' ) . '</a>'
    );
    return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . 'woocommerce-atix.php' ), 'wc_atix_gateway_plugin_links' );
