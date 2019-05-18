<?php

namespace App\Core;

class Db
{
    /**
     * @var \mysqli
     */
    protected $db;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $flags = 0;

    /**
     * @var Db
     */
    protected static $instance;

    /**
     * @param $user
     * @param $password
     * @param $database
     * @param string $host
     * @param int $port
     */
    public function __construct(
        string $user,
        string $password,
        string $database,
        string $host = "127.0.0.1",
        int $port = 3306
    ) {
        $this->db       = mysqli_init();
        $this->host     = $host;
        $this->user     = $user;
        $this->password = $password;
        $this->database = $database;
        $this->port     = $port;

        static::$instance = $this;
    }

    /**
     * @return bool
     */
    public function connect()
    {
        $s = $this->db->real_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port,
            null,
            $this->flags
        );

        return $s;
    }

    public static function getInstance(): Db
    {
        if (!static::$instance) {
            throw new \LogicException('Class Db not initialized');
        }

        return static::$instance;
    }

    /**
     * @param array $data
     * @return string
     * @throws \RuntimeException
     */
    protected function makeSet($data)
    {
        $col = null;
        try {
            foreach ($data as $col => &$val) {
                $val = "`$col` = " . $this->toValue($val);
            }
            return implode(", ", $data);
        } catch (\Exception $e) {
            throw new \RuntimeException("Invalid data for `$col`: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * @param mixed $val
     * @return int|string
     * @throws \InvalidArgumentException
     */
    protected function toValue($val)
    {
        if ($val === false) {
            return 0;
        } elseif (is_scalar($val)) {
            return '"' . $this->escape($val) . '"';
        } elseif ($val === null) {
            return "NULL";
        } else {
            throw new \InvalidArgumentException("unknown parameter's type");
        }
    }

    /**
     * @param string $query
     * @return bool|\mysqli_result
     */
    public function query(string $query)
    {
        if ($result = $this->db->query($query)) {
            return $result;
        } elseif($this->db->errno >= 2000) { // see http://dev.mysql.com/doc/refman/5.1/en/error-messages-client.html
            throw new \RuntimeException("{$this->db->error} ({$this->db->errno})\nQuery: {$query}", $this->db->errno);
        } else {
            throw new \LogicException("{$this->db->error} ({$this->db->errno})\nQuery: {$query}", $this->db->errno);
        }
    }

    /**
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return $this->db->escape_string($string);
    }

    /**
     * @param string $table
     * @param string $where
     * @return int
     */
    public function delete($table, $where)
    {
        $this->query("DELETE FROM `" . $this->escape($table) . "` WHERE $where");

        return $this->db->affected_rows;
    }

    /**
     * @param string $table
     * @param array $data
     * @return mixed
     */
    public function insert($table, array $data)
    {
        $this->query("INSERT INTO `" . $this->escape($table) . "` SET " . $this->makeSet($data));

        return $this->db->insert_id;
    }

    /**
     * @param string $table
     * @param array $data
     * @param string $where
     * @return mixed
     */
    public function update(string $table, array $data, string $where = "")
    {
        $this->query("UPDATE `" . $this->escape($table) . "` SET " . $this->makeSet($data) . ($where ? (" WHERE $where") : ""));

        return $this->db->affected_rows;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function fetchAll($query)
    {
        return $this->query($query)->fetch_all(\MYSQLI_ASSOC);
    }


    /**
     * @param $query
     * @return mixed|null
     */
    public function fetchOne($query) {
        $res = $this->query($query);

        if($res->num_rows) {
            $row = $res->fetch_array(\MYSQLI_NUM);

            return $row[0];
        } else {
            return null;
        }
    }
}
