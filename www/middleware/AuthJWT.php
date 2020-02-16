<?php
namespace photobox\middleware;

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \photobox\utils\Writer;
use \DomainException;


class AuthJWT{
  public function checkToken(Request $request, Response $response, callable $next){
    try{
      $h = $request->getHeader('Authorization')[0];
      $tokenstring = sscanf($h, "Bearer %s")[0];
      //echo $tokenstring;
      $token = JWT::decode($tokenstring,"zrezrerezzrezrezeajior1564",['HS512']);
      $request = $request->withAttribute('mail',$token->mail);

      return $next($request, $response);
    }
    catch(ExpiredException $e){
      return Writer::jsonResponse($response, 401, [
        "type" => "error",
        "error" => 401,
        "message" => "Expired Token"
      ]);

    } catch(SignatureInvalidException $e){
      return Writer::jsonResponse($response, 401, [
        "type" => "error",
        "error" => 401,
        "message" => "Invalid signature"
      ]);

    } catch(BeforeValidException $e){
      return Writer::jsonResponse($response, 401, [
        "type" => "error",
        "error" => 401,
        "message" => "Before Valid exception"
      ]);

    } catch(\UnexpectedValueException $e){
      return Writer::jsonResponse($response, 401, [
        "type" => "error",
        "error" => 401,
        "message" => "Unexpected value"
      ]);

    } catch(DomainException $e){
      return Writer::jsonResponse($response, 401, [
        "type" => "error",
        "error" => 401,
        "message" => "Domain Exception"
      ]);
    }
  }

  public static function generateToken($usermail){
    $token = JWT::encode([
      "iss" => "http://photobox.com",
      "aud" => "http://api.photobox.com",
      "iat" => 1356999524,
      "mail"=>$usermail,
      "nbf" => 1357000000
    ],"zrezrerezzrezrezeajior1564",'HS512');
    return $token;
  }

}
