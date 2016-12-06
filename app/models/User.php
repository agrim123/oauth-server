<?php

namespace Oauth\Server\Model;

class User
{
    /**
     * The id of the user
     *
     * @var numeric
     */
    protected $id;

    /**
     * The name of the user
     *
     * @var string
     */
    protected $name;

    /**
     * The username of the user
     *
     * @var string
     */
    protected $username;

    /**
     * The email of the user
     *
     * @var string
     */
    protected $email;

    /**
     * The password of the user
     *
     * @var numeric
     */
    protected $password;

    /**
     * The url of the user's photo
     *
     * @var string
     */
    protected $image_url;

    /**
     * The timestamp of creation
     *
     * @var numeric
     */
    protected $created_at;

    /**
     * The timestamp of deletion
     *
     * @var numeric
     */
    protected $deleted_at;

    public function __construct($id,
                                $user_params = null)
    {
        if ($user_params === null) {
            $query = Database::get_instance()->prepare("
                SELECT id, name, username, email, password, image_url, created_at, deleted_at
                FROM `users`
                WHERE `id` = :id AND `verified` = 2");

            $query->execute(array("id" => $id));
            $user_params = $query->fetch(\PDO::FETCH_ASSOC);
        }

        $this->update_user_params($user_params);
    }

    /**
     * Returns a user by username
     *
     * @param   username                    The username of the user
     *
     * @return  SDSLabs\Falcon\Model\User   The user object
     */
    public static function getByUsername($username)
    {
        $query = Database::get_instance()->prepare("
            SELECT id, name, username, email, password, image_url, created_at, deleted_at
            FROM `users`
            WHERE `username` = :username AND `verified` = 2");

        $query->execute(array("username" => $username));
        $user_params = $query->fetch(\PDO::FETCH_ASSOC);

        return new static($user_params["id"], $user_params);
    }

    /**
     * Returns a user by email
     *
     * @param   email                       The email of the user
     *
     * @return  SDSLabs\Falcon\Model\User   The user object
     */
    public static function getByEmail($email)
    {
        $query = Database::get_instance()->prepare("
            SELECT id, name, username, email, password, image_url, created_at, deleted_at
            FROM `users`
            WHERE `email` = :email AND `verified` = 2");

        $query->execute(array("email" => $email));
        $user_params = $query->fetch(\PDO::FETCH_ASSOC);

        return new static($user_params["id"], $user_params);
    }

    /**
     * Returns the logged in user
     *
     * @param   hash                        The hash of the user
     *
     * @return  SDSLabs\Falcon\Model\User   The user object
     */
    public static function getLoggedInUser($hash)
    {
        $query = Database::get_instance()->prepare("
            SELECT `users`.id, `users`.name, `users`.username, `users`.email,
            `users`.password, `users`.image_url, `users`.activation, `users`.verified,
            `users`.created_at, `users`.deleted_at
            FROM `users`, `login`
            WHERE `login`.`hash` = :hash AND `users`.id = `login`.user_id");

        $query->execute(array("hash" => $hash));
        $user_params = $query->fetch(\PDO::FETCH_ASSOC);

        if ($query->rowCount() > 0) {
            return new static($user_params["id"], $user_params);
        }
        else {
            return null;
        }
    }

    /**
     * Function to update the user parameters
     *
     * @param   user_params    The user parameters to be updated
     */
    protected function update_user_params($user_params)
    {
        $this->id = (int)$user_params["id"];
        $this->name = $user_params["name"];
        $this->username = $user_params["username"];
        $this->email = $user_params["email"];
        $this->password = $user_params["password"];
        $this->image_url = $user_params["image_url"];
        $this->created_at = (int)$user_params["created_at"];
        $this->deleted_at = (int)$user_params["deleted_at"];
    }

    /**
     * Returns the id of the user
     *
     * @return  number  The id of the user
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Returns the name of the user
     *
     * @return  string  The name of the user
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Returns the username of the user
     *
     * @return  string  The username of the user
     */
    public function get_username()
    {
        return $this->username;
    }

    /**
     * Returns the email of the user
     *
     * @return  string  The email of the user
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * Returns the password of the user
     *
     * @return  string  The email of the user
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     * Returns the image url of the user
     *
     * @return  string  The image url of the user
     */
    public function get_image_url()
    {
        return $this->image_url;
    }
}
