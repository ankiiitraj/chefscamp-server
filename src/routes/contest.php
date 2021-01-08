<?php

$app->get('/api/contests', function ($request, $response, $args) {
    if((isset($_COOKIE["auth"]) && !is_authorized($_COOKIE["auth"])) || !isset($_COOKIE["auth"])){
        $response->getBody()->write(json_encode(get_error_arr("not_authorized")));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    $username = getTokenPayload($_COOKIE["auth"]);
    $payload = make_contests_list_api_request($username);
    if(array_key_exists("status", $payload) && $payload["status"] != "OK"){
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withStatus(200)
        ->withHeader('Cache-Control', 'public, max-age=86400')
        ->withHeader('Content-Type', 'application/json');
});

$app->get('/api/contests/{contestCode}', function ($request, $response, $args) {
    if((isset($_COOKIE["auth"]) && !is_authorized($_COOKIE["auth"])) || !isset($_COOKIE["auth"])){
        $response->getBody()->write(json_encode(get_error_arr("not_authorized")));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    $username = getTokenPayload($_COOKIE["auth"]);
    $payload = make_contest_details_api_request($username, $args['contestCode']);
    if(array_key_exists("status", $payload) && $payload["status"] != "OK"){
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withStatus(200)
        ->withHeader('Cache-Control', 'public, max-age=604800')
        ->withHeader('Content-Type', 'application/json');
});