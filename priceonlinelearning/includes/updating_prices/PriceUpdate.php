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

    static $positive_products_interactions = 'positive_products_interactions';

    public function init()
    {
        // remove_action('template_redirect', array($this, 'wc_track_product_view'), 20);
        add_action('template_redirect', array($this, 'update_interactions'), 20);
        add_action('woocommerce_add_to_cart', array($this, 'add_positive_product_interaction'), 10, 6);
        add_action('woocommerce_payment_complete', array($this, 'products_purchased'));
    }

    public function add_positive_product_interaction($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        CookieList::add_item(self::$positive_products_interactions, $product_id);
    }

    public function update_interactions()
    {
        if (!is_singular('product')) {
            return;
        }

        global $post;

        $product_id = $post->ID;
        $product = wc_get_product($product_id);

        if (in_array($product_id, CookieList::get_items(self::$positive_products_interactions)) == 0) {
            // negative interaction has been detected since the product has not been moved to the cart

            if ($product->is_type('simple')) {
                POLApi::update_price($product_id, 0, false);

            } elseif ($product->is_type('variable')) {

                // foreach variation sending a negative feedback
                $variations_id = $product->get_children();
                foreach ($variations_id as $variation_id) {
                    POLApi::update_price($variation_id, 0, false);
                }

            }

        } else {
            // positive updates will be done on purchase.
        }

        CookieList::remove_items(self::$positive_products_interactions, $product_id);

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