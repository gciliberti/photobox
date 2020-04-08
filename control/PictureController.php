<?php

namespace photobox\control;


use photobox\utils\PictureManager;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class PictureController
{
    private $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }

    public function store(Request $request, Response $response, $args)
    {

        $input = $request->getParsedBody();
        $user = $request->getAttribute('token');
        $rawdata = $input["picture"];
        if ($event = $this->db->event->findOne(['token' => $args['eventtoken']])) {

            $event_id = (string)$event->_id;
            $picture = PictureManager::storeEventPicture($event_id, $rawdata);
            $this->db->event->updateOne(
                ["token" => $args['eventtoken']],
                ['$push' => ['pictures' => $picture]]
            );
            $response = Writer::jsonResponse($response, 201, [
                "picture" => $picture,
            ]);
        }


        return $response;
    }

    public function send(Request $request, Response $response)
    {
        $picture = $request->getAttribute("id");
        $picture = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "../uploads/" . $picture);
        $picture = base64_encode($picture);

        $response = Writer::jsonResponse($response, 200, [
            "picture" => $picture,
        ]);
        return $response;
    }

    public function getEventPictures(Request $request, Response $response, $args)
    {
        if ($event = $this->db->event->findOne(["token" => $args['eventtoken']])) {
            $pictures = array();
            if($event->pictures){
                foreach($event->pictures as $picture){
                    array_push($pictures,["id"=>$picture, "URI"=>"/assets/event/".$args['eventtoken'].'/'.$picture]);
                }
                $responsearray["pictures"]=$pictures;
                $response = Writer::jsonResponse($response, 200,$responsearray);
                return $response;
            } else {
                $response = Writer::jsonResponse($response, 404,["error" => "no pictures found"]);
                return $response;
            }

        }
    }

    public function pictureUri(Request $request, Response $response, $args){
        try{
            $event = $this->db->event->findOne(["token"=>$args["event_token"]]);
            $id = (string) $event->_id;
            if(file_exists("../uploads/".$id.'/'.$args['photo_id'])){
                $img = file_get_contents("../uploads/".$id.'/'.$args['photo_id']);
                $response = $response->withStatus(200)->withHeader("Content-Type", "image/png");
                echo $img;
                return $response;
            } else {
                throw new \Exception('Introuvable');
            }


        } catch (\Exception $e){
            $response = Writer::jsonResponse($response, 404,["error"=>"picture not found"]);
            return $response;
        }
    }
    public function getEventLastPicture(Request $request, Response $response, $args)
    {
        if ($event = $this->db->event->findOne(["token" => $args['eventtoken']])) {
            $pictures = array();
            $picture = end($event->pictures);
            $pictures["picture"]=["id"=>$picture, "URI"=>"/assets/event/".$args['eventtoken'].'/'.$picture];
            $response = Writer::jsonResponse($response, 200,$pictures);
            return $response;
        }
    }

}
