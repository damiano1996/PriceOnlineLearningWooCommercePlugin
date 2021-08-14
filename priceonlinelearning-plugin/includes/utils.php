<?php

function is_woocommerce_active(): bool
{
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}

function add_update_and_save_all_products_to_pol(): int
{

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query($args);
    $added_products_counter = 0;

    while ($loop->have_posts()) : $loop->the_post();
        global $product;

        // Adding the product to POL only if the box is checked
        if (strcmp($product->get_meta('track_product'), 'yes') == 0) {
            $added_products_counter += add_update_and_save_product_from_general_options($product);
        }

    endwhile;

    return $added_products_counter;
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

function add_update_and_save_simple_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): bool
{
    $regular_price = doubleval($product->get_regular_price());
    $min_price = number_format($regular_price + $regular_price * doubleval($min_price_ratio), 2, '.', '');
    $max_price = number_format($regular_price + $regular_price * doubleval($max_price_ratio), 2, '.', '');

    $result = add_product(
        $product->get_id(),
        $product->get_name(),
        $regular_price,
        $min_price,
        $max_price,
        get_woocommerce_currency(),
        intval($days_consistency)
    );

    echo $min_price . " " . $max_price . " " . json_encode($result) . '<br>';

    update_product_regular_and_sales_price($product);

    return isset($result['product_id']);
}

function add_update_and_save_variable_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): int
{
    $added_products_counter = 0;

    $variations_id = $product->get_children();

    foreach ($variations_id as $variation_id) {
        $variation_product = wc_get_product($variation_id);

        if (add_update_and_save_simple_product($variation_product, $min_price_ratio, $max_price_ratio, $days_consistency)) {
            $added_products_counter++;
        }
    }

    return $added_products_counter;
}

function add_update_and_save_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): int
{
    $added_products_counter = 0;

    if ($product->is_type('simple')) {

        if (add_update_and_save_simple_product($product, $min_price_ratio, $max_price_ratio, $days_consistency)) {
            $added_products_counter++;
        }

    } elseif ($product->is_type('variable')) {
        $added_products_counter += add_update_and_save_variable_product($product, $min_price_ratio, $max_price_ratio, $days_consistency);
    }

    return $added_products_counter;
}


function add_update_and_save_product_from_general_options($product): int
{
    $options = get_option('plugin_options');

    return add_update_and_save_product(
        $product,
        $options['min_price_option'],
        $options['max_price_option'],
        $options['days_consistency_option']
    );
}
