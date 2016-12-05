<?php

namespace Oauth\Server\Storage;

use Oauth\Server\Model\Database;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshTokenStorage extends AbstractStorage implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
        $query = Database::get_instance()->prepare(
            "SELECT refresh, expire_time, access_token
            FROM `oauth_refresh_tokens`
            WHERE `refresh_token` = :token");

        $query->execute(array("token" => $token));
        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            // No result
            return;
        }
        
        $token = (new RefreshTokenEntity($this->server))
                    ->setId($result["refresh_token"])
                    ->setExpireTime($result["expire_time"])
                    ->setAccessTokenId($result["access_token"]);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expire_time, $access_token)
    {
        $query = Database::get_instance()->prepare(
            "INSERT INTO `oauth_refresh_tokens`(refresh_token, access_token, expire_time)
            VALUES (:token, :session, :expire_time)");

        $query->execute(array("token" => $token, "access_token" => $access_token, "expire_time" => $expire_time));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RefreshTokenEntity $token)
    {
        $query = Database::get_instance()->prepare(
            "DELETE FROM `oauth_refresh_tokens`
            WHERE refresh_token = :token");

        $query->execute(array("token" => $token->getId()));
    }    
}