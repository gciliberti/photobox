<?php
namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class EventController{
  private $db;
  public function __construct($container){
    $this->db = $container->get('db');
  }

  public function create(Request $request,Response $response){
    $input = $request->getParsedBody();
    $event = [
      "name" => $input['name'],
      "date" => $input['date'],
      "location" => $input['location'],
      "public" => $input['public'],
      "description" => $input['description'],
      "token" => Writer::generateToken(),
      "members" => $input["members"]
    ];

    $insert = $this->db->event->insertOne($event);
    $id = $insert->getInsertedId();
    $event['id'] = $id;
    $response = Writer::jsonResponse($response,201,$event);
    //Il faudra inclure l'ajout en BDD

    return $response;
  }

  public function getEventwithId(Request $request,Response $response, $args){
    $event_id = $request->getAttribute('id');
    $event = $this->db->event
    ->findOne(['_id'=> new \MongoDB\BSON\ObjectId("$event_id")]);
    $eventarray = [
      "id"=>(string)$event->_id,
      "name"=>$event->name,
      "date"=>$event->date,
      "location"=>$event->location,
      "public"=>$event->public,
      "description"=>$event->description,
      "token"=>$event->token,
    ];
    $response = Writer::jsonResponse($response,200,$eventarray);

    return $response;

  }


}
?>
