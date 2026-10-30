<?php
header('Content-Type: application/json');

// Capturamos la ruta y la separamos en segmentos.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

// Detectamos el método HTTP.
$method = $_SERVER['REQUEST_METHOD'];

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$route = $_GET['route'] ?? $_GET['action'] ?? '';
if ($route === '') {
    if (isset($segments[1]) && $segments[1] !== '') {
        $route = $segments[1];
    } elseif (isset($segments[0]) && $segments[0] !== '') {
        $route = $segments[0];
    }
}

switch ($route) {
    case 'health':
        require __DIR__ . '/health.php';
        break;
    case 'altaUsuario':
        require __DIR__ . '/usuario/altaUsuario.php';
        break;
    case 'modificarUsuario':
        require __DIR__ . '/usuario/modificarUsuario.php';
        break;
    case 'eliminarUsuario':
        require __DIR__ . '/usuario/eliminarUsuario.php';
        break;
    case 'listadoUsuarios':
        require __DIR__ . '/usuario/listadoUsuarios.php';
        break;
    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'error' => 'Recurso no encontrado'
        ]);
}