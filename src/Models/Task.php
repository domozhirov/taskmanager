<?php

namespace App\Models;

use App\Core\Db;

class Task
{
    const TABLE = 'task';

    const LIMIT = 3;

    const IN_WORK = 0;
    const COMPLETED = 1;

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
    protected $status = 0;

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
     * @return bool
     */
    public function getStatus(): bool
    {
        return (bool)$this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = (int)$status;
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

        if ($order_by = static::prepareOrder($order_by)) {
            $order_by = $db->escape($order_by);
            $query    .=  " ORDER BY $order_by";
        }

        return Db::getInstance()->fetchAll("$query LIMIT $start, $limit");
    }

    public function add()
    {
        $data = [
            'name'    => $this->getName(),
            'email'   => $this->getEmail(),
            'text'    => $this->getText(),
            'created' => date("Y-m-d H:i:s"),
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
            'id'     => $this->getId(),
            'name'   => $this->getName(),
            'email'  => $this->getEmail(),
            'status' => $this->getStatus(),
            'text'   => $this->getText(),
        ];
    }

    /**
     * @param string $sort_by
     * @return string
     */
    protected static function prepareOrder(string $sort_by): string
    {
        $order         = [];
        $system_fields = ["name", "email", "status"];

        if ($sort_by && !is_array($sort_by)) {
            foreach (explode(",", $sort_by) as $sort_item) {
                $list = explode(" ", trim($sort_item), 2);

                if (count($list) == 2) {
                    list($sort_field, $sort_asc) = $list;

                    if (isset($system_fields[$sort_field]) || is_integer($sort_field)) { // integer is for sorting by folder_id
                        $sort_field = "$sort_field";
                    }

                    $order[$sort_field] = $sort_asc == "asc" ? 'ASC' : 'DESC';
                } else {
                    $order['created'] = 'DESC';
                }
            }
        } else {
            $order['created'] = 'DESC';
        }

        return implode(', ', array_map(
            function ($k, $v) {
                return "$k $v";
            },
            array_keys($order),
            array_values($order)
        ));
    }
}
