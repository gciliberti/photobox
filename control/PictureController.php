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

}
