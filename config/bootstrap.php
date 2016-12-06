<?php 

use Oauth\Server\Storage;

global $CONFIG;
use \Slim\App;
$CONFIG = json_decode(file_get_contents("../config/config.json"), true);
if ($CONFIG["environment"] == "development") {
	error_reporting("-1");
	ini_set("display_errors", "On");
}


require("../vendor/autoload.php");

// replaced Storage with Repositories in accordance with new version of oauth2.0

$clientRepository = new Oauth\Server\Repositories\ClientRepository(); 
$scopeRepository = new Oauth\Server\Repositories\ScopeRepository(); 
$accessTokenRepository = new Oauth\Server\Repositories\AccessTokenRepository(); 
$authCodeRepository = new Oauth\Server\Repositories\AuthCodeRepository(); 
$refreshTokenRepository = new Oauth\Server\Repositories\RefreshTokenRepository(); 

$privateKey = 'file://' . __DIR__ . '/../private.key';
$publicKey = 'file://' . __DIR__ . '/../public.key';
$authorization_server = new \League\OAuth2\Server\AuthorizationServer(
	$clientRepository,
	$accessTokenRepository,
	$scopeRepository,
	$privateKey,
	$publicKey
	);

$auth_code_grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
	$authCodeRepository,
	$refreshTokenRepository,
	new \DateInterval('PT10M')
	);

$auth_code_grant->setRefreshTokenTTL(new \DateInterval('P1M'));
$authorization_server->enableGrantType(
	$auth_code_grant,
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
    );
/*$refresh_grant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
$refresh_grant->setRefreshTokenTTL(new \DateInterval('P1M')); // new refresh tokens will expire after 1 month

// Enable the refresh token grant on the server
$authorization_server->enableGrantType(
	$refresh_grant,
    new \DateInterval('PT1H') // new access tokens will expire after an hour
    );*/
$app = new App();
if ($CONFIG["environment"] == "production") {
	$app->config("debug", false);
}
