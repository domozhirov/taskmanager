<?php

namespace App\Models;

use App\Core\Db;

class Task
{
    const TABLE = 'task';

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
     * @var string
     */
    protected $text;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Task
     */
    public function setId(int $id): Task
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
     * @return Task
     */
    public function setName(string $name): Task
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
     * @return Task
     */
    public function setEmail(string $email): Task
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Task
     */
    public function setText(string $text): Task
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return int
     */
    public static function getTotal(): int
    {
        $table = static::TABLE;
        $query = "SELECT COUNT(1) FROM $table";

        return Db::getInstance()->fetchOne($query) ?? 0;
    }

    /**
     * @param int $start
     * @param int $limit
     * @return array
     */
    public static function getAll(int $start = 0, int $limit = 3): array
    {
        $table = static::TABLE;
        $query = "SELECT * FROM $table LIMIT $start, $limit";

        return Db::getInstance()->fetchAll($query);
    }

    public function add()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
