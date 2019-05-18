<?php

$config = require_once dirname(__DIR__) . '/bootstrap.php';
$engine = new App\Core\Engine($config);

try {
    $engine->dispatch(new App\Core\Request);
} catch (Throwable $e) {
    die('Fatal Error');
}
