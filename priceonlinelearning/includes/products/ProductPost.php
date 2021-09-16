<?php


class ProductPost
{

    public function init()
    {
        add_action('save_post', array($this, 'add_single_product_to_pol_fn'), 10, 3);

        add_action('wp_trash_post', array($this, 'delete_product_fn'), 1);
        add_action('delete_post', array($this, 'delete_product_fn'), 1);
    }

    public function add_single_product_to_pol_fn($post_id, $post, $update)
    {
        if ($post->post_status != 'publish' || $post->post_type != 'product') {
            return;
        }

        if (!$product = wc_get_product($post)) {
            return;
        }

        // Changes and/or connects product to pol only if box is checked
        if (ProductData::is_trackable($product)) {
            return;
        }

        // If we have custom options for this product, we should use them
        if (isset($_POST['min_price_tab_radio'])) {
            POLUtils::add_update_and_save_product($product, $_POST['min_price_tab_radio'], $_POST['max_price_tab_radio'], $_POST['days_consistency_tab_radio']);
        } else {
            POLUtils::add_update_and_save_product_from_general_options($product);
        }

    }


    public function delete_product_fn($post_id)
    {
        if (get_post_type($post_id) != 'product') return;

        $product = wc_get_product($post_id);
        POLApi::delete_product($product->get_id());
    }
}