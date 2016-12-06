<?php
require("../config/bootstrap.php");
use Slim\App;

$app->get('/authorize', function (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($authorization_server) {
   
    try {
    
        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $authorization_server->validateAuthorizationRequest($request);
        
        // The auth request object can be serialized and saved into a user's session.
        // You will probably want to redirect the user at this point to a login endpoint.
        
        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new Oauth\Server\Entities\UserEntity()); // an instance of UserEntityInterface
        
        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.
        
        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);
        
        // Return the HTTP redirect response
        return $authorization_server->completeAuthorizationRequest($authRequest, $response);
        
    } catch (OAuthServerException $exception) {
    
        // All instances of OAuthServerException can be formatted into a HTTP response
        return $exception->generateHttpResponse($response);
        
    } catch (\Exception $exception) {
        echo "500";
        // Unknown exception
       /*$body = new Zend\Diactoros\Stream('php://temp', 'r+');
        $body->write($exception->getMessage());
        return $response->withStatus(500)->withBody($body);
       */ 
    }
});
$app->post('/access_token', function (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($authorization_server) {

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
});
$app->run();