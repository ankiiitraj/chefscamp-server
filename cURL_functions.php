<?php
function make_api_request($user_name, $path){
    $oauth_details = get_tokens($user_name);
    if(array_key_exists("status", $oauth_details) && $oauth_details["status"] != "OK"){
        return json_encode($oauth_details);
    }
    $headers[] = 'Authorization: Bearer ' . $oauth_details['access_token'];
    return make_curl_request($path, false, $headers);
}

function make_api_post_request($user_name, $path, $payload){
    $oauth_details = get_tokens($user_name);
    if(array_key_exists("status", $oauth_details) && $oauth_details["status"] != "OK"){
        return json_encode($oauth_details);
    }
    $headers[] = 'Authorization: Bearer ' . $oauth_details['access_token'];
    return make_curl_request($path, $payload, $headers);
}

function make_curl_request($url, $post, $headers = array()){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    }

    $headers[] = 'content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return $response;
}