<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../clases/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	header('Allow: GET');
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Método no permitido; usa GET']);
	exit;
}

$connection = new Connection();
$usuarios = $connection->query(
	'SELECT idUsuario, nombre, apellido, edad, email FROM usuarios ORDER BY idUsuario'
);

if ($usuarios === false) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Error al listar los usuarios']);
	exit;
}

echo json_encode(['success' => true, 'message' => 'Usuarios listados correctamente', 'data' => $usuarios], JSON_UNESCAPED_UNICODE);