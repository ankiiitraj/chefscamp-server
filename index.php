<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

if (getenv("HTTP_MODE") === 'DEV') {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
$app = AppFactory::create();
$redis = new Predis\Client($_ENV["REDIS_URL"]);


$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

require "./src/routes/tags.php";
require "./src/routes/ide.php";
require "./src/routes/problem.php";
require "./src/routes/contest.php";
require "./src/routes/leaderboard.php";

$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write("Hi");
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/api/auth/', function ($request, $response, $args) {
    if (isset($_GET['code'])) {
        $payload = main();
        if($payload['status'] != "OK"){
            $response->getBody()->write(json_encode($payload));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            }
        $token = createToken($args["username"]);
        setcookie("auth", $token, time() + (60 * 60 * 24 * 365), "/");
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

include "./src/util/config.php";
include "./src/util/auth.php";
include "./src/util/cURL_functions.php";
include "./src/util/db_functions.php";
include "./src/util/route_functions.php";
include "./src/util/redis_functions.php";
include "./src/controller/tags.controller.php";
include "./src/controller/ide.controller.php";
include "./src/controller/problem.controller.php";
include "./src/controller/contest.controller.php";
include "./src/controller/leaderboard.controller.php";

//This function is for first time login purpose
function main()
{
    $config = get_config();

    $oauth_details = array(
        'authorization_code' => '',
        'access_token' => '',
        'refresh_token' => ''
    );

    $oauth_details['authorization_code'] = $_GET['code'];
    $oauth_details = generate_access_token_first_time($config, $oauth_details);
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
