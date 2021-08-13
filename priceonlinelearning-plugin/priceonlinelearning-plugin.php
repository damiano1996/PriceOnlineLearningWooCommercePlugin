<?php

/*
Plugin Name: Price Online Learning
Plugin URI: http://www.priceonlinelearning.com/plugins/
Description: Price Online Learning plugin for WooCommerce
Version: 1.0
Author: Price Online Learning
Author URI: http://www.priceonlinelearning.com/
License: GPL2
*/

include 'utils.php';

$app_name = 'Price Online Learning';

// ON ACTIVATION

register_activation_hook(__FILE__, 'set_default_options_fn');
function set_default_options_fn()
{
    $options = get_option('plugin_options');

    $options['access_token'] = "";
    $options['min_price_option'] = 0.0;
    $options['max_price_option'] = 0.2;
    $options['days_consistency_option'] = 3;

    update_option('plugin_options', $options);
}

//
//// DAILY SCHEDULE
//
//register_activation_hook(__FILE__, 'update_all_prices_fn');
//function update_all_prices_fn()
//{
//    if (!wp_next_scheduled('update_all_product_prices')) {
//        wp_schedule_event(time(), 'daily', 'update_all_product_prices');
//    }
//}
//
//function update_all_product_prices()
//{
//
//    $results = get_all_prices();
//
//    foreach ($results as $product_results) {
//        $product_first_cluster = $product_results[0];
//
//        if (isset($product_first_cluster['price'])) {
//            $product = wc_get_product($product_first_cluster['product_id']);
//
//            $product->set_regular_price($product_first_cluster['price']);
//            $product->set_sale_price(false);
//            $product->save();
//        }
//    }
//}

// NOTICE

register_activation_hook(__FILE__, 'fx_admin_notice_example_activation_hook');
function fx_admin_notice_example_activation_hook()
{
    set_transient('fx-admin-notice-example', true, 5);
}

add_action('admin_notices', 'fx_admin_notice_example_notice');
function fx_admin_notice_example_notice()
{

    /* Check transient, if available display notice */
    if (get_transient('fx-admin-notice-example')) {
        if (is_woocommerce_active()) {
            ?>
            <div class="updated notice is-dismissible">
                <p>WooCommerce is installed.<br>
                    You can proceed with configurations: <a href="admin.php?page=pol-menu">settings</a>.</p>
            </div>
            <?php
        } else {
            ?>
            <div class="updated notice is-dismissible">
                <p>WooCommerce is NOT installed.</p>
            </div>
            <?php
        }
        /* Delete transient, only display this notice once. */
        delete_transient('fx-admin-notice-example');
    }
}

// ---------------------------------------------------------------------------------------------------------------------

add_action('admin_init', 'pol_general_settings');
function pol_general_settings()
{

    register_setting('plugin_options', 'plugin_options');

    // Step 1: General Settings
    add_settings_section('main_section', '', 'section_text_fn', __FILE__);
    // access token
    add_settings_field('access_token', 'Personal Access Token', 'access_token_fn', __FILE__, 'main_section');
    // min price
    add_settings_field('min_price_option', 'Minimum price', 'minimum_price_fn', __FILE__, 'main_section');
    // max price
    add_settings_field('max_price_option', 'Maximum price', 'maximum_price_fn', __FILE__, 'main_section');
    // days consistency
    add_settings_field('days_consistency_option', 'Days Consistency', 'days_consistency_fn', __FILE__, 'main_section');

}


function access_token_fn()
{

    echo '<meta name="viewport" content="width=device-width, initial-scale=1">
          <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

    $options = get_option('plugin_options');

    $is_valid = is_access_token_valid();
    $color = ($is_valid) ? 'rgba(0,255,0,0.2)' : 'rgba(255,0,0,0.2)';

    echo "<p>Get it from your Price Online Learning (POL) <a href='https://priceonlinelearning.herokuapp.com/home_account/' target='_blank'>account page</a>.</p>";
    echo "<input id='access_token' name='plugin_options[access_token]' size='40' type='text' value='{$options['access_token']}'
            style='background: {$color}'/>";

    if ($is_valid) {
        echo '<i class="fa fa-check" style="color: green; margin: 5px;"></i>';
    } else {
        echo '<i class="fa fa-close" style="color: red; margin: 5px;"></i>';
    }
}

function minimum_price_fn()
{
    $options = get_option('plugin_options');
    $items = array(
        "original_price - 50%" => -0.5,
        "original_price - 20%" => -0.2,
        "original_price - 10%" => -0.1,
        "original_price (recommended)" => 0.0
    );

    foreach ($items as $item => $value) {
        $checked = ($options['min_price_option'] == $value) ? ' checked="checked" ' : '';
        echo "<label><input " . $checked . " value='$value' name='plugin_options[min_price_option]' type='radio' /> $item</label><br/>";
    }
}

function maximum_price_fn()
{
    $options = get_option('plugin_options');
    $items = array(
        "original_price" => 0.0,
        "original_price + 10%" => 0.1,
        "original_price + 20% (recommended)" => 0.2,
        "original_price + 50%" => 0.5
    );

    foreach ($items as $item => $value) {
        $checked = ($options['max_price_option'] == $value) ? ' checked="checked" ' : '';
        echo "<label><input " . $checked . " value='$value' name='plugin_options[max_price_option]' type='radio' /> $item</label><br/>";
    }
}

function days_consistency_fn()
{
    $options = get_option('plugin_options');
    $items = array(
        "1" => 1,
        "2" => 2,
        "3 (recommended)" => 3,
        "7" => 7
    );

    foreach ($items as $item => $value) {
        $checked = ($options['days_consistency_option'] == $value) ? ' checked="checked" ' : '';
        echo "<label><input " . $checked . " value='$value' name='plugin_options[days_consistency_option]' type='radio' /> $item</label><br/>";
    }
}

function clustering_options_fn()
{
    $options = get_option('plugin_options');
    $items = array(
        "By device" => 1,
        "By gender" => 2
    );

    foreach ($items as $item => $value) {
        $checked = ($options['clustering-radio'] == $value) ? ' checked="checked" ' : '';
        echo "<label><input " . $checked . " value='$value' name='plugin_options[clustering-radio]' type='radio' /> $item</label><br/>";
    }
}


add_action('admin_menu', 'pol_options_page');
function pol_options_page()
{
    global $app_name;

    add_menu_page($app_name, 'POL', 'manage_options', 'pol-menu', 'pol_options_page_html');
    add_submenu_page('pol-menu', $app_name, 'General Settings', 'manage_options', 'pol-menu');
    // add_submenu_page('my-menu', 'Submenu Page Title2', 'Price Online Learning', 'manage_options', 'my-menu2' );

}


function pol_options_page_html()
{

    is_access_token_valid();

    ?>
    <div class="wrap">
        <h2>Price Online Learning Settings</h2>

        <div style="background: rgba(0,0,0,0.2); height: 3px; width: 100%; border-radius: 5px"></div>
        <h3>Step 1: <b>General Settings</b></h3>

        <div>
            <form action="options.php" method="post">
                <?php
                if (function_exists('wp_nonce_field')) {
                    wp_nonce_field('plugin-name-action_' . "yep");
                }
                ?>
                <?php settings_fields('plugin_options'); ?>
                <?php do_settings_sections(__FILE__); ?>
                <p class="submit">
                    <input name="Submit" type="submit" class="button-primary"
                           value="<?php esc_attr_e('Save Changes'); ?>"/>
                </p>
            </form>
        </div>

        <div style="background: rgba(0,0,0,0.2); height: 3px; width: 100%; border-radius: 5px"></div>
        <h3>Step 2: <b>Connects Your Products to POL</b></h3>

        <div>

            This step is necessary to connect your products with your account on POL.
            <br>
            All your products that have not yet been added will be inserted, do not worry for already inserted products,
            they will be skipped.
            <br>
            For future products, inserted by WooCommerce plugin, this operation will be performed automatically.

            <form method="post" enctype="multipart/form-data">
                <input type='hidden' name='add_all_products'/>
                <?php submit_button('Add All Products');
                try {
                    test_handle_post();
                } catch (Exception $e) {
                }
                ?>
            </form>
        </div>

        <div style="background: rgba(0,0,0,0.2); height: 3px; width: 100%; border-radius: 5px"></div>
        <h3>( Step 3: <b>Clustering Customers</b> )</h3>

        <div>

            POL does not provide clustering services, because clustering algorithms require domain knowledge and
            specific data analysis.
            POL has been designed to be as general as possible to satisfy different requirements.
            <br>
            If you have the skills to design a clustering algorithm,
            you can create clusters among your users and communicate the identifier of the cluster to POL using the <a
                    href="https://priceonlinelearning.herokuapp.com/api/api_documentation/" target="_blank">APIs</a>.
            <br>
            <b>This plugin does not create clusters among your users.</b>
            This means that if you have a MEDIUM or PRO plan on POL, you lose the benefit of providing different prices
            for different clusters.

        </div>

    </div>
    <?php
}

/**
 * @throws Exception
 */
function test_handle_post()
{
    if (isset($_POST['add_all_products'])) {
        add_all_products_to_pol();
    }
}


add_action('save_post', 'add_single_product_to_pol_fn', 10, 3);
function add_single_product_to_pol_fn($post_id, $post, $update)
{
    if ($post->post_status != 'publish' || $post->post_type != 'product') {
        return;
    }

    if (!$product = wc_get_product($post)) {
        return;
    }

    if (strcmp($product->get_meta('track_product'), 'yes') !== 0) {
        return;
    }

    $options = get_option('plugin_options');

    $original_price = doubleval($product->get_regular_price());

    add_product(
        $product->get_id(),
        $product->get_name(),
        $original_price,
        $original_price + $original_price * doubleval($options['min_price_option']),
        $original_price + $original_price * doubleval($options['max_price_option']),
        get_woocommerce_currency(),
        intval($options['days_consistency_option'])
    );

    $result = get_price($product->get_id(), 0);

    if (isset($result['price'])) {
        $product->set_regular_price($result['price']);
        $product->set_sale_price(false);
        $product->save();
    }

}

add_action('wp_trash_post', 'delete_product_fn', 1);
add_action('delete_post', 'delete_product_fn', 1);
function delete_product_fn($post_id)
{
    if (get_post_type($post_id) != 'product') return;

    $product = wc_get_product($post_id);
    delete_product($product->get_id());
}

// ----------------------------------------------

add_action('woocommerce_product_options_general_product_data', 'track_product_with_pol_general_setting');
function track_product_with_pol_general_setting()
{
    global $product_object;

    $values = $product_object->get_meta('track_product');

    $args = array(
        'id' => 'track_product',
        'label' => __('Manage prices with <br><b>Price Online Learning</b>', 'cfwc'),
        'value' => empty($values) ? 'yes' : $values,
        'desc_tip' => true,
        'description' => __('If checked the prices will be handled by AI.', 'ctwc'),
    );
    woocommerce_wp_checkbox($args);
}

// Save quantity setting fields values
add_action('woocommerce_admin_process_product_object', 'save_custom_field_product_options_pricing');
function save_custom_field_product_options_pricing($product)
{
    $product->update_meta_data('track_product', isset($_POST['track_product']) ? 'yes' : 'no');
}

