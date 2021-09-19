<?php

/**
 * Class to create the POL settings page for the admin user.
 */
class GeneralSettingsPage
{

    public function init()
    {
        register_activation_hook(POL_PLUGIN_FILE, array($this, 'set_default_options_fn'));
        add_action('admin_init', array($this, 'pol_general_settings'));
        add_action('admin_menu', array($this, 'pol_options_page'));
    }

    public function set_default_options_fn()
    {
        $options = get_option('plugin_options');

        $options['access_token'] = "";
        $options['min_price_option'] = 0.0;
        $options['max_price_option'] = 0.2;
        $options['days_consistency_option'] = 3;

        update_option('plugin_options', $options);
    }


    public function pol_general_settings()
    {

        register_setting('plugin_options', 'plugin_options');

        // Step 1: General Settings
        add_settings_section('main_section', '', array($this, 'section_text_fn'), __FILE__);
        // access token
        add_settings_field('access_token', 'Personal Access Token', array($this, 'access_token_fn'), __FILE__, 'main_section');
        // min price
        add_settings_field('min_price_option', 'Minimum Price', array($this, 'minimum_price_fn'), __FILE__, 'main_section');
        // max price
        add_settings_field('max_price_option', 'Maximum Price', array($this, 'maximum_price_fn'), __FILE__, 'main_section');
        // days consistency
        add_settings_field('days_consistency_option', 'Days Consistency', array($this, 'days_consistency_fn'), __FILE__, 'main_section');

    }

    public function section_text_fn()
    {
    }


    public function access_token_fn()
    {

        echo '<meta name="viewport" content="width=device-width, initial-scale=1">
              <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

        $options = get_option('plugin_options');

        $is_valid = POLApi::is_access_token_valid();
        $color = ($is_valid) ? 'rgba(0,255,0,0.2)' : 'rgba(255,0,0,0.2)';

        echo "<p>Get it from your Price Online Learning (POL) <a href='https://priceonlinelearning.herokuapp.com/home_account/' target='_blank'>account page</a>.</p><br>";
        echo "<input id='access_token' name='plugin_options[access_token]' size='40' type='text' value='{$options['access_token']}'
            style='background: $color'/>";

        if ($is_valid) {
            echo '<i class="fa fa-check" style="color: green; margin: 5px;"></i>';
        } else {
            echo '<i class="fa fa-close" style="color: red; margin: 5px;"></i>';
        }
    }

    public function minimum_price_fn()
    {
        $options = get_option('plugin_options');

        echo "<p><b>Description</b>: minimum price that can be suggested by the AI. Set a negative ratio only if the minimum price allows you to get a profit.</p><br>";

        foreach (MyOptions::get_min_prices_options() as $item => $value) {
            $checked = ($options['min_price_option'] == $value) ? ' checked="checked" ' : '';
            echo "<label><input " . $checked . " value='$value' name='plugin_options[min_price_option]' type='radio' /> $item</label><br/>";
        }
    }

    public function maximum_price_fn()
    {
        $options = get_option('plugin_options');

        echo "<p><b>Description</b>: maximum price that can be suggested by the AI.</p><br>";

        foreach (MyOptions::get_max_prices_options() as $item => $value) {
            $checked = ($options['max_price_option'] == $value) ? ' checked="checked" ' : '';
            echo "<label><input " . $checked . " value='$value' name='plugin_options[max_price_option]' type='radio' /> $item</label><br/>";
        }
    }

    public function days_consistency_fn()
    {
        $options = get_option('plugin_options');

        echo "<p><b>Description</b>: number of days in which the price must remain steady.</p><br>";

        foreach (MyOptions::get_days_consistency_options() as $item => $value) {
            $checked = ($options['days_consistency_option'] == $value) ? ' checked="checked" ' : '';
            echo "<label><input " . $checked . " value='$value' name='plugin_options[days_consistency_option]' type='radio' /> $item</label><br/>";
        }
    }


    public function pol_options_page()
    {

        add_menu_page(
            'Price Online Learning',
            'POL',
            'manage_options',
            'priceonlinelearning-settings',
            array($this, 'pol_options_page_html'),
            plugin_dir_url(POL_PLUGIN_FILE) . 'images/logo.svg'
        );
        add_submenu_page('priceonlinelearning-settings', 'Price Online Learning', 'General Settings', 'manage_options', 'priceonlinelearning-settings');
        // add_submenu_page('my-menu', 'Submenu Page Title2', 'Price Online Learning', 'manage_options', 'my-menu2' );

    }

    public function pol_options_page_html()
    {

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

                This step is necessary to connect your simple and variable products to POL.
                If you click on the button below, products that are not present in your POL account will be inserted
                using
                the settings defined above.
                The regular price of the products will be updated accordingly.
                If you want a custom settings for different products, you can manage them from the <b>Product data</b>
                section (WordPress menu -> Products -> Edit (a product) -> Product data -> Price Online Learning).
                <br>
                Operations will be performed successfully only if you have an active business plan on your POL account.

                <form method="post" enctype="multipart/form-data">
                    <input type='hidden' name='add_all_products'/>
                    <?php submit_button('Add All Products');
                    try {
                        $this->add_all_products_from_settings();
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
                you can create clusters among your users and communicate the identifier of the cluster to POL using the
                <a href="https://priceonlinelearning.herokuapp.com/api/api_documentation/" target="_blank">APIs</a>.
                <br>
                <b>This plugin does not create clusters among your users.</b>
                This means that if you have a MEDIUM or PRO plan on POL, you lose the benefit of providing different
                prices
                for different clusters.

            </div>

        </div>
        <?php
    }

    /**
     * @throws Exception
     */
    public function add_all_products_from_settings()
    {
        if (isset($_POST['add_all_products'])) {
            echo "<div>Number of new connected products: <b>" . POLUtils::add_update_and_save_all_products_to_pol() . "</b>.<br>";
        }
    }
}