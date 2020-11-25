<?php

// Gets public tags
$app->get('/api/tags', function ($request, $response, $args) {
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
        ->withHeader('Cache-Control', 'public, max-age=604800')
        ->withHeader('Content-Type', 'application/json');
});

//Gets private tags
// Needs Signin
$app->get('/api/tags/my/{username}', function ($request, $response, $args) {
    $payload = (get_private_tags($args['username']));
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

// Gets problemCode with the tags
// Do not need to be looged in
$app->get('/api/tags/problems/{username}', function ($request, $response, $args) {
    $tags = $request->getQueryParams();
    $payload = get_problems_by_tags($args['username'], $tags['filter'], $tags['offset']);
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

// Gets problemCode with the private tags
$app->post('/api/tags/my/problems/{username}', function ($request, $response, $args) {
    
});

// Create/Add a private tag to a problem
$app->post('/api/tags/{username}', function ($request, $response, $args) {
    $body = $request->getParsedBody();
    
    $tag = create_private_tag($args['username'], $body['tag']);
    if(array_key_exists('status', $tag) && $tag['status'] != "OK"){
        if(array_key_exists('error', $tag) && $tag['error'] == "Tag already exists"){
            $problem = add_tag_to_problem($args['username'], $body['problemCode'], $body['tag']);
            if(array_key_exists("status", $problem) && $problem["status"] != "OK"){
                $response->getBody()->write(json_encode($problem));
                return $response
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'application/json');
            }
            $response->getBody()->write(json_encode(["status"=>"OK", "message"=>"Tag added"]));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($tag));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    
    $problem = add_tag_to_problem($args['username'], $body['problemCode'], $body['tag']);
    if(array_key_exists("status", $problem) && $problem["status"] != "OK"){
        $response->getBody()->write(json_encode($problem));
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
    $response->getBody()->write(json_encode($tag));
    return $response
        ->withStatus(200)
        ->withHeader('Content-Type', 'application/json');
});