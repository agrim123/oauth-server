<?php

/**************

// add an extra column to oauth_client_redirect_uris 'name'

**************/
namespace Oauth\Server\Model;

class Database
{
    /**
     * The database handle
     *
     * @var PDO
     */
    private static $instance;

    /**
     * Function to get a database handle
     *
     * @return PDO
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            // Creating database handle
            global $CONFIG;

            self::$instance = new \PDO("mysql:host={$CONFIG['db_host']};dbname={$CONFIG['db_name']}",
                                            $CONFIG['db_user'],
                                            $CONFIG['db_pass']
                                            );
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$instance;
    } 
}
