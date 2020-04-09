<?php

use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use DavidePastore\Slim\Validation\Validation;
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$mongoclient = new \MongoDB\Client(getenv("MONGO_URI"));
$db = $mongoclient->photobox;

$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);
$c = $app->getContainer();
$c['db'] = $db;

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "ignore" => ["/login", "/register","/event/pictures","/assets/event/","/player","/event/picture/last","/player/event/comment/","/player/event/comment/last"],
    "secret" => getenv("JWT_SECRET"),
]));

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



/*get tous les utilisateurs*/
$app->get('/users[/]', \photobox\control\UserController::class . ':getUsers');

/*Get un user en particulier*/
$app->get('/user/{pseudo}[/]', \photobox\control\UserController::class . ':getUser');

$app->put('/user[/]', \photobox\control\UserController::class . ':editProfile');

/*get les évènements auxquels un user a participé*/
$app->get('/users/{id}/{events}[/]', \photobox\control\UserController::class . ':getUserEvents');

/*ajoute un nouveau user*/
$app->post('/register[/]', \photobox\control\AuthController::class . ':register')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['registerValidator']));


//Get une image avec son ID
$app->get('/picture/{id}', \photobox\control\PictureController::class . ':send');

$app->get('/assets/event/{event_token}/{photo_id}', \photobox\control\PictureController::class . ':pictureUri');

$app->post('/picture/event/{eventtoken}', \photobox\control\PictureController::class . ':store')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['postPictureEventValidator']));

//ajouter un commentaire dans un event
$app->post('/event/comment/{eventtoken}', \photobox\control\CommentController::class . ':addCommentEvent')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['postCommentValidator']));

//Recupere le dernier commentaire d'un event
$app->get('/event/comment/last/{eventtoken}', \photobox\control\CommentController::class . ':getEventLastComment');

//Recupere tous les commentaires d'un event
$app->get('/event/comment/{eventtoken}', \photobox\control\CommentController::class . ':getEventComments');

$app->post('/event', \photobox\control\EventController::class . ':create')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['postEventValidator']));
    
$app->get('/player/event/comment/last/{eventtoken}', \photobox\control\CommentController::class . ':getPlayerEventLastComment');
//Recupere le dernier commentaire d'un event

//Recupere tous les commentaires d'un event
$app->get('/player/event/comment/{eventtoken}', \photobox\control\CommentController::class . ':getPlayerEventComments');

//Supprime un event via son token
$app->delete('/event/{eventToken}', \photobox\control\EventController::class . ':deleteEvent');

//Update un event via son token
$app->put('/event/{eventToken}', \photobox\control\EventController::class . ':updateEvent')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['putEventValidator']));

$app->get('/event/pictures/{eventtoken}', \photobox\control\PictureController::class . ':getEventPictures');

$app->get('/event/picture/last/{eventtoken}', \photobox\control\PictureController::class . ':getEventLastPicture');

$app->get('/events/history[/]', \photobox\control\EventController::class . ':getHistory');

$app->get('/event/{id}', \photobox\control\EventController::class . ':getEventwithId');

$app->get('/events',\photobox\control\EventController::class . ':getPublicEvents');

$app->post('/event/join/public/{eventtoken}',\photobox\control\EventController::class . ':joinPublicEvent');

$app->post('/event/join/private[/]', \photobox\control\EventController::class . ':joinPrivateEvent')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['postJoinPrivateEvent']));

$app->get('/events/involved',\photobox\control\EventController::class . ':getUserRegisteredEvents');

$app->get('/events/created',\photobox\control\EventController::class . ':getEventCreated');

$app->post('/login', \photobox\control\AuthController::class . ':login');

$app->post('/player/auth', \photobox\control\PlayerController::class . ':eventAuth')
    ->add(\photobox\middleware\Validator::class . ':dataFormatErrorHandler')
    ->add(new Validation($container->settings['playerAuth']));

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
