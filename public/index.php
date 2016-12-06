<?php
require("../config/bootstrap.php");
$app->get('/authorize', '\Oauth\Server\Controller\AuthCode:get');
$app->post('/access_token', '\Oauth\Server\Controller\AccessToken:post');
$app->run();