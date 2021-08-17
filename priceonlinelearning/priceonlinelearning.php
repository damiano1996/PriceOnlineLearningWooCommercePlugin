<?php

/**
 * Plugin Name: Price Online Learning
 * Plugin URI: http://www.priceonlinelearning.com/
 * Description: Dynamic learning for pricing your products.
 * Version: 1.0
 * Author: Price Online Learning
 * Author URI: http://www.priceonlinelearning.com/
 * License: GPL2
 */


if (!defined('POL_PLUGIN_FILE')) {
    define('POL_PLUGIN_FILE', __FILE__);
}

if (!class_exists('POL', false)) {
    include_once dirname(POL_PLUGIN_FILE) . '/includes/POL.php';
}

POL::instance();

// ---------------------------------------------------------------------------------------------------------------------


// get product page
//add_action('woocommerce_before_single_product', 'action_woocommerce_before_single_product', 10, 1);
//function action_woocommerce_before_single_product($data)
//{
//    global $product;
//    global $post;
//
//    $cookie_name = "lastVisitedProduct";
//    $cookie_value = $product->get_id();
//
//    $time = time();
//    setcookie("test", "value", time() + (86400 * 30));
//    $varname = $_COOKIE["test"];
//    echo "my cookie: " . $varname;
//}

//add_filter('request', 'alter_the_query');
//function alter_the_query($request)
//{
//
//    echo $_COOKIE['lastVisitedProduct'];
//
//    if (isset($_COOKIE['lastVisitedProduct'])) {
//
//        $product_id = $_COOKIE['lastVisitedProduct'];
//        $product = wc_get_product($product_id);
//
//        update_price(
//            $product->get_id(),
//            0,
//            false
//        );
//
//    }
//
//    return $request;
//}


// PAYMENT COMPLETED
//add_action('woocommerce_payment_complete', 'products_purchased');
//function products_purchased($order_id)
//{
//
//    $order = wc_get_order($order_id);
//
//    $items = $order->get_items();
//
//    foreach ($items as $product) {
//        update_price(
//            $product->get_id(),
//            0,
//            true
//        );
//    }
//}