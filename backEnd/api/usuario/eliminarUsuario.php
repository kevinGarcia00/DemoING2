<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../clases/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    header('Allow: DELETE');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido; usa DELETE']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido']);
    exit;
}

$id = filter_var($data['idUsuario'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Se requiere un idUsuario entero positivo']);
    exit;
}

$connection = new Connection();
$result = $connection->query(
    'DELETE FROM usuarios WHERE idUsuario = ?',
    'i',
    [$id]
);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
    exit;
}

if ($result === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No existe un usuario con ese idUsuario']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);