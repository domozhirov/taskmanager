<?php

namespace App\Core;

class Request
{
    public const OK = 200;
    public const ERROR = 500;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $cookie;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $secure;

    /**
     * @var array
     */
    protected $path;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var \Exception
     */
    protected $error;

    /**
     * @var string
     */
    protected $out;

    /**
     * @var AbstractController
     */
    protected $actor;

    /**
     * @var bool
     */
    protected $buffering = false;

    /**
     * @var string
     */
    protected $format = 'html';

    /**
     * @var array
     */
    protected $info = [];

    /**
     * @var array
     */
    protected $data = [];


    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $out
     * @return Request
     */
    public function setOut(string $out): Request
    {
        $this->out = $out;

        return $this;
    }

    /**
     * @return string
     */
    public function getOut(): string
    {
        return $this->out;
    }

    /**
     * @return \Exception
     */
    public function getError(): ?\Exception
    {
        return $this->error;
    }

    /**
     * @param \Exception $error
     * @return Request
     */
    public function setError(\Exception $error): Request
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @param string $format
     * @return Request
     */
    public function setFormat(string $format): Request
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function getBuffering(): bool
    {
        return $this->buffering;
    }

    /**
     * @return AbstractController
     */
    public function getActor(): ?AbstractController
    {
        return $this->actor;
    }

    /**
     * @param AbstractController $actor
     * @return Request
     */
    public function setActor(AbstractController $actor): Request
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @param array $info
     */
    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param int $code
     * @param string $mime
     * @param string $charset
     */
    public function setStatus(int $code, string $mime = "text/html", string $charset = "utf-8") {
        header("Content-Type: $mime; charset=$charset", true, $code);
    }

    /**
     * @param string $url
     * @param int $code
     */
    public function redirect(string $url, int $code = 301) {
        header("Location: $url", true, $code);
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function _parseURI(string $path)
    {
        if ($path = urldecode(ltrim($path, "/"))) {
            $frags = explode('/', $path);
            $last  = &$frags[count($frags) - 1];

            if ($pos = strrpos($last, '.')) {
                $format = substr($last, $pos + 1);

                if ($this->engine->hasRenderFormat($format)) {
                    $last         = substr($last, 0, $pos);
                    $this->format = $format;
                }
            }

            $this->path       = $frags;
            $this->controller = $this->path ? array_shift($this->path) : $this->engine->getDefaultControllerName();
            $this->action     = $this->path ? array_shift($this->path) : $this->engine->getDefaultActionName($this->controller);

            return true;
        } else {
            $this->controller = $this->engine->getDefaultControllerName();
            $this->action     = $this->engine->getDefaultActionName($this->controller);

            return false;
        }
    }

    /**
     * @param Engine $engine
     * @throws State
     */
    public function parse(Engine $engine): void
    {
        $this->engine  = $engine;
        $this->uri     = $_SERVER["REQUEST_URI"];
        $this->cookie  = $_COOKIE;
        $this->options = &$_GET;
        $this->secure  = !empty($_SERVER["HTTPS"]);
        $path          = parse_url($this->uri, PHP_URL_PATH);

        $this->_parseURI($path);

        $this->method = $_SERVER["REQUEST_METHOD"];

        if ($this->method === "GET") {
            if (isset($this->options['param']) && is_array($this->options['param'])) {
                $this->params += $this->options['param'];
            }
        } elseif ($this->method === "POST") {
            if (!empty($_SERVER["CONTENT_TYPE"])) {
                $type = $_SERVER["CONTENT_TYPE"];

                switch ($type) {
                    case "multipart/form-data":
                    case "application/x-www-form-urlencoded":
                        $this->params += array_filter($_POST, function ($value) {
                            return ($value === "") ? false : true;
                        });
                        break;
                    case "application/json":
                    case "application/json;charset=UTF-8":
                        $this->params = json_decode(file_get_contents("php://input"), true);
                        break;
                    case "application/phpser":
                        $this->params = unserialize(file_get_contents("php://input"));
                        break;
                    default:
                }
            }
        } else {
            throw State::badRequest("Unsupported request method $this->method");
        }
    }
}
