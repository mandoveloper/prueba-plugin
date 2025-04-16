<?php

function register_webhook_endpoint() {
    add_rewrite_rule('^webhook_atix_payment/?', 'index.php?webhook_atix_payment=1', 'top');
}
add_action('init', 'register_webhook_endpoint');

function add_var_webhook_endpoint($vars) {
    $vars[] = 'webhook_atix_payment';
    return $vars;
}
add_filter('query_vars', 'add_var_webhook_endpoint');