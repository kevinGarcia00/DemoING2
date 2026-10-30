<?php

require_once __DIR__ . '/../../clases/UsuarioMySQLRepository.php';

function responderJson(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function leerJson(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        responderJson(400, ['success' => false, 'message' => 'JSON inválido']);
    }

    return $data;
}

function validarUsuario(array $data): array
{
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $apellido = trim((string) ($data['apellido'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $edad = filter_var($data['edad'] ?? 0, FILTER_VALIDATE_INT);

    if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responderJson(400, [
            'success' => false,
            'message' => 'Se requiere un nombre y un email válido',
        ]);
    }

    if ($edad === false || $edad < 0) {
        responderJson(400, [
            'success' => false,
            'message' => 'La edad debe ser un entero no negativo',
        ]);
    }

    return [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'edad' => $edad,
        'email' => $email,
    ];
}

function validarMetodo(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        header('Allow: ' . $method . ', OPTIONS');
        responderJson(405, ['success' => false, 'message' => 'Método no permitido']);
    }
}