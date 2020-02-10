<?php
namespace photobox\control;


use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use photobox\utils\Writer as Writer;

class PictureController{
  public function store(Request $request,Response $response){
    $input = $request->getParsedBody();
    $rawdata = $input["picture"];
    $rawdata = explode(',',$rawdata);
    $picturecontent = base64_decode($rawdata[1]);
    $basename = bin2hex(random_bytes(16));
    $path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $basename;
    file_put_contents($path,$picturecontent);
    Writter::jsonResponse();
  }
}
 ?>
