<?php

require_once __DIR__ . '/UsuarioRepository.php';
require_once __DIR__ . '/connection.php';

class UsuarioMySQLRepository implements UsuarioRepository
{
    private Connection $conexion;

    public function __construct()
    {
        $this->conexion = new Connection();
    }

    public function alta(array $usuario): bool
    {
        return $this->conexion->query(
            'INSERT INTO usuarios (nombre, apellido, edad, email) VALUES (?, ?, ?, ?)',
            'ssis',
            [$usuario['nombre'], $usuario['apellido'], $usuario['edad'], $usuario['email']]
        ) !== false;
    }

    public function modificar(int $id, array $usuario): bool
    {
        return $this->conexion->query(
            'UPDATE usuarios SET nombre = ?, apellido = ?, edad = ?, email = ? WHERE idUsuario = ?',
            'ssisi',
            [$usuario['nombre'], $usuario['apellido'], $usuario['edad'], $usuario['email'], $id]
        ) !== false;
    }

    public function eliminar(int $id): bool
    {
        return $this->conexion->query(
            'DELETE FROM usuarios WHERE idUsuario = ?',
            'i',
            [$id]
        ) !== false;
    }

    public function listar(): array
    {
        $usuarios = $this->conexion->query(
            'SELECT idUsuario, nombre, apellido, edad, email FROM usuarios ORDER BY idUsuario'
        );

        return is_array($usuarios) ? $usuarios : [];
    }
}