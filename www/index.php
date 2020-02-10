<?php
require 'vendor/autoload.php';

$config = parse_ini_file("conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require './conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

$app_config = array_merge($errors);

$app = new \Slim\App(new \Slim\Container($app_config,$configuration));

/*route test*/
$app->get('/hello/{name}', function (Request $req, Response $resp, $args){
  $name = $args['name'];
  $resp->getBody()->write("Hello, $name");
  return $resp;
});
/*get tous les utilisateurs*/
$app->get('/users[/]', \photobox\control\UserController::class . 'getUsers');
/*ajoute un nouveau user*/
$app->post('/users/{nom}/{mail}', \photobox\control\UserController::class . ':insertUser');
$app->post('/picture', \photobox\control\PictureController::class . ':store');
$app->run();
