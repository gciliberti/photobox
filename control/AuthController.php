<?php
namespace photobox\control;

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class AuthController
{
    private $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }


    public function login(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        $authorization_header = $request->getHeader("Authorization");
        $credentialsb64 = sscanf($authorization_header[0], "Basic %s");
        $crendentials = base64_decode($credentialsb64[0]);
        $crendentials = explode(":", $crendentials);

        if ($user = $this->db->users->findOne(['mail' => $crendentials[0]])) {
            if (password_verify($crendentials[1], $user->mdp)) {

                $token = $token = JWT::encode([
                    "iss" => "https://apiphotobox.tallium.tech/",
                    "aud" => "https://apiphotobox.tallium.tech/",
                    "iat" => 1356999524,
                    "mail" => $user->mail,
                    "nbf" => 1357000000
                ], getenv("JWT_SECRET"), 'HS512');

                $userarray = [
                    "pseudo" => $user->pseudo,
                    "nom" => $user->nom,
                    "prenom" => $user->prenom,
                    "date_naiss" => $user->date_naiss,
                    "tel" => $user->tel,
                    "mail" => $user->mail,
                    "date_insc" => $user->date_insc,
                    "token" => $token
                ];

                $response = Writer::jsonResponse($response, 200, $userarray);
            } else {
                $response = Writer::jsonResponse($response, 401, ["error" => "login mismatch"]);
            }
        }

        return $response;

    }

    public function register(Request $request, Response $response, array $args)
    {
        $insert = $request->getParsedBody();
        if (!$user = $this->db->users->findOne(['mail' => $insert['mail']])) {
            //Hash le pwd d'un utilisateur
            $mdp = password_hash($insert['mdp'], PASSWORD_DEFAULT);
            //$date = new Date('d-m-Y', $insert["date_insc"]);
            $user = array
            (
                'nom' => $insert['nom'],
                'prenom' => $insert['prenom'],
                'pseudo' => $insert['pseudo'],
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
            $response = Writer::jsonResponse($response, 201, $user);

            return $response;

        } else {
            $response = Writer::jsonResponse($response, 401, ["error" => "existing account"]);
        }
        return $response;
    }


}
