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
        $id = $args['id'];
        $user = $this->db->users->find(['_id' => new \MongoDB\BSON\ObjectId("$id")]);
        foreach($user as $utilisateur){
            $array = array();
            $array['_id'] = (string)$utilisateur->_id;
            $array['nom'] = $oneuser->nom;
            $array['prenom'] = $oneuser->prenom;
            $array['date_naiss'] = $oneuser->date_naiss;
            $array['tel'] = $oneuser->tel;
            $array['mail'] = $utilisateur->mail;
            $array['date_insc'] = $utilisateur->date_insc;
        }
        //var_dump($array);
        $resp = Writer::jsonResponse($resp,200,['user' => $array]);
        return $resp;
    }
    /*Récupérer les évènements{id} auxquels le membre a participé*/
    public function getUserEvents(Request $req, Response $resp, array $args){
        $id = $args['id'];
        //var_dump($id);
        $user = $this->db->users->find(['_id' => new \MongoDB\BSON\ObjectId("$id")]);
        foreach($user as $oneuser){
            $arrayuser = array();
            $arrayuser['_id'] = (string)$oneuser->_id;
            $arrayuser['nom'] = $oneuser->nom;
            $arrayuser['prenom'] = $oneuser->prenom;
            $arrayuser['date_naiss'] = $oneuser->date_naiss;
            $arrayuser['tel'] = $oneuser->tel;
            $someone = $arrayuser['mail'] = $oneuser->mail;
        }
        //var_dump($arrayuser);
        
        $events = $this->db->event->find(['members' => $arrayuser['mail']]);
        foreach($events as $event){
            $arrayevent = array();
            $arrayevent['_id'] = $event->_id;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['public'] = $event->public;
            $arrayevent['description'] = $event->description;
            $arrayevent['token'] = $event->token;
            $arrayevent['members'] = $event->members;
            $evenements[] = $arrayevent;
        }
        //var_dump($event);
        $resp = Writer::jsonResponse($resp,200,['member' => $someone, 'events' => $evenements]);
        return $resp;
    }

    public function insertUser(Request $req, Response $resp, array $args){
        $insert = $req->getParsedBody();
        //Hash le pwd d'un utilisateur
        $mdp = password_hash($insert['mdp'], PASSWORD_DEFAULT);
        $user = array
        (
            'nom' => $insert['nom'],
            'prenom' => $insert['prenom'],
            'date_naiss' => $insert['date_naiss'],
            'tel' => $insert['tel'],
            'mail' => $insert['mail'],
            'mdp' => $mdp,
            'date_insc' => date("Y-m-d H:i:s"),
            'ban_user' => false
        );

        $create = $this->db->users->insertOne($user);
        $id = $create->getInsertedId();
        $user['id'] = $id;
        $response = Writer::jsonResponse($resp,201,$user);

        return $resp;
    }
}
