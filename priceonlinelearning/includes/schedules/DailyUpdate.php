<?php

class DailyUpdate
{

    public function init()
    {
        register_activation_hook(POL_PLUGIN_FILE, array($this, 'scheduling_activation'));
        add_action('my_event', array($this, 'update_all_product_prices'));
        // add a custom interval filter
        add_filter('cron_schedules', array($this, 'five_minutes_interval'));
    }

    function five_minutes_interval($schedules)
    {
        $schedules['five_minutes'] = array(
            'interval' => 60 * 5,
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

        POLApi::update_price(276, 0, true);

//        $results = POLApi::get_all_prices();
//
//        foreach ($results as $product_results) {
//            $product_first_cluster = $product_results[0];
//
//            if (isset($product_first_cluster['price'])) {
//                $product = wc_get_product($product_first_cluster['product_id']);
//
//                if (strcmp($product->get_meta('track_product'), 'yes') == 0) {
//
//                    // TODO: generalize this
//                    $product->set_regular_price($product_first_cluster['price']);
//                    $product->set_sale_price(false);
//                    $product->save();
//                }
//            }
//        }
    }

}