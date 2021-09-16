<?php

class DailyUpdate
{

    public function init()
    {
        register_activation_hook(POL_PLUGIN_FILE, array($this, 'scheduling_activation'));
        add_action('my_event', array($this, 'update_all_product_prices'));

        // add a custom interval filter
        add_filter('cron_schedules', array($this, 'five_minutes_interval'));

        // deactivation
        register_deactivation_hook(POL_PLUGIN_FILE, array($this, 'my_deactivation'));

    }

    function five_minutes_interval($schedules)
    {
        $schedules['five_minutes'] = array(
            'interval' => 1,
            'display' => '5 minutes'
        );
        return $schedules;
    }

    public function scheduling_activation()
    {
        if (!wp_next_scheduled('my_event')) {
            wp_schedule_event(time(), 'five_minutes', 'my_event');
        }
    }

    public function update_all_product_prices()
    {

        $results = POLApi::get_all_prices();

        foreach ($results as $product_results) {
            $product_first_cluster = $product_results[0];

            if (isset($product_first_cluster['price'])) {

                try {
                    $product = wc_get_product($product_first_cluster['product_id']);
                    $new_price = $product_first_cluster['price'];

                    if (ProductData::is_trackable($product))
                        POLUtils::set_new_price($product, $new_price);

                } catch (Exception $exception) {
                }
            }
        }
    }

    function my_deactivation()
    {
        wp_clear_scheduled_hook('my_event');
    }

}