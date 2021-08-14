<?php

$BASE_URL = 'https://priceonlinelearning.herokuapp.com/';
$API_URL = $BASE_URL . 'api/';

function send_request($url, $postRequest = array(), $delete = false)
{
    $options = get_option('plugin_options');
    $headerRequest = array('Authorization: Token ' . $options['access_token']);

    $cURLConnection = curl_init($url);

    if (count($postRequest) > 0) curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
    if ($delete) curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $headerRequest);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $apiResponse = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    return json_decode($apiResponse, true);
}

function is_access_token_valid(): bool
{
    $result = get_price(0, 0);
    // echo 'result: ' . json_encode($result);

    if (isset($result['detail'])) {
        // {"detail":"Invalid token."}
        // echo 'Token is not valid';
        return false;
    } else {
        // echo 'Token is valid';
        return true;
    }

}

function add_product($product_id, $name, $original_price, $min_price, $max_price, $currency, $days_consistency)
{
    global $API_URL;

    $postRequest = array(
        'product_id' => $product_id,
        'name' => $name,
        'original_price' => $original_price,
        'min_price' => $min_price,
        'max_price' => $max_price,
        'currency' => $currency,
        'days_consistency' => $days_consistency
    );

    return send_request($API_URL . 'add_product/', $postRequest);
}

function get_product_pol_info($product_id)
{
    global $API_URL;

    return send_request($API_URL . 'get_product/' . $product_id);
}

function delete_product($product_id)
{
    global $API_URL;

    return send_request($API_URL . 'delete_product/' . $product_id, array(), true);
}

function get_price($product_id, $cluster_id)
{
    global $API_URL;

    return send_request($API_URL . 'get_price/' . $product_id . '/' . $cluster_id);
}

function get_all_prices()
{
    global $API_URL;

    return send_request($API_URL . 'get_all_prices/');
}

function update_price($product_id, $cluster_id, $purchased)
{
    global $API_URL;

    $postRequest = array(
        'product_id' => $product_id,
        'cluster_id' => $cluster_id,
        'purchased' => ($purchased) ? "true" : "false"
    );

    return send_request($API_URL . 'update_price/', $postRequest);
}