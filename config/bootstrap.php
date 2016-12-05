<?php 

use Oauth\Server\Storage;

	global $CONFIG;
	use \Slim\App;
	$CONFIG = json_decode(file_get_contents("../config/config.json"), true);
	if ($CONFIG["environment"] == "development") {
		error_reporting("-1");
		ini_set("display_errors", "On");
	}

    // Autoloading
	require("../vendor/autoload.php");
	$clientRepository = new Oauth\Server\Repositories\ClientRepository(); // instance of ClientRepositoryInterface
$scopeRepository = new Oauth\Server\Repositories\ScopeRepository(); // instance of ScopeRepositoryInterface
$accessTokenRepository = new Oauth\Server\Repositories\AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
$authCodeRepository = new Oauth\Server\Repositories\AuthCodeRepository(); // instance of AuthCodeRepositoryInterface
$refreshTokenRepository = new Oauth\Server\Repositories\RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface

$privateKey = 'file://path/to/private.key';
//$privateKey = new CryptKey('file://path/to/private.key', 'passphrase'); // if private key has a pass phrase
$publicKey = 'file://path/to/public.key';

// Setup the authorization server
$server = new \League\OAuth2\Server\AuthorizationServer(
    $clientRepository,
    $accessTokenRepository,
    $scopeRepository,
    $privateKey,
    $publicKey
);

$grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
     $authCodeRepository,
     $refreshTokenRepository,
     new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
 );

$grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
$server->enableGrantType(
    $grant,
    new \DateInterval('PT1H') // access tokens will expire after 1 hour
);


	$app = new App();
	if ($CONFIG["environment"] == "production") {
		$app->config("debug", false);
	}
