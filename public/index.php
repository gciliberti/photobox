<?php

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$mongoclient = new \MongoDB\Client(getenv("MONGO_URI"));
$db = $mongoclient->photobox;

$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);
$c = $app->getContainer();
$c['db'] = $db;

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "ignore" => ["/login", "/register","/event/pictures","/assets/event/","/player","/event/picture/last"],
    "secret" => getenv("JWT_SECRET"),
]));

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'https://apiphotobox.tallium.tech/*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



/*get tous les utilisateurs*/
$app->get('/users[/]', \photobox\control\UserController::class . ':getUsers');

/*Get un user en particulier*/
$app->get('/user/{pseudo}[/]', \photobox\control\UserController::class . ':getUser');

$app->put('/user[/]', \photobox\control\UserController::class . ':editProfile');

/*get les évènements auxquels un user a participé*/
$app->get('/users/{id}/{events}[/]', \photobox\control\UserController::class . ':getUserEvents');

/*ajoute un nouveau user*/
$app->post('/register[/]', \photobox\control\AuthController::class . ':register');


//Get une image avec son ID
$app->get('/picture/{id}', \photobox\control\PictureController::class . ':send');

$app->get('/assets/event/{event_token}/{photo_id}', \photobox\control\PictureController::class . ':pictureUri');

$app->post('/picture/event/{eventtoken}', \photobox\control\PictureController::class . ':store');

$app->post('/event', \photobox\control\EventController::class . ':create');

$app->delete('/event/{eventToken}', \photobox\control\EventController::class . ':deleteEvent');

$app->get('/event/pictures/{eventtoken}', \photobox\control\PictureController::class . ':getEventPictures');

$app->get('/event/picture/last/{eventtoken}', \photobox\control\PictureController::class . ':getEventLastPicture');

$app->get('/events/history[/]', \photobox\control\EventController::class . ':getHistory');

$app->get('/event/{id}', \photobox\control\EventController::class . ':getEventwithId');

$app->get('/events',\photobox\control\EventController::class . ':getPublicEvents');

$app->post('/event/join/{eventtoken}',\photobox\control\EventController::class . ':joinPublicEvent');

$app->get('/events/involved',\photobox\control\EventController::class . ':getUserRegisteredEvents');

$app->get('/events/created',\photobox\control\EventController::class . ':getEventCreated');

$app->post('/login', \photobox\control\AuthController::class . ':login');

$app->post('/player/auth', \photobox\control\PlayerController::class . ':eventAuth');

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
