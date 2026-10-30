<?php

require_once __DIR__ . '/bootstrap.php';
validarMetodo('GET');

$repository = new UsuarioMySQLRepository();
responderJson(200, $repository->listar());