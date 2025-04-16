<?php

function wc_atix_documentation_handler() {
ob_start();
?>
<div class="wc-atix-admin-header">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <img src="<?php echo plugins_url('../assets/images/logoatix.svg', __FILE__); ?>" alt="Atix Payment Services" class="img-admin-logo" />
</div>
<div class="wc-atix-admin-content">
    <h2><?php _e('How to configure this Payment Gateway', 'woocommerce-atix'); ?></h2>
    <ol>
        <li><?php _e("First, you'll have to go through Woocoommerce > Settings in order to enter the Payments Sections.", "woocommerce-atix"); ?><br />
        <?php _e('Also you can click', 'woocommerce-atix'); ?>
        <?php echo sprintf(__('<a href="%s" target="_blank">%s</a>', 'woocommerce-atix'), admin_url('admin.php?page=wc-settings&tab=checkout&section=atix_gateway'), __('here', 'woocommerce-atix')); ?></li>
        <li>
            <?php _e('You will find the different values to register and set your data', 'woocommerce-atix'); ?>
            <div style="width: 100%; text-align: left; margin-top: 15px; margin-bottom: 15px;">
                <img src="<?php echo plugins_url('assets/images/screen-1.png', __FILE__);?>" alt="" class="img-admin-fluid" />
            </div>
            <ul>
                <li><strong><?php _e('Description', 'woocommerce-atix'); ?></strong>: <?php _e('This input controls what the final user will see on the front-end as description, it can be changed to anything, just remember to be as descriptive as possible', 'woocommerce-atix')?></li>
                <li><strong><?php _e('API Key PEN', 'woocommerce-atix'); ?></strong>: <?php _e("This input is the most important one, controls your shop's permission to use the Atix Payment Services API, this one is sent by Atix Payment Services", 'woocommerce-atix')?></li>
                <li><strong><?php _e('API Key USD', 'woocommerce-atix'); ?></strong>: <?php _e("This input is the most important one, controls your shop's permission to use the Atix Payment Services API, this one is sent by Atix Payment Services", 'woocommerce-atix')?></li>
                <li><strong><?php _e('Security key', 'woocommerce-atix'); ?></strong>: <?php _e("This entry is important for authenticating you when updating the sale. It's used by Atix Payment Services", 'woocommerce-atix')?></li>
                <li><strong><?php _e('Pay confirmation url', 'woocommerce-atix'); ?></strong>: <?php _e('This entry indicates the name of your purchase confirmation page, by default it is "checkout"', 'woocommerce-atix')?></li>
                <li><strong><?php _e('Final status', 'woocommerce-atix'); ?></strong>: <?php _e('This entry indicates the status in which you want the transaction to end if it is successful, by default it is "completed."', 'woocommerce-atix')?></li>
                <li><strong><?php _e('Cash/Wallets', 'woocommerce-atix'); ?></strong>: <?php _e("This entry indicates whether you are eligible for cash or digital wallet payments.", 'woocommerce-atix')?></li>
                <li><strong><?php _e('Test Mode', 'woocommerce-atix'); ?></strong>: <?php _e('This checkbox controls if this shop is currently on development or it is in production.', 'woocommerce-atix')?></li>
            </ul>
        </li>
        <li><?php _e('Enter your customized instructions and descriptions first.', 'woocommerce-atix'); ?></li>
        <li><?php _e('Enter your Exclusive Atix Payment API Key.', 'woocommerce-atix'); ?></li>
        <li><?php _e('Specify if your shop is currently on development or are already on production, using the last checkbox of "Test Mode" and click "Save Changes"', 'woocommerce-atix'); ?></li>
        <li><strong><?php _e('Done!', 'woocommerce-atix'); ?></strong> <?php _e('You are ready to start shopping using our Payment Gateway', 'woocommerce-atix'); ?></li>
    </ol>
    <hr>
    <h4><?php _e('If you have any inquiry regarding our plugin, please send an email specifying your problem at the following address:', 'woocommerce-atix'); ?></h4>
    <a href="mailto:soporteti@atix.com.pe">soporteti@atix.com.pe</a>
</div>
<?php
    $content = ob_get_contents();
    ob_flush();
    return $content;

}
