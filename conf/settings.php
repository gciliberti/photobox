<?php
use Respect\Validation\Validator;
return [
    'settings' => [
        'displayErrorDetails' => true,
        'registerValidator'=> [
            'nom' => Validator::alpha("é à è ù ô î â ï ë ö ü"),
            'prenom' =>  Validator::alpha("é à è ù ô î â ï ë ö ü"),
            'pseudo' =>  Validator::alpha("é à è ù ô î â ï ë ö ü"),
            'date_naiss' =>  Validator::regex('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/'),
            'tel' =>  Validator::phone(),
            'mail'=>Validator::email(),
            'mdp'=>Validator::stringType()->length(6, 50),
        ],
        'postPictureEventValidator'=>[
            'picture'=>Validator::stringType(),
        ],
        'postCommentValidator'=>[
            'comment'=> Validator::stringType()->length(1,250),
        ],
        'postEventValidator'=>[
            'name'=>Validator::alpha("é à è ù ô î â ï ë ö ü")->length(1,120),
            'date'=>Validator::regex('/^([1-9]|([012][0-9])|(3[01]))\/([0]{0,1}[1-9]|1[012])\/\d\d\d\d (20|21|22|23|[0-1]?\d):[0-5]?\d/'),
            'location'=>Validator::stringType()->length(5,200),
            'is_public'=>Validator::boolType(),
            'description'=>Validator::stringType()->length(5,250),
        ],
        'putEventValidator'=>[
            'name'=>Validator::alpha("é à è ù ô î â ï ë ö ü")->length(1,120),
            'location'=>Validator::stringType()->length(5,200),
            'date'=>Validator::regex('/^([1-9]|([012][0-9])|(3[01]))\/([0]{0,1}[1-9]|1[012])\/\d\d\d\d (20|21|22|23|[0-1]?\d):[0-5]?\d/'),
            'description'=>Validator::stringType()->length(5,250),
        ],
        'postJoinPrivateEvent'=>[
            'eventpass'=>Validator::alpha()->length(8,8),
        ],
        'playerAuth'=>[
            'pass'=>Validator::alnum()->length(8,8),
        ]
    ]
];