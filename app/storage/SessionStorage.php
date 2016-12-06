<?php

namespace Oauth\Server\Storage;

use Oauth\Server\Model\Database;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\SessionInterface;

class SessionStorage extends AbstractStorage implements SessionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByAccessToken(AccessTokenEntity $access_token)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_sessions`.id, `oauth_sessions`.owner_type, `oauth_sessions`.owner_id
            FROM `oauth_sessions`, `oauth_access_tokens`
            WHERE `oauth_sessions`.id = `oauth_access_tokens`.session_id
                AND `oauth_access_tokens`.access_token = :token");

        $query->execute(array("token" => $access_token->getId()));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }

        $session = new SessionEntity($this->server);
        $session->setId($result["id"]);
        $session->setOwner($result["owner_type"], $result["owner_id"]);

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getByAuthCode(AuthCodeEntity $auth_code)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_sessions`.id, `oauth_sessions`.owner_type, `oauth_sessions`.owner_id
            FROM `oauth_sessions`, `oauth_auth_codes`
            WHERE `oauth_sessions`.id = `oauth_auth_codes`.session_id
                AND `oauth_auth_codes`.auth_code = :auth_code");

        $query->execute(array("auth_code" => $auth_code->getId()));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }

        $session = new SessionEntity($this->server);
        $session->setId($result["id"]);
        $session->setOwner($result["owner_type"], $result["owner_id"]);

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(SessionEntity $session)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_scopes`.id, `oauth_scopes`.description
            FROM `oauth_scopes`, `oauth_session_scopes`
            WHERE `oauth_session_scopes`.scope = `oauth_scopes`.id
                AND `oauth_session_scopes`.session_id = :id");

        $query->execute(array("id" => $session->getId()));
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
    public function create($owner_type, $owner_id, $client_id, $client_redirect_uri = null)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_sessions`(owner_type, owner_id, client_id)
            VALUES (:owner_type, :owner_id, :client_id)");

        $query->execute(array("owner_type" => $owner_type, "owner_id" => $owner_id, "client_id" => $client_id));

        return Database::get_instance()->lastInsertId();
    }

     /**
     * {@inheritdoc}
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_session_scopes`(session_id, scope)
            VALUES (:session_id, :scope)");

        $query->execute(array("session_id" => $session->getId(), "scope" => $scope->getId()));
    }
}