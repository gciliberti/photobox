<?php
namespace photobox\control;

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \photobox\middleware\AuthJWT;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class AuthController{
  private $db;
  public function __construct($container){
    $this->db = $container->get('db');
  }


  public function login(Request $request,Response $response, $args){
    $input = $request->getParsedBody();

    $user = $this->db->users->findOne(['mail'=> $input['mail']]);
    if(password_verify($input['mdp'],$user->mdp)){

      $token = AuthJWT::generateToken($input['mail']);

      $response = Writer::jsonResponse($response,200,["token"=>$token]);
    }else{
      $response = Writer::jsonResponse($response,401,["error"=>"login mismatch"]);
    }

    return $response;

  }


}
?>
