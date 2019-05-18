<?php

namespace App\Core;

class Engine
{
    /**
     * @var array
     */
    protected $home = [];

    /**
     * @var array[]
     */
    protected $controllers = [];

    /**
     * @var string
     */
    protected $render = Render::class;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Engine constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        foreach ($config->actions as $name => $action) {
            $this->_addController($name, $action[0], $action[1] ?? 'index');
        }

        $this->setHome($config->app->home);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     * Display request result
     * @param $request
     * @param \Exception|null $error if NULL - no error
     */
    public function display(Request $request, $error): void
    {
        $request->setOut(ob_get_clean());

        if ($error) {
            $request->setError($error);
        }

        call_user_func([$this->render, $request->getFormat() . 'Format'], $request);
    }

    /**
     * @param string $format
     * @return bool
     */
    public function hasRenderFormat(string $format): bool
    {
        return method_exists($this->render, $format . "Format");
    }

    /**
     * @return bool
     */
    public function getDefaultControllerName()
    {
        return isset($this->home['controller']) ? $this->home['controller'] : false;
    }

    /**
     * @param string|null $controller
     * @return bool|mixed
     */
    public function getDefaultActionName(string $controller = null)
    {
        if ($controller) {
            return isset($this->controllers[$controller]) ? $this->controllers[$controller]['action'] : false;
        } else {
            return isset($this->home['action']) ? $this->home['action'] : false;
        }
    }

    /**
     * @param $controller_name
     * @param bool $action
     * @return $this
     */
    protected function setHome($controller_name, $action = false)
    {
        $this->home = [
            'controller' => $controller_name,
            'action'     => $action ?: $this->getDefaultActionName($controller_name),
        ];

        return $this;
    }

    /**
     * @param $controller
     * @return AbstractController
     * @throws State
     */
    public function getControllerObject($controller): AbstractController
    {
        $class = $this->getControllerClass($controller);

        if ($class) {
            if (is_object($class)) { // it is already instance
                return $class;
            } else {
                return new $class($this);
            }
        } else {
            throw State::notFound("Controller $controller not found");
        }
    }

    /**
     * @param string $controller
     * @return AbstractController|bool
     */
    public function getControllerClass($controller)
    {
        return isset($this->controllers[$controller]) ? $this->controllers[$controller]['class'] : false;
    }

    /**
     * @param string $class
     * @param string $method
     * @return array
     * @throws State
     */
    public function getActionInfo(string $class, string $method): array
    {
        try {
            $me = new \ReflectionMethod($this->getControllerClass($class), $method . $this->controllers[$class]['postfix']);
        } catch (\Exception $e) {
            throw new State("Controller method not found", 404, $e);
        }

        return $me->getParameters();
    }

    /**
     * @param $controller
     * @param $class
     * @param string $action
     * @param int $access
     */
    protected function _addController(
        string $controller,
        string $class,
        string $action = 'none',
        int $access = User::ACCESS_GUEST
    ) {
        $this->controllers[$controller] = [
            'class'   => $class,
            'action'  => $action,
            'access'  => $access,
            'postfix' => "Action",
        ];
    }

    protected function invoke(AbstractController $controller, string $method, array &$params)
    {
        $args = [];

        /**
         * @var $info \ReflectionParameter
         */
        foreach ($info = $this->request->getInfo() as $info) {
            $name = $info->getName();

            if (isset($params[$name])) {
                $args[] = $params[$name];
            }
        }

        return call_user_func_array([$controller, $method .'Action'], $args);
    }

    /**
     * @param Request $request
     * @return int|mixed
     */
    public function dispatch(Request $request)
    {
        $this->request = $request;

        try {
            $request->parse($this);

            if (!$controller = $request->getController()) {
                throw State::notFound("No controller presets in request");
            }
            if (!$action = $request->getAction()) {
                throw State::notFound("No action presets in request");
            }

            if (!$this->hasRenderFormat($request->getFormat())) {
                throw State::notFound("Invalid url extension {$request->getFormat()}");
            }

            $request->setActor($this->getControllerObject($controller));

            if ($request->getBuffering()) {
                ob_start();
            }

            if ($info = $this->getActionInfo($controller, $action)) {
                $request->setInfo($info);

                $data = $this->invoke($request->getActor(), $request->getAction(), $request->getParams());

                $request->setData($data);
            }

            $this->display($request, null);

            return Request::OK;
        } catch (State\Render $e) {   // throw 200 code
//            $request->setData($e->data);

            $this->display($request, null);
        } catch (State $e) {
            $this->display($request, $e);
        } catch (\Exception $e) {
            $e = State::error($e);
            $this->display($request, $e);
        }

        $this->request = null;

        return $e->getCode();
    }
}
