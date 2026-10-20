<?php
/**
 * payhere.php — PayHere Payment Gateway Configuration
 * Monet Nails E-Commerce
 */

return [
    // Change to 'false' when going live with production credentials
    'is_sandbox'      => true,

    // Your PayHere Merchant ID (found in PayHere Merchant Dashboard > Domains)
    'merchant_id'     => '1238332',

    // Your PayHere Merchant Secret (found in PayHere Merchant Dashboard)
    'merchant_secret' => 'MzU0MjUyODM4ODM3OTI1Njc1NjUxNzk1ODEwMjI5MTQ4NDY4NjY1Mg==',

    // Default Currency
    'currency'        => 'LKR',

    // URLs
    'return_url'      => 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=order_success',
    'cancel_url'      => 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=checkout',
    'notify_url'      => 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=payhere_notify',
];
