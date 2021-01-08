<?php


$app->get('/api/ide/status/{link}', function ($request, $response, $args) {
    if((isset($_COOKIE["auth"]) && !is_authorized($_COOKIE["auth"])) || !isset($_COOKIE["auth"])){
        $response->getBody()->write(json_encode(get_error_arr("not_authorized")));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    $username = getTokenPayload($_COOKIE["auth"]);
    $payload = (make_ide_status_api_request($username, $args['link']));
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

$app->post('/api/ide/run', function ($request, $response, $args) {
    if((isset($_COOKIE["auth"]) && !is_authorized($_COOKIE["auth"])) || !isset($_COOKIE["auth"])){
        $response->getBody()->write(json_encode(get_error_arr("not_authorized")));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
    $body = $request->getParsedBody();
    $username = getTokenPayload($_COOKIE["auth"]);
    $payload = (make_ide_run_api_request($username, $body));
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