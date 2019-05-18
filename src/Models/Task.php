<?php

namespace App\Models;

use App\Core\Db;

class Task
{
    const TABLE = 'task';

    const LIMIT = 3;

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
    protected $completed = 0;

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
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new \InvalidArgumentException("Email ($email) is invalid", 400);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCompleted(): int
    {
        return $this->completed;
    }

    /**
     * @param int $completed
     */
    public function setCompleted(int $completed): void
    {
        $this->completed = $completed;
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
     * @param string $order_by
     * @return array
     */
    public static function getAll(int $start = 0, int $limit = Task::LIMIT, string $order_by = ''): array
    {
        $db    = Db::getInstance();
        $table = static::TABLE;
        $query = "SELECT * FROM $table";

        if ($order_by) {
            $order_by = $db->escape($order_by);

            $query .= " ORDER BY $order_by";
        }

        return Db::getInstance()->fetchAll("$query LIMIT $start, $limit");
    }

    public function add()
    {
        $data = [
            'name'  => $this->getName(),
            'email' => $this->getEmail(),
            'text'  => $this->getText(),
        ];

        $this->id = Db::getInstance()->insert(static::TABLE, $data);

        return $this->id;
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->getId(),
            'name'      => $this->getName(),
            'email'     => $this->getEmail(),
            'completed' => $this->getCompleted(),
            'text'      => $this->getText(),
        ];
    }
}
