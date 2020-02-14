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
            $array['nom'] = $utilisateur->nom;
            $array['prenom'] = $utilisateur->prenom;
            $array['date_naiss'] = $utilisateur->date_naiss;
            $array['tel'] = $utilisateur->tel;
            $array['mail'] = $utilisateur->mail;
            $array['date_insc'] = $utilisateur->date_insc;
        }
        $resp = Writer::jsonResponse($resp,200,['user' => $array]);
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
        $resp = Writer::jsonResponse($resp,201,$user);
        return $resp;
    }

    public function updateUserProfile(Request $req, Response $resp, array $args){
        $update = $req->getParsedBody();
        $id = $args['id'];
        //var_dump($update, $id);       
        $mdp = password_hash($update['mdp'], PASSWORD_DEFAULT);
        $user = $this->db->users->find(['_id' => new \MongoDB\BSON\ObjectId("$id")]);
        foreach($user as $utilisateur){
            $utilisateur->nom = $update['nom'];
            $utilisateur->prenom = $update['prenom'];
            $utilisateur->mail = $update['mail'];
            $utilisateur->tel = $update['tel'];
            $utilisateur->mdp = $mdp;
        }
        var_dump($utilisateur);
        $maj = $this->db->users->updateOne($utilisateur);
        $resp = Writer::jsonResponse($resp,200,$utilisateur);
        //return $resp;
    }
}
