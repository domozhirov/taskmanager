<?php

$config = require_once dirname(__DIR__) . '/bootstrap.php';
$engine = new App\Core\Engine($config);
$request = new App\Core\Request;

try {
    $engine->dispatch($request);
} catch (Exception $e) {
    $engine->display($request, $e);
} catch (Throwable $e) {
    $error = new App\Core\State('Fatal Error');

    $engine->display($request, $error);
}
