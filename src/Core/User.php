<?php


namespace App\Core;


class User
{
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
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $access;

    /**
     * @param int $id
     * @param string $name
     * @param string $email
     * @param int $access
     * @return User
     */
    public static function factory(int $id, string $name, string $email, int $access = self::ACCESS_GUEST)
    {
        $user = (new static)
            ->setId($id)
            ->setName($name)
            ->setEmail($email)
            ->setAccess($access);

        return $user;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name . " ($this->id)";
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
        $this->email = $email;

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
        $this->access = $access;

        return $this;
    }
}
