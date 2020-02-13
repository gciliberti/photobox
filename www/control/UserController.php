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
            $array['pseudo'] = $user->pseudo;
            $array['mail'] = $user->mail;
            $resp = Writer::jsonResponse($resp,200,['users' => $array]);
        }
        return $resp;
    }

    public function getUser(Request $req, Response $resp, array $args){
        $pseudo = $args['pseudo'];
        $user = $this->db->users->find(['pseudo' => $pseudo]);
        foreach($user as $utilisateur){
            $array = array();
            $array['id'] = $utilisateur->_id;
            $array['pseudo'] = $utilisateur->pseudo;
            $array['mail'] = $utilisateur->mail;
            $array['date_insc'] = $utilisateur->date_insc;
        }
        $resp = Writer::jsonResponse($resp,200,['user' => $array]);
        return $resp;
    }

    public function getUserEvents(Request $req, Response $resp, array $args){
        $id = $args['id'];
        //var_dump($id);
        $user = $this->db->users->find(['_id' => new \MongoDB\BSON\ObjectId("$id")]);
        foreach($user as $oneuser){
            $arrayuser = array();
            $arrayuser['_id'] = (string)$oneuser->_id;
            $arrayuser['pseudo'] = $oneuser->pseudo;
            $arrayuser['mail'] = $oneuser->mail;
            $arrayuser['mdp'] = $oneuser->mdp;
            $arrayuser['date_insc'] = $oneuser->date_insc;
            $arrayuser['ban_user'] = $oneuser->ban_user;
        }

        $event = $this->db->event->find(['users' => $arrayuser["event_token"]["name"]]);

        $resp = Writer::jsonResponse($resp,200,['user' => $arrayuser]);
        return $resp;
    }

    public function insertUser(Request $req, Response $resp, array $args){
        $insert = $req->getParsedBody();
        //Hash le pwd d'un utilisateur
        $mdp = password_hash($insert['mdp'], PASSWORD_DEFAULT);
        //$date = new Date('d-m-Y', $insert["date_insc"]);
        $user = array
        (
            'nom' => $insert['nom'], 'prenom' => $insert['prenom'], 'date_naiss' => $insert['date_naiss'], 'tel' => $insert['tel'], 'mail' => $insert['mail'],
            'mdp' => $mdp, 'date_insc' => $insert['date_insc'], 'ban_user' => false
        );

        $create = $this->db->users->insertOne($user);
        $id = $create->getInsertedId();
        $user['id'] = $id;
        $response = Writer::jsonResponse($resp,201,$user);

        return $resp;
    }
}