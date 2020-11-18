<?php

function get_all_tags(){
    $tags = make_curl_request("https://www.codechef.com/get/tags/problems", false);
    $tags = json_decode($tags, true);    
    return $tags;
}

function get_private_tags($username){

}

function add_private_tags($username, $problemCode, $tags){

}

function get_problems_by_private_tags($username, $tags){

}

function get_problems_by_tags($user_name, $tags){
    $config = get_config();
    $filter="";
    foreach($tags as $tag){
        $filter .= ($tag . ",");
    }
    $filter = substr($filter, 0, -1);
    $path = $config['api_endpoint'] . "tags/problems?filter=". $filter;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}