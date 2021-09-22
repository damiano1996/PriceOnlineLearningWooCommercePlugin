<?php

/**
 * Class that contains static functions implementing the APIs offered by POL.
 */
class POLApi
{
    private static $API_URL = 'https://www.priceonlinelearning.com/api/';

    /**
     * Method to check if the saved token is valid.
     *
     * @return bool
     */
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

    /**
     * Method returns the suggested price for the given product.
     *
     * @param $product_id
     * @param $cluster_id
     * @return mixed
     */
    public static function get_price($product_id, $cluster_id)
    {
        return Request::send_request(self::$API_URL . 'get_price/' . $product_id . '/' . $cluster_id);
    }

    /**
     * Method to add a product on POL.
     *
     * @param $product_id
     * @param $name
     * @param $original_price
     * @param $min_price
     * @param $max_price
     * @param $currency
     * @param $days_consistency
     * @return mixed
     */
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

    /**
     * Method to retrieve product infos from POL.
     *
     * @param $product_id
     * @return mixed
     */
    public static function get_product_pol_info($product_id)
    {
        return Request::send_request(self::$API_URL . 'get_product/' . $product_id);
    }

    /**
     * Method to delete a product from POL.
     *
     * @param $product_id
     * @return mixed
     */
    public static function delete_product($product_id)
    {
        return Request::send_request(self::$API_URL . 'delete_product/' . $product_id, array(), true);
    }

    /**
     * Method to get all the prices of all the products.
     *
     * @return mixed
     */
    public static function get_all_prices()
    {
        return Request::send_request(self::$API_URL . 'get_all_prices/');
    }

    /**
     * Method to give the feedback to POL.
     *
     * @param $product_id
     * @param $cluster_id
     * @param $purchased
     * @return mixed
     */
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