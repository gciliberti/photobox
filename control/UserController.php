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

    public function getUsers(Request $request, Response $response, array $args){
        $users = $this->db->users->find([]);
        foreach($users as $user){
            $array = array();
            $array['pseudo'] = $user->pseudo;
            $array['mail'] = $user->mail;
            $response = Writer::jsonResponse($response,200,['users' => $array]);
        }
        return $response;
    }

    public function getUser(Request $request, Response $response, array $args){
        $pseudo = $args['pseudo'];
        $user = $this->db->users->find(['pseudo' => $pseudo]);
        foreach($user as $utilisateur){
            $array = array();
            $array['id'] = $utilisateur->_id;
            $array['pseudo'] = $utilisateur->pseudo;
            $array['mail'] = $utilisateur->mail;
            $array['date_insc'] = $utilisateur->date_insc;
        }
        $response = Writer::jsonResponse($response,200,['user' => $array]);
        return $response;
    }

    public function getUserEvents(Request $request, Response $response, array $args){
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

        $response = Writer::jsonResponse($response,200,['user' => $arrayuser]);
        return $response;
    }

}
