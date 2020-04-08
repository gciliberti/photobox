<?php


namespace photobox\control;


use photobox\utils\Writer;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class PlayerController
{
    private $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }

    public function eventAuth(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $pass = $input["pass"];
        if($event = $this->db->event->findOne(['playerpass' => $pass]))
        {
            $token = $event->token;
            $response = Writer::jsonResponse($response, 200, ["token"=>$token]);
            return $response;
        } else {
            $response = Writer::jsonResponse($response, 404, ["error"=>"not found"]);
            return $response;
        }

    }
}