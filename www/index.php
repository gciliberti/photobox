<?php
require 'vendor/autoload.php';

$config = parse_ini_file("conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();

$mongoclient = new \MongoDB\Client("mongodb://mongo_photobox");
$db = $mongoclient->photobox;

$errors = require './conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

$app_config = array_merge($errors);

$app = new \Slim\App(new \Slim\Container($app_config,$configuration));
$c = $app->getContainer();
$c['db'] = $db;

/*route test*/
$app->get('/hello/{name}', function (Request $req, Response $resp, $args){
  $name = $args['name'];
  $resp->getBody()->write("Hello, $name");
  return $resp;
});
/*get tous les utilisateurs*/
$app->get('/users[/]', \photobox\control\UserController::class . ':getUsers');

/*Get un user en particulier*/
$app->get('/user/{id}[/]', \photobox\control\UserController::class . ':getUser');

/*get les évènements auxquels un user a participé*/
$app->get('/user/{id}/{events}[/]', \photobox\control\UserController::class . ':getUserEvents');

/*ajoute un nouveau user*/
$app->post('/user[/]', \photobox\control\UserController::class . ':insertUser');

//Ajouter une image (depuis un string b64)
$app->post('/picture', \photobox\control\PictureController::class . ':store');

//Get une image avec son ID
$app->get('/picture/{id}', \photobox\control\PictureController::class . ':send');

$app->post('/event', \photobox\control\EventController::class . ':create')
->add(\photobox\middleware\AuthJWT::class . ":checkToken");

$app->get('/event/{id}', \photobox\control\EventController::class . ':getEventwithId')
->add(\photobox\middleware\AuthJWT::class . ":checkToken");

$app->get('/events',\photobox\control\EventController::class . ':getUserEvents')
->add(\photobox\middleware\AuthJWT::class . ":checkToken");

$app->get('/events/involved',\photobox\control\EventController::class . ':getUserRegisteredEvents')
->add(\photobox\middleware\AuthJWT::class . ":checkToken");

$app->post('/login', \photobox\control\AuthController::class . ':login');

$app->run();
