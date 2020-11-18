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