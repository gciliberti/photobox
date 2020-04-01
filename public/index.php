<?php
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$mongoclient = new \MongoDB\Client(getenv("MONGO_URI"));
$db = $mongoclient->photobox;

$errors = require '../conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

$app_config = array_merge($errors);

$app = new \Slim\App(new \Slim\Container($app_config,$configuration));
$c = $app->getContainer();
$c['db'] = $db;


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

$app->get('/events',\photobox\control\EventController::class . ':getUserEvents');

$app->get('/events/involved',\photobox\control\EventController::class . ':getUserRegisteredEvents');

$app->post('/login', \photobox\control\AuthController::class . ':login');

$app->run();
