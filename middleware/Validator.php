<?php


namespace photobox\middleware;


use photobox\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Validator
{
    public static function dataFormatErrorHandler(Request $request, Response $response, callable $next) {
        if ($request->getAttribute('has_errors')) {
            return Writer::jsonResponse($response, 400, [
                "type" => "error",
                "error" => 400,
                "message" => "Parametres mal formes.",
                "details" => $request->getAttribute('errors'),
            ]);
        }

        return $next($request, $response);
    }
}