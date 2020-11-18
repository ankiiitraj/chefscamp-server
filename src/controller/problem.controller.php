<?php

function make_contest_problem_api_request($user_name, $problem_code, $contest_code)
{
    $config = get_config();
    $path = $config['api_endpoint'] . "contests/" . $contest_code . "/problems/" . $problem_code;
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
