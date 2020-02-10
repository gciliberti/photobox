<?php
namespace photobox\utils;

use \Psr\Http\Message\ResponseInterface as Response;

class Writer {
    public static function jsonResponse(Response $response, $status, $json_array) {
        $response = $response->withStatus($status)
            ->withHeader("Content-Type", "application/json;charset=utf-8");

        $response->getBody()
            ->write(json_encode($json_array));

        return $response;
    }

    public static function generateToken() {
        $token = openssl_random_pseudo_bytes(32);
        $token = bin2hex($token);
        return $token;
    }
}
