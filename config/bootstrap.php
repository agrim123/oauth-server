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
//intialize repositories
$clientRepository = new Oauth\Server\Repositories\ClientRepository(); 
$scopeRepository = new Oauth\Server\Repositories\ScopeRepository(); 
$accessTokenRepository = new Oauth\Server\Repositories\AccessTokenRepository(); 
$authCodeRepository = new Oauth\Server\Repositories\AuthCodeRepository(); 
$refreshTokenRepository = new Oauth\Server\Repositories\RefreshTokenRepository(); 
$privateKey = 'file://' . __DIR__ . '/../private.key';
$publicKey = 'file://' . __DIR__ . '/../public.key';
// initialize server
$authorization_server = new \League\OAuth2\Server\AuthorizationServer(
	$clientRepository,
	$accessTokenRepository,
	$scopeRepository,
	$privateKey,
	$publicKey
	);
// grants
// enable auth code grant
// currently not working
$auth_code_grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
	$authCodeRepository,
	$refreshTokenRepository,
	new \DateInterval('PT10M')
	);
$client_credentials_grant = new \League\OAuth2\Server\Grant\ClientCredentialsGrant();
$refresh_grant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
$auth_code_grant->setRefreshTokenTTL(new \DateInterval('P1M'));//custom time given can be changed
$refresh_grant->setRefreshTokenTTL(new \DateInterval('P1M'));//custom time given can be changed

// enabling different grants

$authorization_server->enableGrantType(
	$auth_code_grant,
	new \DateInterval('PT1H')
	);
// enable client credentials grant
$authorization_server->enableGrantType(
	$client_credentials_grant,
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
    );
// Enable the refresh token grant on the server
$authorization_server->enableGrantType(
	$refresh_grant,
    new \DateInterval('PT1H') // new access tokens will expire after an hour
    );
// for implicit grant
/*
$authorization_server->enableGrantType(
    new \League\OAuth2\Server\Grant\ImplicitGrant(new \DateInterval('PT1H')),
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
);
*/


$app = new App();
if ($CONFIG["environment"] == "production") {
	$app->config("debug", false);
}
