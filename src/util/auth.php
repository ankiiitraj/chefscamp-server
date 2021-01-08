<?php
use \Firebase\JWT\JWT;

function is_authorized($jwt){
    $key = $_ENV["JWT_KEY"];
    try{
        $payload = JWT::decode($jwt, $key, array('HS256'));
        $payload = (array) $payload;
        return true;
    }catch(Exception $e){
        setcookie("auth", $jwt, time() - 3600, "/");
        setcookie("userName", "", time() - 3600, "/");
        return false;
    }
}

function getTokenPayload($jwt){
    $key = $_ENV["JWT_KEY"];
    $payload = JWT::decode($jwt, $key, array('HS256'));
    $payload = (array) $payload;
    return $payload["username"];
}

function createToken($username){
    $key = $_ENV["JWT_KEY"];
    $payload = [
        "username" => $username,
        "iat" => time(),
    ];
    $jwt = JWT::encode($payload, $key);
    return $jwt;
}