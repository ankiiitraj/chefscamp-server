<?php

$app->get('/api/tags/problems/{userName}', function ($request, $response, $args) {
    $payload = (get_all_tags());
    if(array_key_exists("status", $payload) && $payload["status"] != "OK"){
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json');
});

$app->post('/api/tags/problems/{userName}', function ($request, $response, $args) {
    $tags = $request->getParsedBody();
    $payload = (get_problems_by_tags($args['userName'], $tags['tags']));
    if(array_key_exists("status", $payload) && $payload["status"] != "OK"){
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json');
});