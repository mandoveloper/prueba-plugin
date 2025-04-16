<?php
//Create dinamic route
function custom_page_rewrite_rule() {
    add_rewrite_rule('^atixpaymentgateway/confirmation/?', 'index.php?custom_page=1', 'top');
    add_rewrite_rule('^atixpaymentgateway/order-confirmation/?', 'index.php?custom_page=2', 'top');
}
add_action('init', 'custom_page_rewrite_rule');

//Add query param
function custom_page_query_vars($vars) {
    $vars[] = 'custom_page';
    return $vars;
}
add_filter('query_vars', 'custom_page_query_vars');


function custom_page_parse_request($wp) {
    if (array_key_exists('custom_page', $wp->query_vars) && $wp->query_vars['custom_page'] == 1) {
        custom_page_controller();
        exit;
    }
    if (array_key_exists('custom_page', $wp->query_vars) && $wp->query_vars['custom_page'] == 2) {
        order_confirmation_page_controller();
        exit;
    }
}
add_action('parse_request', 'custom_page_parse_request');

//Set and activate custom page
function custom_page_flush_rewrite_rules() {
    custom_page_rewrite_rule();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'custom_page_flush_rewrite_rules');

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');