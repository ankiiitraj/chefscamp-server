<?php

$app->get('/api/rankings/{contestCode}', function ($request, $response, $args) {
    if((isset($_COOKIE["auth"]) && !is_authorized($_COOKIE["auth"])) || !isset($_COOKIE["auth"])){
        $response->getBody()->write(json_encode(get_error_arr("not_authorized")));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    $username = getTokenPayload($_COOKIE["auth"]);
    $payload = make_contest_ranklist_api_request($username, $args['contestCode']);
    if(array_key_exists("status", $payload) && $payload["status"] != "OK"){
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withStatus(200)
        ->withHeader('Cache-Control', 'public, max-age=60')
        ->withHeader('Content-Type', 'application/json');
});