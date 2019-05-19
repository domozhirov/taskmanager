<?php

namespace App\Core;

abstract class AbstractController
{
    /**
     * @var Engine
     */
    public $engine;

    /**
     * @var Config
     */
    public $config;

    /**
     * AbstractController constructor.
     * @param Engine $engine
     */
    public function __construct(Engine $engine) {
        $this->engine = $engine;
        $this->config = $engine->getConfig();

        if ($user = User::getCurrent()) {
            $engine->setUser($user);
        }
    }

    /**
     * @param $name
     * @param $params
     * @throws State
     */
    public function __call($name, $params) {
        throw State::notFound("Action " . __METHOD__ . " not found");
    }
}
