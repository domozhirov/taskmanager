<?php

namespace App\Core;

/**
 * Class Config
 *
 * @property Config actions
 *
 * @property Config admin
 * @property int    id
 * @property string login
 * @property string email
 * @property string name
 * @property int    access
 *
 * @property Config db
 * @property string host
 * @property int    port
 * @property string user
 * @property string password
 * @property string database
 *
 * @property Config app
 * @property string home
 *
 */
class Config implements \ArrayAccess
{
    /**
     * @var Config
     */
    protected $main;

    /**
     * @var string
     */
    protected $config_path;

    /**
     * @var string
     */
    protected $configs_dir;

    /**
     * @var Config
     */
    public static $instance;

    public function __construct(string $config_path, string $configs_dir)
    {
        $this->main        = include($config_path);
        $this->config_path = $config_path;
        $this->configs_dir = $configs_dir;

        static::$instance = $this;
    }

    /**
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (!static::$instance) {
            throw new \LogicException('Class Config is not initialized');
        }

        return self::$instance;
    }

    /**
     * @param $part
     * @return \ArrayObject
     */
    public function __get($part): \ArrayObject
    {
        if (isset($this->main[$part])) {
            $conf = $this->main[$part];
        } else {
            $conf = [];
        }

        if (file_exists($this->configs_dir . '/' . $part . '.php')) {
            if ($conf) {
                $conf += require($this->configs_dir . '/' . $part . '.php');
            } else {
                $conf = require($this->configs_dir . '/' . $part . '.php');
            }
        } elseif (!$conf) {
            throw new \LogicException("Configuration part $part not found");
        }
        return $this->$part = new \ArrayObject($conf, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param $part
     * @return \ArrayObject
     */
    public function get($part) {
        return $this->$part;
    }

    /**
     * @param $part
     * @param $key
     * @param $value
     */
    public function set($part, $key, $value) {
        $this->main[$part][$key] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->$offset) || isset($this->main[$offset]) || file_exists($this->configs_dir . '/' . $offset . '.php');
    }

    /**
     * @param mixed $offset
     * @return \ArrayObject|mixed
     */
    public function offsetGet($offset) {
        return $this->$offset;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (isset($this->$offset)) {
            if (is_array($value)) {
                $this->$offset = $value + $this->$offset;
            } else {
                throw new \LogicException('Unexpected type ' . gettype($value) . ', expected array');
            }
        } else {
            $this->main[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->$offset);
    }
}
