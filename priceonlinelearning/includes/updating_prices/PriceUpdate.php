<?php

/**
 * Class to send feedbacks to POL.
 *
 * It follows the main steps reported below:
 * Step 1: user visits the page of a product.
 * Step 2: the product can be added or not to the cart.
 * Step 3: if added no feedback will be sent to POL.
 * Step 4: if not added, send a negative feedback on 'template_redirect'.
 * Step 5: sends a positive feedback after payment completed successfully.
 */
class PriceUpdate
{

    static $positive_interactions = 'positive_interactions';
    static $recently_viewed = 'recently_viewed';
    static $feedbacks = 'feedbacks';

    public function init()
    {
        add_action('template_redirect', array($this, 'update_recently_viewed'), 20);
        add_action('woocommerce_add_to_cart', array($this, 'add_positive_interaction'), 10, 6);

        add_action('template_redirect', array($this, 'send_negative_feedback'), 20);
        add_action('woocommerce_thankyou', array($this, 'send_positive_feedback'));
    }

    /**
     * It adds the product id to the cookie list containing products added to the cart.
     * They are considered as temporarily positive products. The feedback will be sent on order completed.
     *
     * @param $cart_item_key
     * @param $product_id
     * @param $quantity
     * @param $variation_id
     * @param $variation
     * @param $cart_item_data
     */
    public function add_positive_interaction($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        CookieList::add_item(self::$positive_interactions, $product_id);
    }

    /**
     * Method to update the cookie list of recently viewed products.
     */
    public function update_recently_viewed()
    {

        if (!is_singular('product')) {
            return;
        }

        global $post;

        $product_id = $post->ID;

        CookieList::add_item(self::$recently_viewed, $product_id);
    }

    /**
     * Method used to send negative feedbacks to POL.
     * It checks if a product is in the list of recently viewed items,
     * but not in the list of positive interactions (products in the cart) or in the list of feedbacks already sent.
     * Thus, it considers this event as negative interaction and sends to POL the feedback.
     *
     * For variable products, it sends a feedback for each variation.
     */
    public function send_negative_feedback()
    {

        $recent_item_ids = CookieList::get_items(self::$recently_viewed);
        $positive_item_ids = CookieList::get_items(self::$positive_interactions);
        $feedback_item_ids = CookieList::get_items(self::$feedbacks);

        foreach ($recent_item_ids as $product_id) {

            $product = wc_get_product($product_id);

            if (ProductData::is_trackable($product)) {

                // sending feedback only if product is not in the positive list and only if
                // a feedback had not already been sent
                if (in_array($product_id, $positive_item_ids) == 0 &&
                    in_array($product_id, $feedback_item_ids) == 0) {

                    // negative interaction
                    // echo '<br>Negative interaction for product: ' . $product_id . ' -> ' . $product->get_name();

                    if ($product->is_type('simple')) {

                        POLApi::update_price($product_id, 0, false);
                        CookieList::add_item(self::$feedbacks, $product_id);

                    } elseif ($product->is_type('variable')) {

                        // foreach variation sending a negative feedback
                        $variations_id = $product->get_children();
                        foreach ($variations_id as $variation_id) {
                            POLApi::update_price($variation_id, 0, false);
                        }

                        CookieList::add_item(self::$feedbacks, $product_id);

                    }

                } else {
                    // positive updates will be done on purchase.
                }

            }

        }

    }

    /**
     * Method to send positive feedbacks to POL.
     * It sends a feedback for each product in the order, after payment completed successfully (on thank you page).
     *
     * @param $order_id
     */
    public function send_positive_feedback($order_id)
    {

        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item_id => $item) {

            $product = $item->get_product();
            echo '<br>' . $product->get_name() . ' - ' . $product->get_id();

            if (ProductData::is_trackable($product)) {

                POLApi::update_price(
                    $product->get_id(),
                    0,
                    true
                );

                CookieList::add_item(self::$feedbacks, $product->get_id());

            }
        }
    }


}