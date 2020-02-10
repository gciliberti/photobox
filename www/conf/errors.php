<?php
return $tab = ["notAllowedHandler"=>
    $c['notAllowedHandler']= function( $c ) {
        return function( $req, $resp , $methods ) {
            return $resp->withStatus(405)
                ->withHeader('Allow', implode(', ', $methods))
                ->withHeader('Content-type', 'text/html')
                ->write('Method must be one of: ' . implode(', ', $methods));
        };
    },"notFoundHandler"=>
    $c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('PAGE NOT FOUND !');
    };
},"phpErrorHandler" =>
    $c['phpErrorHandler'] = function ($c) {
        return function ($request, $response, $error) use ($c) {
            return $response->withStatus(500);
                $resp->getBody()->write( 'error :' .$e->getMessage() )
                    ->write( 'file : ' . $e->getFile() )
                    ->write( 'line : ' . $e->getLine() ) ;
                return$resp ;
        };
    },"notFoundHandler"=>
    $c['notFoundHandler']= function( $c ) {
    return function( $req, $resp ) {
        $resp=$resp->withStatus( 400 ) ;
        $resp->getBody()->write( 'URI non traitée' ) ;
        return $resp ;
        };
    }
];