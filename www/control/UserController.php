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
            $array = array();
            $array["pseudo"] = $user->pseudo;
            $array["mail"] = $user->mail;
        }
        //var_dump($users);
        //die("debug");
        $resp = Writer::jsonResponse($resp,200,["users" => $array]);
        return $resp;
    }

    public function getUser(Request $req, Response $resp, array $args){
        $pseudo = $args["pseudo"];
        $user = $this->db->users->find(["pseudo" => $pseudo]);
        foreach($user as $utilisateur){
            $array = array();
            $array["id"] = $utilisateur->id;
            $array["pseudo"] = $utilisateur->pseudo;
            $array["mail"] = $utilisateur->mail;
            $array["date_insc"] = $utilisateur->date_insc;
        }
        $resp = Writer::jsonResponse($resp,200,["user" => $array]);
        return $resp;
    }

    public function insertUser(Request $req, Response $resp, array $args){
        $insert = $req->getParsedBody();
        //Hash le pwd d'un utilisateur
        $mdp = password_hash($insert["mdp"], PASSWORD_DEFAULT);
        $date = date_create_from_format('d-m-Y h:i', $insert["date_insc"]);
        $user = array
        (
            "pseudo" => $insert["pseudo"], "mail" => $insert["mail"],
            "mdp" => $mdp, "date_insc" => $date, "ban_user" => $insert["ban_user"]
        );
        var_dump($user);

        $create = $this->db->users->insertOne($user);
        $id = $create->getInsertedId();
        $user['id'] = $id;
        $response = Writer::jsonResponse($resp,201,$user);

        return $resp;
    }

    
}