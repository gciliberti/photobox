<?php
namespace photobox\control;

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class UserController 
{
    protected $db;

    public function __construct($container){
        $this->db = $container->get('db');
    }

    public function getUsers(Request $req, Response $resp, array $args){
        
        $users = $this->db->users->find([]);
        foreach($users as $user){
          return $user->pseudo." ".$user->mail;
        }
        //var_dump($users);
        //die("getUsers");
        
        $resp = Writer::jsonResponse($resp,200,["users" => $users]);
        return $resp;
    }

    public function createUser(Request $req, Response $resp, array $args){

    }
}