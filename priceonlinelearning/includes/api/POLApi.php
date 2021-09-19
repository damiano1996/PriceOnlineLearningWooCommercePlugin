<?php

class POLApi
{
    private static $API_URL = 'https://priceonlinelearning.herokuapp.com/api/';

    public static function is_access_token_valid(): bool
    {
        $result = self::get_price(0, 0);
        // echo 'result: ' . json_encode($result);

        if (isset($result['detail']) || is_null($result)) {
            // {"detail":"Invalid token."}
            // echo 'Token is not valid';
            return false;
        } else {
            // echo 'Token is valid';
            return true;
        }

    }

    public static function get_price($product_id, $cluster_id)
    {
        return Request::send_request(self::$API_URL . 'get_price/' . $product_id . '/' . $cluster_id);
    }

    public static function add_product($product_id, $name, $original_price, $min_price, $max_price, $currency, $days_consistency)
    {
        $postRequest = array(
            'product_id' => $product_id,
            'name' => $name,
            'original_price' => $original_price,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'currency' => $currency,
            'days_consistency' => $days_consistency
        );

        return Request::send_request(self::$API_URL . 'add_product/', $postRequest);
    }

    public static function get_product_pol_info($product_id)
    {
        return Request::send_request(self::$API_URL . 'get_product/' . $product_id);
    }

    public static function delete_product($product_id)
    {
        return Request::send_request(self::$API_URL . 'delete_product/' . $product_id, array(), true);
    }

    public static function get_all_prices()
    {
        return Request::send_request(self::$API_URL . 'get_all_prices/');
    }

    public static function update_price($product_id, $cluster_id, $purchased)
    {
        $postRequest = array(
            'product_id' => $product_id,
            'cluster_id' => $cluster_id,
            'purchased' => ($purchased) ? "true" : "false"
        );

        return Request::send_request(self::$API_URL . 'update_price/', $postRequest);
    }
}