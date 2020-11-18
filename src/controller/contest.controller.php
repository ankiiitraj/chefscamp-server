<?php

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