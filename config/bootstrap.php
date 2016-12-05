<?php 

use SDSLabs\Falcon\Storage;

	global $CONFIG;
	use \Slim\App;
	$CONFIG = json_decode(file_get_contents("../config/config.json"), true);
	if ($CONFIG["environment"] == "development") {
		error_reporting("-1");
		ini_set("display_errors", "On");
	}

    // Autoloading
	require("../vendor/autoload.php");
	 $authorization_server = new \League\OAuth2\Server\AuthorizationServer();
	$auth_code_storage = new Storage\AuthCodeStorage();
	$authorization_server->setAuthCodeStorage($auth_code_storage);


	$app = new App();
	if ($CONFIG["environment"] == "production") {
		$app->config("debug", false);
	}
