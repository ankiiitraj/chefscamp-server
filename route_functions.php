<?php

function make_ide_run_api_request($user_name, $payload)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "ide/run";
    $response = (array)json_decode(make_api_post_request($user_name, $path, $payload));
    return $response;
}

function make_ide_status_api_request($user_name, $link)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "ide/status?link=" . $link;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

function make_submissions_api_request($user_name, $problem_ID)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "submissions/?result=AC&problemCode=" . $problem_ID;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

function make_contest_problem_api_request($user_name, $problem_code, $contest_code)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "contests/" . $contest_code . "/problems/" . $problem_code;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

function make_contest_ranklist_api_request($user_name, $contest_code)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "rankings/" . $contest_code;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

function make_contests_list_api_request($user_name)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "contests";
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

function make_contest_details_api_request($user_name, $contest_code)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "contests/" . $contest_code;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}

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
