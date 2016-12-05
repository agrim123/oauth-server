<?php

namespace SDSLabs\Falcon\Storage;

use SDSLabs\Falcon\Model\Database;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AccessTokenInterface;

class AccessTokenStorage extends AbstractStorage implements AccessTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
        $query = Database::get_instance()->prepare(
            "SELECT access_token, expire_time
            FROM `oauth_access_tokens`
            WHERE `access_token` = :token");

        $query->execute(array("token" => $token));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }
        
        $token = (new AccessTokenEntity($this->server))
                    ->setId($result["access_token"])
                    ->setExpireTime($result["expire_time"]);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(AccessTokenEntity $access_token)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_scopes`.id, `oauth_scopes`.description
            FROM `oauth_scopes`, `oauth_access_token_scopes`
            WHERE `oauth_access_token_scopes`.scope = `oauth_scopes`.id
                AND `oauth_access_token_scopes`.access_token = :access_token");

        $query->execute(array("access_token" => $access_token->getId()));
        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        $scopes = [];
        foreach ($results as $result) {
            $scopes[] = (new ScopeEntity($this->server))->hydrate([
                "id" => $result["id"],
                "description" => $result["description"]
            ]);
        }

        return $scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expire_time, $session_id)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_access_tokens`(access_token, session_id, expire_time)
            VALUES (:token, :session_id, :expire_time)");

        $query->execute(array("token" => $token, "session_id" => $session_id, "expire_time" => $expire_time));
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_access_token_scopes`(access_token, scope)
            VALUES (:token, :scope)");

        $query->execute(array("token" => $token->getId(), "scope" => $scope->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccessTokenEntity $token)
    {
        $query = Database::get_instance()->prepare(
            "DELETE FROM `oauth_access_tokens`
            WHERE access_token = :token");

        $query->execute(array("token" => $token->getId()));
    }
}