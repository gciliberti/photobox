<?php
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;
require 'vendor/autoload.php';
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);
$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/uploads';
$app->post('/picture', function(Request $request, Response $response) {
    $directory = $this->get('upload_directory');

    $uploadedFiles = $request->getUploadedFiles();

    // handle single input with multiple file uploads
    foreach ($uploadedFiles as $uploadedFile) {
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename );
        }
    }

});

$app->get('/picture/{id}', function(Request $request, Response $response, $args){
  $filename = __DIR__ . '/uploads/' . $args["id"];
  if(file_exists($filename)){
    $image = file_get_contents($filename);
    $response->write($image);
    return $response->withHeader('Content-Type', 'image/jpeg');
  }
  else{
    $response->write("c pa bO");
  }
});

function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

$app->run();
