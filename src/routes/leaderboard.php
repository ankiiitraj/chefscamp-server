<?php

$app->get('/api/rankings/{contestCode}/{userName}', function ($request, $response, $args) {
    $payload = make_contest_ranklist_api_request($args['userName'], $args['contestCode']);
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