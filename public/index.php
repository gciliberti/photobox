<?php
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
    "ignore" => ["/login", "/register"],
    "secret" => getenv("JWT_SECRET"),
]));


/*get tous les utilisateurs*/
$app->get('/users[/]', \photobox\control\UserController::class . ':getUsers');

/*Get un user en particulier*/
$app->get('/user/{pseudo}[/]', \photobox\control\UserController::class . ':getUser');

/*get les évènements auxquels un user a participé*/
$app->get('/users/{id}/{events}[/]', \photobox\control\UserController::class . ':getUserEvents');

/*ajoute un nouveau user*/
$app->post('/register[/]', \photobox\control\AuthController::class . ':register');

//Ajouter une image (depuis un string b64)
$app->post('/picture', \photobox\control\PictureController::class . ':store');

//Get une image avec son ID
$app->get('/picture/{id}', \photobox\control\PictureController::class . ':send');

$app->post('/event', \photobox\control\EventController::class . ':create');

$app->get('/event/{id}', \photobox\control\EventController::class . ':getEventwithId');

$app->get('/events',\photobox\control\EventController::class . ':getPublicEvents');

$app->post('/event/join/{eventtoken}',\photobox\control\EventController::class . ':joinPublicEvent');

$app->get('/events/involved',\photobox\control\EventController::class . ':getUserRegisteredEvents');

$app->get('/events/created',\photobox\control\EventController::class . ':getEventCreated');

$app->post('/login', \photobox\control\AuthController::class . ':login');

$app->run();
