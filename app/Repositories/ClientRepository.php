<?php
 

namespace Oauth\Server\Repositories;

use Oauth\Server\Model\Database;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Oauth\Server\Entities\ClientEntity;
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_client_redirect_uris`.client_id, `oauth_client_redirect_uris`.redirect_uri, `oauth_client_redirect_uris`.name
            FROM `oauth_client_redirect_uris`
            WHERE `oauth_client_redirect_uris`.client_id = :clientIdentifier");

        $query->execute(array(":clientIdentifier" => $clientIdentifier));
        $client = $query->fetch(\PDO::FETCH_ASSOC);
        if(!$client){
            return;
        }
        /*$clients = [
            'myawesomeapp' => [
                'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
                'name'            => 'My Awesome App',
                'redirect_uri'    => 'http://localhost:8080',
                'is_confidential' => true,
            ],
        ];*/

        // Check if client is registered
        //if (array_key_exists($clientIdentifier, $clients) === false) {
        //    return;
        //}

       /* if (
            $mustValidateSecret === true
            && $clients[$clientIdentifier]['is_confidential'] === true
            && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
        ) {
            return;
        }*/
        $client_name = $client['name'];
        $client_redirect_uri = $client['redirect_uri'];
        $client = new ClientEntity();
        $client->setIdentifier($clientIdentifier);
        $client->setName($client_name);
        $client->setRedirectUri($client_redirect_uri);

        return $client;
    }
}
