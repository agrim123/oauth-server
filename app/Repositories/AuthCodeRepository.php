<?php


namespace Oauth\Server\Repositories;
use Oauth\Server\Model\Database;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Oauth\Server\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * {@inheritdoc}
    **/
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        // Some logic to persist the auth code to a database
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        // Some logic to revoke the auth code in a database
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        return false; // The auth code has not been revoked
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
    public function create($auth_code, $expire_time, $session_id, $redirect_uri)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_auth_codes`(auth_code, session_id, expire_time, client_redirect_uri)
            VALUES(:auth_code, :session_id, :expire_time, :client_redirect_uri)");

        $query->execute(array("auth_code" => $auth_code, "expire_time" => $expire_time, "session_id", $session_id, "client_redirect_uri" => $redirect_uri));
    }
}
