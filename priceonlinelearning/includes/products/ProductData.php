<?php

/**
 * Class to add configurations to the product page of the admin section.
 * It adds a tab to the product data section with custom settings for POL integration.
 */
class ProductData
{

    /**
     * Method to check if the given product can be or not tracked by POL.
     *
     * @param $product
     * @return bool true if metadata is yes or empty
     */
    public static function is_trackable($product): bool
    {
        $meta = $product->get_meta('track_product');
        return strcmp($meta, 'yes') == 0 || strcmp($meta, '') == 0;
    }

    public function init()
    {
        add_filter('woocommerce_product_data_tabs', array($this, 'pol_edit_product_data_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'pol_edit_product_tab_content'));
        add_action('woocommerce_admin_process_product_object', array($this, 'update_tracking_status'));
        // add_action('woocommerce_save_product_variation', array($this, 'save_custom_field_product_options_pricing'));
    }

    public function pol_edit_product_data_tab($tabs)
    {

        $tabs['pol_tab'] = array(
            'label' => 'Price Online Learning',
            'class' => array('show_if_simple', 'show_if_variable'),
            'target' => 'pol_tab_target'
        );

        return $tabs;
    }

    public function pol_edit_product_tab_content()
    {

        global $product_object;

        $this->set_style();
        echo '<div id="pol_tab_target" class="panel woocommerce_options_panel">';

        $values = $product_object->get_meta('track_product');

        woocommerce_wp_checkbox(array(
            'id' => 'track_product',
            'label' => 'Manage prices with <br><b>Price Online Learning</b>',
            'value' => empty($values) ? 'yes' : $values,
            'desc_tip' => true,
            'description' => 'If checked the prices will be handled by AI.',
        ));

        $print_radio_options = true;

        // simple product case: checks if product has been added to pol
        if ($product_object->is_type('simple')) {

            $pol_product = POLApi::get_product_pol_info($product_object->get_id());
            if (isset($pol_product['original_price'])) {
                $print_radio_options = false;
                self::print_product_info($pol_product);
            }

            // variable case
        } elseif ($product_object->is_type('variable')) {

            $variations_id = $product_object->get_children();
            foreach ($variations_id as $variation_id) {

                $pol_product = POLApi::get_product_pol_info($variation_id);

                if (isset($pol_product['original_price'])) {
                    $print_radio_options = false; // if at least one variation has been added, we can hide radio options
                    self::print_product_info($pol_product);

                    echo '<br>';
                }
            }

        }


        // print options only if no simple or variation has been added
        if ($print_radio_options) {

            woocommerce_wp_radio(array(
                'id' => 'min_price_tab_radio',
                'label' => '<b>Minimum Price</b>',
                'options' => MyOptions::reverse_option(MyOptions::get_min_prices_options()),
                'value' => 0.0,
                'desc_tip' => true,
                'description' => 'Minimum price that can be suggested by the AI.
                              Set a negative ratio only if the minimum price allows you to get a profit.',
            ));

            woocommerce_wp_radio(array(
                'id' => 'max_price_tab_radio',
                'label' => '<b>Maximum Price</b>',
                'options' => MyOptions::reverse_option(MyOptions::get_max_prices_options()),
                'value' => 0.2,
                'desc_tip' => true,
                'description' => 'Maximum price that can be suggested by the AI.'
            ));

            woocommerce_wp_radio(array(
                'id' => 'days_consistency_tab_radio',
                'label' => '<b>Days Consistency</b>',
                'options' => MyOptions::reverse_option(MyOptions::get_days_consistency_options()),
                'value' => 3,
                'desc_tip' => true,
                'description' => 'Number of days in which the price must remain steady.'
            ));

        }

        echo '</div>';

    }

    private function set_style()
    {

        ?>
        <style>
            .woocommerce_options_panel label,
            .woocommerce_options_panel legend {
                width: auto;
            }

            li {
                margin: 15px;
            }
        </style>
        <?php

    }

    private function print_product_info($pol_product)
    {
        echo "<p><b>Original name</b>: " . $pol_product['name'] . " ( <b>product id</b>: " . $pol_product['product_id'] . " )<br>
             <b>Original price</b> (" . get_woocommerce_currency() . "): " . $pol_product['original_price'] . "<br>
             <b>Min price</b> (" . get_woocommerce_currency() . "): " . $pol_product['min_price'] . "<br>
             <b>Max price</b> (" . get_woocommerce_currency() . "): " . $pol_product['max_price'] . "<br>
             <b>Days consistency</b>: " . $pol_product['days_consistency'] . "</p>";
    }

    /**
     * Method to update the product status, can it be tracked by POL, or not?
     * It does not consider variations of a given product.
     *
     * @param $product
     */
    public function update_tracking_status($product)
    {
        $product->update_meta_data('track_product', isset($_POST['track_product']) ? 'yes' : 'no');
    }

}