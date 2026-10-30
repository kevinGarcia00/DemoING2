<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedPath = __DIR__ . $uri;

// Si la petición corresponde a un archivo real, lo servimos directamente.
// Esto permite usar el servidor embebido para archivos estáticos sin pasar por el router.
if (is_file($requestedPath) && $uri !== '/api.php') {
    return false;
}

require_once __DIR__ . '/api/api.php';