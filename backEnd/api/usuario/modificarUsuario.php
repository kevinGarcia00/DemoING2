<?php

require_once __DIR__ . '/bootstrap.php';
validarMetodo('PUT');

$data = leerJson();
$id = filter_var($data['idUsuario'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    responderJson(400, ['success' => false, 'message' => 'Se requiere un idUsuario válido']);
}

$usuario = validarUsuario($data);
$repository = new UsuarioMySQLRepository();

if (!$repository->modificar($id, $usuario)) {
    responderJson(500, ['success' => false, 'message' => 'Error al modificar el usuario']);
}

responderJson(200, ['success' => true, 'idUsuario' => $id, 'data' => $usuario]);