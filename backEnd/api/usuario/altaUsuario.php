<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../clases/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido; usa POST']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido']);
    exit;
}

if (array_key_exists('idUsuario', $data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'idUsuario se genera automáticamente; no lo envíes al crear']);
    exit;
}

$nombre = $data['nombre'] ?? null;
$apellido = $data['apellido'] ?? '';
$email = $data['email'] ?? null;
$edad = filter_var($data['edad'] ?? null, FILTER_VALIDATE_INT);

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
    'INSERT INTO usuarios (nombre, apellido, edad, email) VALUES (?, ?, ?, ?)',
    'ssis',
    [$usuario['nombre'], $usuario['apellido'], $usuario['edad'], $usuario['email']]
);

if ($result === false || $result < 1) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear el usuario']);
    exit;
}

http_response_code(201);
echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente', 'data' => $usuario], JSON_UNESCAPED_UNICODE);