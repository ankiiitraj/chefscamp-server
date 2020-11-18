<?php

function make_contest_ranklist_api_request($user_name, $contest_code)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "rankings/" . $contest_code;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}