<?php

include 'api.php';

function is_woocommerce_active(): bool
{
    return is_plugin_active('woocommerce/woocommerce.php');
}


/**
 * @throws Exception
 */
function add_all_products_to_pol()
{
    if (!is_woocommerce_active()) {
        throw new Exception('WooCommerce is not active');
    } else {

        $options = get_option('plugin_options');

        $added_products_counter = 0;
        $error_products_counter = 0;

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );

        $loop = new WP_Query($args);

        while ($loop->have_posts()) : $loop->the_post();
            global $product;

            $original_price = doubleval($product->get_price());

            $result = add_product(
                $product->get_id(),
                $product->get_name(),
                $original_price,
                $original_price + $original_price * doubleval($options['min-price-radio']),
                $original_price + $original_price * doubleval($options['max-price-radio']),
                get_woocommerce_currency(),
                intval($options['days-consistency-radio'])
            );

            if (isset($result['messages'])) {
                $added_products_counter++;
            }

            if (isset($result['errors'])) {
                $error_products_counter++;
            }

            echo "<div>Number of new connected products: <b>" . $added_products_counter . "</b>.<br>";

//            echo '<br>' . get_price($product->get_id(), 0);
//
//            echo '<br>' . get_all_prices()[0][0]['price'];
//            echo '<br>' . get_all_prices()[0][1]['price'];
//            echo '<br>' . get_all_prices()[0][2]['price'];
//            echo '<br>' . get_all_prices()[0][3]['price'];
//            echo '<br>' . get_all_prices()[0][4]['price'];
//            echo '<br>' . get_all_prices()[0][5]['price'];
//
//            echo '<br>' . json_encode(update_price($product->get_id(), 0, true));
//            echo '<br>' . json_encode(update_price($product->get_id(), 0, false));

        endwhile;
    }
}
