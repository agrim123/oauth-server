<?php

namespace OauthServer\Storage;

use OauthServer\Model\Database;

use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AuthCodeInterface;

class AuthCodeStorage extends AbstractStorage implements AuthCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($auth_code)
    {
        $query = Database::get_instance()->prepare(
            "SELECT auth_code, client_redirect_uri, expire_time
            FROM `oauth_auth_codes`
            WHERE `auth_code` = :auth_code
                AND expire_time >= :time");

        $query->execute(array("auth_code" => $auth_code, "time" => time()));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }

        $auth_code = new AuthCodeEntity($this->server);
        $auth_code->setId($result["auth_code"]);
        $auth_code->setRedirectUri($result["client_redirect_uri"]);
        $auth_code->setExpireTime($result["expire_time"]);

        return $auth_code;
    }

    /**
     * {@inheritdoc}
     */
    public function create($auth_code, $expire_time, $session_id, $redirect_uri)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_auth_codes`(auth_code, session_id, expire_time, client_redirect_uri)
            VALUES(:auth_code, :session_id, :expire_time, :client_redirect_uri)");

        $query->execute(array("auth_code" => $auth_code, "expire_time" => $expire_time, "session_id", $session_id, "client_redirect_uri" => $redirect_uri));
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(AuthCodeEntity $auth_code)
    {
        $query = Database::get_instance()->prepare(
            "SELECT `oauth_scopes`.id, `oauth_scopes`.description
            FROM `oauth_scopes`, `oauth_auth_code_scopes`
            WHERE `oauth_auth_code_scopes`.scope = `oauth_scopes`.id
                AND `oauth_auth_cide_scopes`.auth_code = :auth_code");

        $query->execute(array("auth_code" => $auth_code->getId()));
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
    public function associateScope(AuthCodeEntity $auth_code, ScopeEntity $scope)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_auth_code_scopes`(auth_code, scope)
            VALUES (:auth_code, :scope)");

        $query->execute(array("auth_code" => $auth_code->getId(), "scope" => $scope->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthCodeEntity $auth_code)
    {
        $query = Database::get_instance()->prepare(
            "DELETE FROM `oauth_auth_code`
            WHERE auth_code = :auth_code");

        $query->execute(array("auth_code" => $auth_code->getId()));
    }
}