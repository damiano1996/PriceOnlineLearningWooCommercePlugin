<?php

/*
Plugin Name: Price Online Learning
Plugin URI: http://www.priceonlinelearning.com/
Description: Price Online Learning plugin for WooCommerce
Version: 1.0
Author: Price Online Learning
Author URI: http://www.priceonlinelearning.com/
License: GPL2
*/

include 'includes/my_options.php';
include 'includes/pol_api.php';
include 'includes/utils.php';
include 'includes/admin_menu/general_settings.php';
include 'includes/products/product_data.php';
include 'includes/products/product_post.php';

$app_name = 'Price Online Learning';

// ON ACTIVATION


//
//// DAILY SCHEDULE
//
//register_activation_hook(__FILE__, 'update_all_prices_fn');
//function update_all_prices_fn()
//{
//    if (!wp_next_scheduled('update_all_product_prices')) {
//        wp_schedule_event(time(), 'daily', 'update_all_product_prices');
//    }
//}
//
//function update_all_product_prices()
//{
//
//    $results = get_all_prices();
//
//    foreach ($results as $product_results) {
//        $product_first_cluster = $product_results[0];
//
//        if (isset($product_first_cluster['price'])) {
//            $product = wc_get_product($product_first_cluster['product_id']);
//
//            $product->set_regular_price($product_first_cluster['price']);
//            $product->set_sale_price(false);
//            $product->save();
//        }
//    }
//}

// NOTICE

register_activation_hook(__FILE__, 'fx_admin_notice_example_activation_hook');
function fx_admin_notice_example_activation_hook()
{
    set_transient('fx-admin-notice-example', true, 5);
}

add_action('admin_notices', 'fx_admin_notice_example_notice');
function fx_admin_notice_example_notice()
{

    /* Check transient, if available display notice */
    if (get_transient('fx-admin-notice-example')) {
        if (is_woocommerce_active()) {
            ?>
            <div class="updated notice is-dismissible">
                <p><b>Price Online Learning</b> successfully activated!<br>
                    You can proceed with the <a href="admin.php?page=pol-menu">configurations</a>.</p>
            </div>
            <?php
        } else {
            ?>
            <div class="updated notice is-dismissible">
                <p><b>WooCommerce</b> is not active...<br>
                    <b>Price Online Learning</b> has been activated, but it won't work properly.<br>
                    Please, activate <b>WooCommerce</b>.</p>
            </div>
            <?php
        }
        /* Delete transient, only display this notice once. */
        delete_transient('fx-admin-notice-example');
    }
}

// ---------------------------------------------------------------------------------------------------------------------


//// get product page
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
add_action('woocommerce_payment_complete', 'products_purchased');
function products_purchased($order_id)
{

    $order = wc_get_order($order_id);

    $items = $order->get_items();

    foreach ($items as $product) {
        update_price(
            $product->get_id(),
            0,
            true
        );
    }
}