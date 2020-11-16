<?php

function get_config(){
    $config = array('client_id'=> $_ENV["CLIENT_ID"],
    'client_secret' => $_ENV["CLIENT_SECRET"],
    'api_endpoint'=> 'https://api.codechef.com/',
    'authorization_code_endpoint'=> 'https://api.codechef.com/oauth/authorize',
    'access_token_endpoint'=> 'https://api.codechef.com/oauth/token',
    'redirect_uri'=> $_ENV["REDIRECT_URI"],
    'website_base_url' => $_ENV["BASE_URL"]);

    return $config;
}

function get_error_arr($err){
    return array(
        "status" => "error",
        "error" => $err
    );
}