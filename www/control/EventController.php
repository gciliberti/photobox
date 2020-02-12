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
    ];

    $insert = $this->db->event->insertOne($event);
    $id = $insert->getInsertedId();
    $event['id'] = $id;
    $response = Writer::jsonResponse($response,201,$event);
    //Il faudra inclure l'ajout en BDD

    return $response;
  }


}
 ?>
