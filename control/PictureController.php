<?php
namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \photobox\utils\Writer;

class PictureController{
  private $db;
  public function __construct($container){
    $this->db = $container->get('db');
  }

  public function store(Request $request,Response $response){

    $input = $request->getParsedBody();
    $rawdata = $input["picture"];
    $rawdata = explode(',',$rawdata);
    $picturecontent = base64_decode($rawdata[1]);
    $basename = bin2hex(random_bytes(16));
    $path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $basename;
    file_put_contents($path,$picturecontent);
    var_dump($this->db);
    $response = Writer::jsonResponse($response,201,[
      "picture" => $basename,
      "debug" => var_dump($this->db),
    ]);
    //Il faudra inclure l'ajout en BDD

    return $response;
  }

  public function send(Request $request, Response $response)
  {
    $picture = $request->getAttribute("id");
    $picture = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "../uploads/" . $picture);
    $picture = base64_encode($picture);

    $response = Writer::jsonResponse($response,200,[
      "picture" => $picture,
    ]);
    return $response;
  }

}
