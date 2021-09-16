<?php

/**
 * Class to manage recently viewed products.
 *
 * Step 1: user visits the page of a product.
 * Step 2: the product can be added or not to the cart.
 * Step 3: if added no feedback will be sent to POL.
 * Step 4: if not added, send a negative feedback on 'template_redirect'.
 * Step 5: sends a positive feedback on payment completed successfully.
 */
class PriceUpdate
{

    static $products_in_cart = 'products_in_cart';

    public function init()
    {
        // remove_action('template_redirect', array($this, 'wc_track_product_view'), 20);
        add_action('template_redirect', array($this, 'update_interactions'), 20);
        add_action('woocommerce_add_to_cart', array($this, 'product_in_cart'), 10, 6);
        add_action('woocommerce_payment_complete', array($this, 'products_purchased'));


        add_action('woocommerce_before_shop_loop', array($this, 'test_fn'));
    }

    function test_fn()
    {
        echo "<br><br>Cart: ";
        foreach (CookieList::get_items(self::$products_in_cart) as $item) echo "<br>" . $item;
    }

    public function product_in_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        CookieList::add_item(self::$products_in_cart, $product_id);
    }

    public function update_interactions()
    {
        if (!is_singular('product')) {
            return;
        }

        global $post;

        $product_id = $post->ID;
        $product = wc_get_product($product_id);

        if (in_array($product_id, CookieList::get_items(self::$products_in_cart)) == 0) {
            // negative interaction has been detected since the product has not been moved to the cart

            if ($product->is_type('simple')) {
                POLApi::update_price($product_id, 0, false);
            } else {

                // foreach variation sending a negative feedback
                $variations_id = $product->get_children();
                foreach ($variations_id as $variation_id) {
                    POLApi::update_price($variation_id, 0, false);
                }

            }

        } else {
            // positive updates will be done on purchase.
        }

        CookieList::remove_items(self::$products_in_cart, $product_id);

    }

    function products_purchased($order_id)
    {

        $order = wc_get_order($order_id);

        $items = $order->get_items();

        foreach ($items as $product) {
            POLApi::update_price(
                $product->get_id(),
                0,
                true
            );
        }
    }


}