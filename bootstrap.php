<?php

// Root path for inclusion.
define('APP_DIR', __DIR__ . DIRECTORY_SEPARATOR);

// Require composer autoloader
require_once APP_DIR . 'vendor/autoload.php';

$config = new App\Core\Config(APP_DIR . 'config.php', APP_DIR . 'src/Configs');

return $config;
