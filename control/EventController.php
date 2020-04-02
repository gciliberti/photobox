<?php
namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class EventController
{
    private $db;

    public function __construct($container)
    {
        $this->db = $container->get('db');
    }

    public function create(Request $request, Response $response)
    {
        $user = $request->getAttribute('token');
        $input = $request->getParsedBody();
        $event = [
            "author" => $user['mail'],
            "name" => $input['name'],
            "date" => $input['date'],
            "location" => $input['location'],
            "public" => $input['public'],
            "description" => $input['description'],
            "members" => array($user['mail']),
            "token" => Writer::generateToken(),
        ];

        $insert = $this->db->event->insertOne($event);
        $id = $insert->getInsertedId();
        $event['id'] = (string)$id;
        $response = Writer::jsonResponse($response, 201, $event);

        return $response;
    }

    public function getEventwithId(Request $request, Response $response, $args)
    {
        $event_id = $request->getAttribute('id');
        $usermail = $request->getAttribute('mail');
        $event = $this->db->event
            ->findOne(['_id' => new \MongoDB\BSON\ObjectId("$event_id")]);
        $eventarray = [
            "id" => (string)$event->_id,
            "name" => $event->name,
            "date" => $event->date,
            "location" => $event->location,
            "public" => $event->public,
            "description" => $event->description,
            "token" => $event->token,
        ];
        $response = Writer::jsonResponse($response, 200, $eventarray);

        return $response;

    }

    public function getUserEvents(Request $request, Response $response, $args)
    {
        $usermail = $request->getAttribute('mail');
        $events = $this->db->event->find(['author' => $usermail]);
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['public'] = $event->public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, $evenements);
        return $response;
    }

    public function getUserRegisteredEvents(Request $request, Response $response, $args)
    {
        $usermail = $request->getAttribute('mail');
        $events = $this->db->event->find(['members' => $usermail]);
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['public'] = $event->public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, $evenements);
        return $response;
    }

    public function joinPublicEvent(Request $request, Response $response, $args){
        $user = $request->getAttribute('token');
        $eventtoken = $args["token"];

        if($event = $this->db->event->find(['token' => $eventtoken]))
        {
            $event->members->$push;
        }

    }


}

?>
