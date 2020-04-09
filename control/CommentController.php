<?php

namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class CommentController
{
    private $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }

    /*public function getEventLastPicture(Request $request, Response $response, $args)
    {
        if ($event = $this->db->event->findOne(["token" => $args['eventtoken']])) {
            $pictures = array();
            $picture = end($event->pictures);
            $pictures["picture"]=["id"=>$picture, "URI"=>"/assets/event/".$args['eventtoken'].'/'.$picture];
            $response = Writer::jsonResponse($response, 200,$pictures);
            return $response;
        }
    }*/

    public function addCommentEvent(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        $eventToken = $request->getAttribute('eventtoken');

        if(isset($input['comment'])){
            $user = $request->getAttribute('token');
            $userId= $user['id'];
            $userInfos = $this->db->users->findOne(["_id"=> new \MongoDB\BSON\ObjectId($userId)]); //on recupere les infos de l'user
            $pseudoUser = $userInfos["pseudo"];
            $comment = [];
            $comment['pseudo'] = $pseudoUser;
            $comment['comment'] = $input['comment'];
            $comment['date'] = date('d/m/Y G:i:s');

            $this->db->event->updateOne(["token" => $eventToken],['$push' => ['comments' => $comment]]);

            $response = Writer::jsonResponse($response, 201, $comment);
            }else{
            $resp = [
                "type" => "erreur",
                "error" => 400,
                "message" => "Le paramètre comment est manquant"
            ];
            $response = Writer::jsonResponse($response, 401, $resp);
        }
        return $response;
    }

    public function getEventLastComment(Request $request, Response $response)
    {
        $eventToken = $request->getAttribute('eventtoken');
        $user = $request->getAttribute('token');
        $userId= $user['id'];
        $isMember = $this->db->event->count(['token' => $eventToken, 'members'=>$userId]);
        if($isMember){
            $rep = $this->db->event->findOne(["token"=> $eventToken], ['projection' => ['comments' => 1]]); //on recupere le dernier comment
            $lastcomment = end($rep->comments);
            $response = Writer::jsonResponse($response, 200, $lastcomment);
        }else{
            $resp = [
                "type" => "erreur",
                "error" => 401,
                "message" => "L'utilisateur ne fais pas parti de l'event"
            ];
            $response = Writer::jsonResponse($response, 401, $resp);
        }

        return $response;
    }

    public function getEventComments(Request $request, Response $response)
    {
        $eventToken = $request->getAttribute('eventtoken');
        $user = $request->getAttribute('token');
        $userId= $user['id'];
        $isMember = $this->db->event->count(['token' => $eventToken, 'members'=>$userId]);
        if($isMember){
            $comments = $this->db->event->findOne(["token"=> $eventToken], ['projection' => ['comments' => 1]]); //on recupere le dernier comment
            $rep = $comments->comments;
            $response = Writer::jsonResponse($response, 200, $rep);
        }else{
            $resp = [
                "type" => "erreur",
                "error" => 401,
                "message" => "L'utilisateur ne fais pas parti de l'event"
            ];
            $response = Writer::jsonResponse($response, 401, $resp);
        }

        return $response;
    }

    public function getPlayerEventLastComment(Request $request, Response $response)
    {
        $eventToken = $request->getAttribute('eventtoken');
            $rep = $this->db->event->findOne(["token"=> $eventToken], ['projection' => ['comments' => 1]]); //on recupere le dernier comment
            $lastcomment = end($rep->comments);
            $response = Writer::jsonResponse($response, 200, $lastcomment);

        return $response;
    }

    public function getPlayerEventComments(Request $request, Response $response)
    {
        $eventToken = $request->getAttribute('eventtoken');
            $comments = $this->db->event->findOne(["token"=> $eventToken], ['projection' => ['comments' => 1]]); //on recupere le dernier comment
            $rep = $comments->comments;
            $response = Writer::jsonResponse($response, 200, $rep);


        return $response;
    }

}
