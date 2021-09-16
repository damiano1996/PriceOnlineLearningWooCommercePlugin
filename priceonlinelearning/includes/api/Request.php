<?php

class Request
{
    public static function send_request($url, $postRequest = array(), $delete = false)
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
}