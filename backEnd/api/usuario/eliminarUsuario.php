<?php

require_once __DIR__ . '/bootstrap.php';
validarMetodo('DELETE');

$data = leerJson();
$id = filter_var($data['idUsuario'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    responderJson(400, ['success' => false, 'message' => 'Se requiere un idUsuario válido']);
}

$repository = new UsuarioMySQLRepository();
if (!$repository->eliminar($id)) {
    responderJson(500, ['success' => false, 'message' => 'Error al eliminar el usuario']);
}

responderJson(200, ['success' => true, 'message' => 'Usuario eliminado correctamente']);