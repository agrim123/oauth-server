<?php

namespace SDSLabs\Falcon\Storage;

//use SDSLabs\Falcon\Model\Database;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ClientInterface;

class ClientStorage extends AbstractStorage implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($client_id, $client_secret = null, $redirect_uri = null, $grant_type = null)
    {
        /*$query = Database::get_instance()->prepare(
            "SELECT `oauth_clients`.id, `oauth_clients`.name
            FROM `oauth_clients`, `oauth_client_redirect_uris`
            WHERE `oauth_clients`.id = :client_id
                AND COALESCE(:client_secret, `oauth_clients`.secret) = `oauth_clients`.secret
                AND COALESCE(:redirect_uri, `oauth_client_redirect_uris`.redirect_uri) = `oauth_client_redirect_uris`.redirect_uri");

        $query->execute(array("client_id" => $client_id, "client_secret" => $client_secret, "redirect_uri" => $redirect_uri));*/

        // Temporary fix for client credentials grant
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_clients`.id, `oauth_clients`.name
            FROM `oauth_clients`
            WHERE `oauth_clients`.id = :client_id
                AND COALESCE(:client_secret, `oauth_clients`.secret) = `oauth_clients`.secret");

        $query->execute(array("client_id" => $client_id, "client_secret" => $client_secret));
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            // No result
            return;
        }

        $client = new ClientEntity($this->server);
        $client->hydrate([
            "id"    => $result["id"],
            "name"  => $result["name"]
        ]);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySession(SessionEntity $session)
    {
        $query = Database::get_instance(
            "SELECT `oauth_clients`.id, `oauth_clients`.name
            FROM `oauth_clients`, `oauth_sessions`
            WHERE `oauth_clients`.id = `oauth_sessions`.client_id
                AND `oauth_sessions`.id = :session_id");

        $query->execute(array("session_id" => $session_id));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }

        $client = new ClientEntity($this->server);
        $client->hydrate([
            "id"    => $result["id"],
            "name"  => $result["name"]
        ]);

        return $client;
    }
}