<?php
namespace photobox\middleware;

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;

class AuthJWT{
  public static function checkToken(Request $request, Response $response, callable $next){
    try{
      $h = $request->getHeader('Authorization')[0];
      $tokenstring = sscanf($h, "Bearer %s")[0];
      $token = JWT::decode($tokenstring,"zrezrerezzrezrezeajior1564",['HS512']);

      return true;
    }
    catch(ExpiredException $e){

    } catch(SignatureInvalidException $e){

    } catch(BeforeValidException $e){

    } catch(\UnexpectedValueException $e){

    }
  }

  public static function generateToken($usermail){
    $token = JWT::encode([
      "iss" => "http://example.org",
      "aud" => "http://example.com",
      "iat" => 1356999524,
      "mail"=>$usermail,
      "nbf" => 1357000000
    ],"zrezrerezzrezrezeajior1564",'HS512');
    return $token;
  }

}
