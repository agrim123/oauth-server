<?php

namespace SDSLabs\Falcon\Storage;

//use SDSLabs\Falcon\Model\Database;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ScopeInterface;

class ScopeStorage extends AbstractStorage implements ScopeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
        $query = Database::get_instance()->prepare(
            "SELECT id, description
            FROM `oauth_scopes`
            WHERE id = :scope");

        $query->execute(array("scope" => $scope));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }

        $scope = (new ScopeEntity($this->server))
                    ->hydrate([
                        "id"            => $result["id"],
                        "description"   => $result["description"]
                    ]);

        return $scope;
    }
}