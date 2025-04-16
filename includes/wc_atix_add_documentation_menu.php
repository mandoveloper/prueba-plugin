<?php
function wc_atix_add_documentation_menu() {
    add_submenu_page(
        'woocommerce',
        __('Atix Payment Services - Documentation', 'woocommerce-atix'),
        __('Atix Documentation', 'woocommerce-atix'),
        'manage_options',
        'atix-documentation',
        'wc_atix_documentation_handler'
    );
}

add_action('admin_menu', 'wc_atix_add_documentation_menu', 99);