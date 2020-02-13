<?php
namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class AuthController{
  private $db;
  public function __construct($container){
    $this->db = $container->get('db');
  }


  public function register(Request $request,Response $response, $args){
    

    return $response;

  }


}
?>
