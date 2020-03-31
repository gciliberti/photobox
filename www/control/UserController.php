<?php
namespace photobox\control;

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;
use \photobox\middleware\AuthJWT;
use \Firebase\JWT\JWT;

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
            $array['nom'] = $user->nom;
            $array['prenom'] = $user->prenom;
            $array['mail'] = $user->mail;
            $resp = Writer::jsonResponse($resp,200,['users',['nom' => $user->nom, 'prenom'=>$user->prenom, 'mail'=>$user->mail]]);
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
            $array['token'] = $utilisateur->token;
        }
        $resp = Writer::jsonResponse($resp,200,['user' => $array]);
        return $resp;
    }

    public function insertUser(Request $req, Response $resp, array $args){
        $insert = $req->getParsedBody();
        //Hash le pwd d'un utilisateur
        $mdp = password_hash($insert['mdp'], PASSWORD_DEFAULT);
        $token = AuthJWT::generateToken($insert['mail']);
        $user = array
        (
            'nom' => $insert['nom'],
            'prenom' => $insert['prenom'],
            'date_naiss' => $insert['date_naiss'],
            'tel' => $insert['tel'],
            'mail' => $insert['mail'],
            'mdp' => $mdp,
            'date_insc' => date("Y-m-d H:i:s"),
            'ban_user' => false,
            "token" => $token
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

        $mdp = password_hash($update['mdp'], PASSWORD_DEFAULT);
        
        $updateResult = $this->db->users->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId("$id")],
            [ '$set' => [ 'nom' => $update['nom'], 'prenom' => $update['prenom'], 'mail' => $update['mail'],
                        'tel' => $update['tel'], 'mdp' => $mdp ]]
        );
        printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
        printf("Modified %d document(s)\n", $updateResult->getModifiedCount());

        $resp = Writer::jsonResponse($resp,200,"Profil mis à jours.");
        return $resp;
    }
}
