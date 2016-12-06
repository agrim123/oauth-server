<?php

namespace Oauth\Server\Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \Oauth\Server\Entities\UserEntity;

class AccessToken 
{
	public static function post(ServerRequestInterface $request, ResponseInterface $response){
		global $authorization_server;
		try {

        // Try to respond to the request
			return $authorization_server->respondToAccessTokenRequest($request, $response);

		} catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {

        // All instances of OAuthServerException can be formatted into a HTTP response
			return $exception->generateHttpResponse($response);

		} catch (\Exception $exception) {
			echo "500";
        // Unknown exception
        /*$body = new Stream('php://temp', 'r+');
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);*/
    }
}
}