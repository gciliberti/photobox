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
        $nom = $args['nom'];
        $user = $this->db->users->find(['nom' => $nom]);
        foreach($user as $utilisateur){
            $array = array();
            $array['id'] = $utilisateur->_id;
            $array['nom'] = $oneuser->nom;
            $array['prenom'] = $oneuser->prenom;
            $array['date_naiss'] = $oneuser->date_naiss;
            $array['tel'] = $oneuser->tel;
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
            $arrayuser['user']['_id'] = (string)$oneuser->_id;
            $arrayuser['user']['nom'] = $oneuser->nom;
            $arrayuser['user']['prenom'] = $oneuser->prenom;
            $arrayuser['user']['date_naiss'] = $oneuser->date_naiss;
            $arrayuser['user']['tel'] = $oneuser->tel;
            $arrayuser['user']['mail'] = $oneuser->mail;
            $arrayuser['user']['mdp'] = $oneuser->mdp;
            $arrayuser['user']['date_insc'] = $oneuser->date_insc;
            $arrayuser['user']['ban_user'] = $oneuser->ban_user;
        }
        //modifier la recherche by(id) ou mail
        $events = $this->db->event->find(['users' => $arrayuser['user']['members']]);
        foreach($events as $event){
            $arrayevent = array();
            $arrayevent['event']['_id'] = $event->_id;
            $arrayevent['event']['name'] = $event->name;
            $arrayevent['event']['date'] = $event->date;
            $arrayevent['event']['location'] = $event->location;
            $arrayevent['event']['public'] = $event->public;
            $arrayevent['event']['description'] = $event->description;
            $arrayevent['event']['token'] = $event->token;
            $arrayevent['event']['members'] = $event->members;
        }
        //var_dump($arrayevent);
        $resp = Writer::jsonResponse($resp,200,['user' => $arrayuser, 'event' => $arrayevent]);
        return $resp;
    }

    public function insertUser(Request $req, Response $resp, array $args){
        $insert = $req->getParsedBody();
        //Hash le pwd d'un utilisateur
        $mdp = password_hash($insert['mdp'], PASSWORD_DEFAULT);
        //$date = new Date('d-m-Y', $insert["date_insc"]);
        $user = array
        (
<<<<<<< HEAD
            'nom' => $insert['nom'], 
            'prenom' => $insert['prenom'], 
            'date_naiss' => $insert['date_naiss'], 
            'tel' => $insert['tel'], 
            'mail' => $insert['mail'],
            'mdp' => $mdp, 
            'date_insc' => $insert['date_insc'], 
=======
            'nom' => $insert['nom'],
            'prenom' => $insert['prenom'],
            'date_naiss' => $insert['date_naiss'],
            'tel' => $insert['tel'],
            'mail' => $insert['mail'],
            'mdp' => $mdp,
            'date_insc' => date("Y-m-d H:i:s"),
>>>>>>> 7f354b7d9fc650f361a41006c86006a77f26491f
            'ban_user' => false
        );

        $create = $this->db->users->insertOne($user);
        $id = $create->getInsertedId();
        $user['id'] = $id;
        $response = Writer::jsonResponse($resp,201,$user);

        return $resp;
    }
}
