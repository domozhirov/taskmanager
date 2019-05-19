<?php


namespace App\Models;

use App\Core\Db;

class User
{
    const TABLE = 'user';

    const ACCESS_GUEST = 0;
    const ACCESS_USER = 5;
    const ACCESS_ADMIN = 10;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $access;

    /**
     * @param int $id
     * @param string $name
     * @param string $login
     * @param string $email
     * @param int $access
     * @return User
     */
    public static function factory(int $id, string $name, string $login, string $email, int $access = self::ACCESS_GUEST)
    {
        $user = (new static)
            ->setId($id)
            ->setName($name)
            ->setLogin($login)
            ->setEmail($email)
            ->setAccess($access);

        return $user;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->login . " ($this->id)";
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return User
     */
    public function setLogin(string $login): User
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new \InvalidArgumentException("Email ($email) is invalid", 400);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccess(): int
    {
        return $this->access;
    }

    /**
     * @param int $access
     * @return User
     */
    public function setAccess(int $access): User
    {
        if (!$access || $access > static::ACCESS_ADMIN) {
            throw new \InvalidArgumentException("Access ($access) is invalid", 400);
        }

        $this->access = $access;

        return $this;
    }

    /**
     * array
     */
    public function toArray(): array
    {
        return [
            'id'     => $this->getId(),
            'login'  => $this->getLogin(),
            'name'   => $this->getName(),
            'email'  => $this->getEmail(),
            'access' => $this->getAccess(),
        ];
    }

    /**
     * @param string $login
     * @return User|null
     */
    public static function get(string $login): ?User
    {
        $db    = Db::getInstance();
        $login = $db->escape($login);
        $table = static::TABLE;
        $query = "SELECT * FROM $table WHERE login = '$login'";
        $user  = Db::getInstance()->fetchRow($query);

        if ($user) {
            return (new static)
                ->setId($user['id'])
                ->setLogin($user['login'])
                ->setName($user['name'])
                ->setEmail($user['email'])
                ->setPassword($user['password'])
                ->setAccess($user['access']);
        } else {
            return null;
        }
    }
}
