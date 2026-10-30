<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedPath = __DIR__ . $uri;

if (is_file($requestedPath) && $uri !== '/api.php') {
    return false;
}

require_once __DIR__ . '/api/api.php';