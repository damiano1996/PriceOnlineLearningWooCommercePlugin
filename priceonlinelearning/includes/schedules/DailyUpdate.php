<?php

/**
 * Class to update prices of the tracked products.
 * It sets up a cron event to update prices once time a day.
 * Since WordPress doesn't provide threading functionalities, it will update price at the first user access of the day.
 */
class DailyUpdate
{

    public function init()
    {
        register_activation_hook(POL_PLUGIN_FILE, array($this, 'scheduling_activation'));
        add_action('prices_update_event', array($this, 'update_all_product_prices'));

        // add a custom interval filter
        // add_filter('cron_schedules', array($this, 'custom_timing_interval'));

        // deactivation
        register_deactivation_hook(POL_PLUGIN_FILE, array($this, 'scheduling_deactivation'));

    }

//    function custom_timing_interval($schedules)
//    {
//        $schedules['custom_interval'] = array(
//            'interval' => WP_CRON_LOCK_TIMEOUT,
//            'display' => 'pol custom interval'
//        );
//        return $schedules;
//    }

    public function scheduling_activation()
    {
        if (!wp_next_scheduled('prices_update_event')) {
            wp_schedule_event(time(), 'daily', 'prices_update_event');
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

    function scheduling_deactivation()
    {
        wp_clear_scheduled_hook('prices_update_event');
    }

}