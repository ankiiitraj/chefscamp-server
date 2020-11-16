<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();
$app = AppFactory::create();
$redis = new Predis\Client($_ENV["REDIS_URL"]);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Hi");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/api/auth/', function ($request, $response, $args) {
    if (isset($_GET['code'])) {
        $payload = main($response);
        if($payload['status'] != "OK"){
            $response->getBody()->write(json_encode($payload));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            }
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }else{
        $response->getBody()->write("Code not found");
        return $response
            ->withStatus(500);
    }
});

$app->get('/api/contests/{userName}', function ($request, $response, $args) {
    $payload = make_contests_list_api_request($args['userName']);
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

$app->get('/api/contests/{contestCode}/{userName}', function ($request, $response, $args) {
    $payload = make_contest_details_api_request($args['userName'], $args['contestCode']);
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

$app->get('/api/ide/status/{link}/{userName}', function ($request, $response, $args) {
    $payload = (make_ide_status_api_request($args['userName'], $args['link']));
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

$app->post('/api/ide/run/{userName}', function ($request, $response, $args) {
    $body = $request->getParsedBody();
    $payload = (make_ide_run_api_request($args['userName'], $body));
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

$app->any('{route:.*}', function ($request, $response, $args) {
    $payload = json_encode(
        array(
            "status" => "Not found",
            "message" => "You look lost, consider visiting https://chefscamp.tech"
        )
    );
    $response->getBody()->write($payload);
    return $response
        ->withStatus(404)
        ->withHeader('Content-Type', 'application/json');
});

include "./config.php";
include "./cURL_functions.php";
include "./db_functions.php";
include "./route_functions.php";
include "./redis_functions.php";

//This function is for first time login purpose
function main($response)
{
    $config = get_config();

    $oauth_details = array(
        'authorization_code' => '',
        'access_token' => '',
        'refresh_token' => ''
    );

    $oauth_details['authorization_code'] = $_GET['code'];
    $oauth_details = generate_access_token_first_time($config, $oauth_details, $response);
    if(array_key_exists('status', $oauth_details)){
        return $oauth_details;
    }
    $user_data = json_decode(get_current_user_data($config, $oauth_details));
    $user_data_arr = (array)$user_data;
    if($user_data_arr['status'] != "OK"){
        return $user_data;
    }
    $user_name = $user_data->result->data->content->username;
    update_or_set_user_details_to_db($oauth_details, $user_name);

    update_tokens($user_name, $oauth_details['access_token'], $oauth_details['refresh_token']);

    return $user_data_arr;
}

$app->run();
