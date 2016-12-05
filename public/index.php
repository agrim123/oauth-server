<?php
require("../config/bootstrap.php");

$app->get('/authorize', function (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) use ($authorization_server) {
   echo json_encode($request->getQueryParams());
   global $auth_code_storage;
   $auth_code_storage->create('testapp',3600,1,'asas');
   
});
$app->run();