<?php

$app->get('/api/contests/{contestCode}/problems/{problemCode}/{userName}', function ($request, $response, $args) {
    $payload = make_contest_problem_api_request($args['userName'], $args['problemCode'], $args['contestCode']);
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

$app->get('/api/submissions/{problemId}/{userName}', function ($request, $response, $args) {
    $payload = (make_submissions_api_request($args['userName'], $args['problemId']));
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