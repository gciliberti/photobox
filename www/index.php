<?php
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'vendor/autoload.php';
$app = new \Slim\App;
$app->get('/hello/{name}',
function (Request $req, Response $resp, $args)
{
  $name = $args['name'];
  $resp->getBody()->write("Hello, $name");
  return$resp;
});
$app->run();
