<?php

add_filter('woocommerce_product_data_tabs', 'pol_edit_product_data_tab');
function pol_edit_product_data_tab($tabs)
{

    $tabs['pol_tab'] = array(
        'label' => 'Price Online Learning',
        'class' => array('show_if_simple', 'show_if_variable'),
        'target' => 'pol_tab_target'
    );
    return $tabs;

}

add_action('woocommerce_product_data_panels', 'pol_edit_product_tab_content');
function pol_edit_product_tab_content()
{

    global $product_object;

    echo '<style>
                 .woocommerce_options_panel label, .woocommerce_options_panel legend { width: auto; }
                 li  { margin: 15px; }
          </style>
          <div id="pol_tab_target" class="panel woocommerce_options_panel">';

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

        $pol_product = get_product_pol_info($product_object->get_id());
        if (isset($pol_product['original_price'])) {
            $print_radio_options = false;
            print_product_info($pol_product);
        }

        // variable case
    } elseif ($product_object->is_type('variable')) {

        $variations_id = $product_object->get_children();
        foreach ($variations_id as $variation_id) {

            $pol_product = get_product_pol_info($variation_id);

            if (isset($pol_product['original_price'])) {
                $print_radio_options = false; // if at least one variation has been added, we can hide radio options
                print_product_info($pol_product);

                echo '<br>';
            }
        }

    }


    // print options only if no simple or variation has been added
    if ($print_radio_options) {

        woocommerce_wp_radio(array(
            'id' => 'min_price_tab_radio',
            'label' => '<b>Minimum Price</b>',
            'options' => reverse_option(get_min_prices_options()),
            'value' => 0.0,
            'desc_tip' => true,
            'description' => 'Minimum price that can be suggested by the AI.
                              Set a negative ratio only if the minimum price allows you to get a profit.',
        ));

        woocommerce_wp_radio(array(
            'id' => 'max_price_tab_radio',
            'label' => '<b>Maximum Price</b>',
            'options' => reverse_option(get_max_prices_options()),
            'value' => 0.2,
            'desc_tip' => true,
            'description' => 'Maximum price that can be suggested by the AI.'
        ));

        woocommerce_wp_radio(array(
            'id' => 'days_consistency_tab_radio',
            'label' => '<b>Days Consistency</b>',
            'options' => reverse_option(get_days_consistency_options()),
            'value' => 3,
            'desc_tip' => true,
            'description' => 'Number of days in which the price must remain steady.'
        ));

    }

    echo '</div>';

}

function print_product_info($pol_product)
{
    echo "<p><b>Original name</b>: " . $pol_product['name'] . " ( <b>product id</b>: " . $pol_product['product_id'] . " )<br>
             <b>Original price</b> (" . get_woocommerce_currency() . "): " . $pol_product['original_price'] . "<br>
             <b>Min price</b> (" . get_woocommerce_currency() . "): " . $pol_product['min_price'] . "<br>
             <b>Max price</b> (" . get_woocommerce_currency() . "): " . $pol_product['max_price'] . "<br>
             <b>Days consistency</b>: " . $pol_product['days_consistency'] . "</p>";
}

// Save quantity setting fields values
add_action('woocommerce_admin_process_product_object', 'save_custom_field_product_options_pricing');
function save_custom_field_product_options_pricing($product)
{
    $product->update_meta_data('track_product', isset($_POST['track_product']) ? 'yes' : 'no');
}

