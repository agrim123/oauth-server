<?php 

	use OauthServer\Storage;
	global $CONFIG;
	use \Slim\App;
	$CONFIG = json_decode(file_get_contents("../config/config.json"), true);
	if ($CONFIG["environment"] == "development") {
		error_reporting("-1");
		ini_set("display_errors", "On");
	}

    // Autoloading
	require("../vendor/autoload.php");

// Init our repositories
$clientStorage = new Storage\ClientStorage(); // instance of ClientStorageInterface
$scopeStorage = new Storage\ScopeStorage(); // instance of ScopeStorageInterface
$accessTokenStorage = new Storage\AccessTokenStorage(); // instance of AccessTokenStorageInterface
$authCodeStorage = new Storage\AuthCodeStorage(); // instance of AuthCodeStorageInterface
$refreshTokenStorage = new Storage\RefreshTokenStorage(); // instance of RefreshTokenStorageInterface

$privateKey = 'file://path/to/private.key';
//$privateKey = new CryptKey('file://path/to/private.key', 'passphrase'); // if private key has a pass phrase
$publicKey = 'file://path/to/public.key';

// Setup the authorization server
$server = new \League\OAuth2\Server\AuthorizationServer(
    $clientStorage,
    $accessTokenStorage,
    $scopeStorage,
    $privateKey,
    $publicKey
);

$grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
     $authCodeStorage,
     $refreshTokenStorage,
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
