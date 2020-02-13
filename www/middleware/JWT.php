<?php
namespace photobox\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JWT{
  public static function checkToken(Request $request, Response $response, callable $next){
    try{
      $h = $request->getHeader('Authorization')[0];
      $tokenstring = sscanf($h, "Bearer %s")[0];
      $token = JWT::decode($tokenstring,$secret,['HS512']);
    }
    catch(ExpiredException $e){

    } catch(SignatureInvalidException $e){

    } catch(BeforeValidException $e){

    } catch(\UnexpectedValueException $e){

    }
  }

}
