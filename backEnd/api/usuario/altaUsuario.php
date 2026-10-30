<?php

require_once __DIR__ . '/bootstrap.php';
validarMetodo('POST');

$usuario = validarUsuario(leerJson());
$repository = new UsuarioMySQLRepository();

if (!$repository->alta($usuario)) {
    responderJson(500, ['success' => false, 'message' => 'Error al crear el usuario']);
}

responderJson(201, ['success' => true, 'data' => $usuario]);