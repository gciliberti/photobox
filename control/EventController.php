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
        $playerpass = bin2hex(random_bytes(4));
        $event = [
            "author" => $user['id'],
            "name" => $input['name'],
            "date" => $input['date'],
            "location" => $input['location'],
            "is_public" => $input['is_public'],
            "description" => $input['description'],
            "members" => array($user['id']),
            "status"=> "prochainement",
            "playerpass"=> $playerpass,
            "token" => Writer::generateToken(),
        ];

        if($event['is_public']==false){
            $event['eventpass']=$playerpass = bin2hex(random_bytes(4));
        }

        $insert = $this->db->event->insertOne($event);
        $id = $insert->getInsertedId();
        $event['id'] = (string)$id;
        $response = Writer::jsonResponse($response, 201, $event);

        return $response;
    }

    public function deleteEvent(Request $request, Response $response)
    {
        $user = $request->getAttribute('token');
        $userId= $user['id'];
        $eventToken = $request->getAttribute('eventToken');
        $isOwner = $this->db->event->findOne(['author' => $userId,'token' => $eventToken]);
        if(isset($isOwner)){// si l'user est bien le proprietaire on supprime l'event
            $event = $this->db->event->deleteOne(['token'=>$eventToken]);
            $response = Writer::jsonResponse($response, 204, $event);
        }else{
            $resp = [
                "type" => "erreur",
                "error" => 401,
                "message" => "unauthorized"
            ];
            $response = Writer::jsonResponse($response, 401, $resp);
        }

        return $response;
    }

    public function updateEvent(Request $request, Response $response)
    {
        $input = $request->getParsedBody();
        if(isset($input['name']) && isset($input['date']) && isset($input['location']) && isset($input['description'])){
            $user = $request->getAttribute('token');
            $userId= $user['id'];
            $eventToken = $request->getAttribute('eventToken');
            $isOwner = $this->db->event->findOne(['author' => $userId,'token' => $eventToken]);
            if(isset($isOwner)){// si l'user est bien le proprietaire on modifie l'event
                $event = [
                    "name" => $input['name'],
                    "date" => $input['date'],
                    "location" => $input['location'],
                    "description" => $input['description'],
                ];
                $this->db->event->updateOne(["author" => $userId,"token" => $eventToken],['$set'=> $event]);

                $UpdatedEvent = $this->db->event->findOne(["author" => $userId,"token" => $eventToken]);
                $eventarray = [
                    "id" => (string)$UpdatedEvent->_id,
                    "name" => $UpdatedEvent->name,
                    "date" => $UpdatedEvent->date,
                    "location" => $UpdatedEvent->location,
                    "is_public" => $UpdatedEvent->is_public,
                    "description" => $UpdatedEvent->description,
                    "token" => $UpdatedEvent->token,
                ];
                $response = Writer::jsonResponse($response, 201, $eventarray);
            }else{
                $resp = [
                    "type" => "erreur",
                    "error" => 401,
                    "message" => "unauthorized"
                ];
                $response = Writer::jsonResponse($response, 401, $resp);

            }
        }else{
            $resp = [
                "type" => "erreur",
                "error" => 400,
                "message" => "Les paramètres ne correspondent pas"
            ];
            $response = Writer::jsonResponse($response, 401, $resp);
        }
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
            "is_public" => $event->is_public,
            "description" => $event->description,
            "token" => $event->token,
        ];
        $response = Writer::jsonResponse($response, 200, $eventarray);

        return $response;

    }

    public function getPublicEvents(Request $request, Response $response, $args)
    {
        $events = $this->db->event->find(['is_public' => true]);
        $evenements = array();
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['is_public'] = $event->is_public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, (object)$evenements);
        return $response;
    }

    public function getUserRegisteredEvents(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('token');
        $events = $this->db->event->find(['members' => $user['id']]);
        $evenements = array();
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['is_public'] = $event->is_public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, (object)$evenements);
        return $response;
    }

    public function joinPublicEvent(Request $request, Response $response, $args){
        $user = $request->getAttribute('token');
        $eventtoken = $args["eventtoken"];
        $id = $user['id'];

        if($event = $this->db->event->find(['token' => $eventtoken]))
        {
            $this->db->event->updateOne(
                ["token"=>$eventtoken],
                ['$push'=>['members'=>$id]]
            );
            $response = Writer::jsonResponse($response, 200, ["success"=>"Join with success"]);
            return $response;
        }
        return $response = Writer::jsonResponse($response, 500, ["error"=>"internal error"]);

    }

    public function joinPrivateEvent(Request $request, Response $response){
        $input = $request->getParsedBody();
        $user = $request->getAttribute('token');
        $id = $user['id'];

        if($event = $this->db->event->find(['eventpass' => $input['eventpass']]))
        {
            $this->db->event->updateOne(
                ["eventpass"=>$input['eventpass']],
                ['$push'=>['members'=>$id]]
            );
            $response = Writer::jsonResponse($response, 200, ["success"=>"Join with success"]);
            return $response;
        }
        return $response = Writer::jsonResponse($response, 500, ["error"=>"internal error"]);
    }

    public function getEventCreated(Request $request, Response $response, $args){
        $user = $request->getAttribute('token');
        $events = $this->db->event->find(['author' => $user['id']]);
        $evenements = array();
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['is_public'] = $event->is_public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, (object)$evenements);
        return $response;
    }

    public function getHistory(Request $request, Response $response, $args){
        $user = $request->getAttribute('token');
        $events = $this->db->event->find(['status' => "fini","members"=>$user['id']]);
        $evenements = array();
        foreach ($events as $event) {
            $arrayevent = array();
            $arrayevent['_id'] = (string)$event->_id;
            $arrayevent['author'] = $event->author;
            $arrayevent['name'] = $event->name;
            $arrayevent['date'] = $event->date;
            $arrayevent['location'] = $event->location;
            $arrayevent['is_public'] = $event->is_public;
            $arrayevent['description'] = $event->description;
            $arrayevent['members'] = $event->members;
            $arrayevent['token'] = $event->token;
            $arrayevent['status'] = $event->status;
            $evenements[] = $arrayevent;
        }
        $response = Writer::jsonResponse($response, 200, (object)$evenements);
        return $response;
    }


}

?>
