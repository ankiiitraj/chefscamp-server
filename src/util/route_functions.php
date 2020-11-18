<?php

// Return config details whereever required
function get_current_user_data($config, $oauth_details)
{
    $path = $config['api_endpoint'] . "users/me";
    $headers[] = 'Authorization: Bearer ' . $oauth_details['access_token'];
    return make_curl_request($path, false, $headers);
}

//Generated new access token with the refresh token and returns oauth details
function generate_access_token_from_refresh_token($oauth_details)
{
    $config = get_config();
    $oauth_config = array(
        'grant_type' => 'refresh_token',
        'refresh_token' => $oauth_details['refresh_token'],
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret']
    );
    $response = (array)json_decode(make_curl_request($config['access_token_endpoint'], $oauth_config), true);
    if($response['status'] != "OK"){
        return $response;
    }
    $result = $response['result']['data'];

    $oauth_details['access_token'] = $result['access_token'];
    $oauth_details['refresh_token'] = $result['refresh_token'];
    $oauth_details['scope'] = $result['scope'];

    return $oauth_details;
}

//generates access token for the first time
function generate_access_token_first_time($config, $oauth_details)
{
    $oauth_config = array(
        'grant_type' => 'authorization_code', 'code' => $oauth_details['authorization_code'], 'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'], 'redirect_uri' => $config['redirect_uri']
    );
    $response = (array)json_decode(make_curl_request($config['access_token_endpoint'], $oauth_config), true);
    if($response['status'] != "OK"){
        return $response;
    }
    $result = $response['result']['data'];

    $oauth_details['access_token'] = $result['access_token'];
    $oauth_details['refresh_token'] = $result['refresh_token'];
    $oauth_details['scope'] = $result['scope'];

    return $oauth_details;
}
