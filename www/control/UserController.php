<?php
namespace photobox\control;

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\model\User as user;

class UserController 
{
    protected $c;

    public function __construct(\Slim\Container $c = null){
        $this->c = $c;
    }

    public function getUsers(Request $req, Response $resp, array $args){
        try {
            $users = user::all();
            $count = user::all()->count();
            $rs = $resp->withStatus(201)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "count" => $count,
                "users" => $users]));
                return $rs;
        }catch (\Exception $e){
            return Writer::json_error($rs, 404, $e->getMessage());
        }
    }
}