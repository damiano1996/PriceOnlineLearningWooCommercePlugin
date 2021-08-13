<?php

include 'api.php';

function is_woocommerce_active(): bool
{
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}


function add_all_products_to_pol(): int
{

    // When adding all products from button, the general options have precedence.
    $options = get_option('plugin_options');

    $added_products_counter = 0;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query($args);

    while ($loop->have_posts()) : $loop->the_post();
        global $product;

        // trying to add the product only if the box is checked
        if (strcmp($product->get_meta('track_product'), 'yes') == 0) {

            if ($product->is_type('simple')) { // FIRST CASE

                if (add_and_save_product($product, $options)) {
                    $added_products_counter++;
                }

            } elseif ($product->is_type('variable')) { // SECOND CASE

                $variations_id = $product->get_children();

                foreach ($variations_id as $variation_id) {
                    $variation_product = wc_get_product($variation_id);

                    if (add_and_save_product($variation_product, $options)) {
                        $added_products_counter++;
                    }
                }

            }

        }

    endwhile;

    return $added_products_counter;
}

function add_and_save_product($product, $options): bool
{
    $regular_price = doubleval($product->get_regular_price());

    $result = add_product(
        $product->get_id(),
        $product->get_name(),
        $regular_price,
        $regular_price + $regular_price * doubleval($options['min_price_option']),
        $regular_price + $regular_price * doubleval($options['max_price_option']),
        get_woocommerce_currency(),
        intval($options['days_consistency_option'])
    );

    update_product_regular_and_sales_price($product);

    return isset($result['product_id']);
}

function update_product_regular_and_sales_price($product)
{
    $result = get_price($product->get_id(), 0);

    if (isset($result['price'])) {
        $product->set_regular_price($result['price']);
        $product->set_sale_price(false);
        $product->save();
    }
}
