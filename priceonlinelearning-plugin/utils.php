<?php

include 'api.php';

function is_woocommerce_active(): bool
{
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
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
                $original_price + $original_price * doubleval($options['min_price_option']),
                $original_price + $original_price * doubleval($options['max_price_option']),
                get_woocommerce_currency(),
                intval($options['days_consistency_option'])
            );

            if (isset($result['product_id'])) {
                $added_products_counter++;
            }

            if (isset($result['errors'])) {
                $error_products_counter++;
            }

        endwhile;

        echo "<div>Number of new connected products: <b>" . $added_products_counter . "</b>.<br>";

    }
}
