<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../clases/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    header('Allow: PUT');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido; usa PUT']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido']);
    exit;
}

$id = filter_var($data['idUsuario'] ?? null, FILTER_VALIDATE_INT);
$nombre = $data['nombre'] ?? null;
$apellido = $data['apellido'] ?? '';
$email = $data['email'] ?? null;
$edad = filter_var($data['edad'] ?? null, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Se requiere un idUsuario entero positivo']);
    exit;
}

if (!is_string($nombre) || trim($nombre) === ''
    || !is_string($apellido)
    || !is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)
    || $edad === false || $edad < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Revisa nombre, apellido, edad y email']);
    exit;
}

$usuario = [
    'nombre' => trim($nombre),
    'apellido' => trim($apellido),
    'edad' => $edad,
    'email' => trim($email),
];

$connection = new Connection();
$result = $connection->query(
    'UPDATE usuarios SET nombre = ?, apellido = ?, edad = ?, email = ? WHERE idUsuario = ?',
    'ssisi',
    [$usuario['nombre'], $usuario['apellido'], $usuario['edad'], $usuario['email'], $id]
);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al modificar el usuario']);
    exit;
}

if ($result === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No existe un usuario con ese idUsuario']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Usuario modificado correctamente', 'idUsuario' => $id, 'data' => $usuario], JSON_UNESCAPED_UNICODE);