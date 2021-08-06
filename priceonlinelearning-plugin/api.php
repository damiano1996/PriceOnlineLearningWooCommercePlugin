<?php

$api_url = 'https://priceonlinelearning.herokuapp.com/api/';

function send_request( $url, $postRequest = array() ) {
	$options       = get_option( 'plugin_options' );
	$headerRequest = array( 'Authorization: Token ' . $options['access_token'] );

	$cURLConnection = curl_init( $url );
	if ( count( $postRequest ) > 0 ) {
		curl_setopt( $cURLConnection, CURLOPT_POSTFIELDS, $postRequest );
	}
	curl_setopt( $cURLConnection, CURLOPT_HTTPHEADER, $headerRequest );
	curl_setopt( $cURLConnection, CURLOPT_RETURNTRANSFER, true );

	$apiResponse = curl_exec( $cURLConnection );
	curl_close( $cURLConnection );

	return json_decode( $apiResponse, true );
}

function add_product( $product_id, $name, $original_price, $min_price, $max_price, $currency, $days_consistency ) {
	global $api_url;

	$postRequest = array(
		'product_id'       => $product_id,
		'name'             => $name,
		'original_price'   => $original_price,
		'min_price'        => $min_price,
		'max_price'        => $max_price,
		'currency'         => $currency,
		'days_consistency' => $days_consistency
	);

	return send_request( $api_url . 'add_product/', $postRequest );
}

function get_price( $product_id, $cluster_id ) {
	global $api_url;

	return send_request( $api_url . 'get_price/' . $product_id . '/' . $cluster_id );
}

function get_all_prices() {
	global $api_url;

	return send_request( $api_url . 'get_all_prices/' );
}

function update_price( $product_id, $cluster_id, $purchased ) {
	global $api_url;

	$postRequest = array(
		'product_id' => $product_id,
		'cluster_id' => $cluster_id,
		'purchased'  => ( $purchased ) ? "true" : "false"
	);

	return send_request( $api_url . 'update_price/', $postRequest );
}