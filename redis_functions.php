<?php

function update_tokens($username, $access_token, $refresh_token){
    global $redis;
    $tokens = json_encode(array('access_token' => $access_token, 'refresh_token' => $refresh_token));
    $redis->set($username, $tokens);
    $redis->expire($username, 60*58);
}

function get_tokens($username){
    global $redis;
    if($redis->exists($username) == 1){
        $tokens = (array)json_decode($redis->get($username));
        return $tokens;
    }
    $oauth_details = get_oauth_details_from_db($username);
    if(array_key_exists("status", $oauth_details) && $oauth_details["status"] != "OK"){
        return $oauth_details;
    }
    $oauth_details = generate_access_token_from_refresh_token($oauth_details);
    if(array_key_exists("status", $oauth_details) && $oauth_details["status"] != "OK"){
        return $oauth_details;
    }
    $db_message = update_or_set_user_details_to_db($oauth_details, $username);
    if(array_key_exists("status", $db_message) && $db_message["status"] != "OK"){
        return $db_message;
    }
    update_tokens($username, $oauth_details['access_token'], $oauth_details['refresh_token']);
    
    $tokens = (array)json_decode($redis->get($username)); 
    return $tokens;
}