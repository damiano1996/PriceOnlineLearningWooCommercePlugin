<?php

class POLUtils
{

    public static function is_woocommerce_active(): bool
    {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    public static function is_server_to_server_communication_available(): bool
    {
        return !is_null(POLApi::get_price(0, 0));
    }

    public static function add_update_and_save_all_products_to_pol(): int
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
            if (ProductData::is_trackable($product)) {
                $added_products_counter += self::add_update_and_save_product_from_general_options($product);
            }

        endwhile;

        return $added_products_counter;
    }

    public static function set_new_price($product, $new_price)
    {
        $product->set_regular_price(doubleval($new_price));
        $product->set_sale_price(false);
        $product->save();
    }

    private static function update_product_regular_and_sales_price($product)
    {
        $result = POLApi::get_price($product->get_id(), 0);

        if (isset($result['price'])) {
            self::set_new_price($product, $result['price']);
        }
    }

    private static function add_update_and_save_simple_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): bool
    {
        $regular_price = doubleval($product->get_regular_price());
        $min_price = number_format($regular_price + $regular_price * doubleval($min_price_ratio), 2, '.', '');
        $max_price = number_format($regular_price + $regular_price * doubleval($max_price_ratio), 2, '.', '');

        $result = POLApi::add_product(
            $product->get_id(),
            $product->get_name(),
            $regular_price,
            $min_price,
            $max_price,
            get_woocommerce_currency(),
            intval($days_consistency)
        );

        // echo $min_price . " " . $max_price . " " . json_encode($result) . '<br>';

        self::update_product_regular_and_sales_price($product);

        return isset($result['product_id']);
    }

    private static function add_update_and_save_variable_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): int
    {
        $added_products_counter = 0;

        $variations_id = $product->get_children();

        foreach ($variations_id as $variation_id) {
            $variation_product = wc_get_product($variation_id);

            if (self::add_update_and_save_simple_product($variation_product, $min_price_ratio, $max_price_ratio, $days_consistency)) {
                $added_products_counter++;
            }
        }

        return $added_products_counter;
    }

    public static function add_update_and_save_product($product, $min_price_ratio, $max_price_ratio, $days_consistency): int
    {
        $added_products_counter = 0;

        if ($product->is_type('simple')) {

            if (self::add_update_and_save_simple_product($product, $min_price_ratio, $max_price_ratio, $days_consistency)) {
                $added_products_counter++;
            }

        } elseif ($product->is_type('variable')) {
            $added_products_counter += self::add_update_and_save_variable_product($product, $min_price_ratio, $max_price_ratio, $days_consistency);
        }

        return $added_products_counter;
    }


    public static function add_update_and_save_product_from_general_options($product): int
    {
        $options = get_option('plugin_options');

        return self::add_update_and_save_product(
            $product,
            $options['min_price_option'],
            $options['max_price_option'],
            $options['days_consistency_option']
        );
    }
}
