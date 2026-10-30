<?php

interface UsuarioRepository
{
    public function alta(array $usuario): bool;

    public function modificar(int $id, array $usuario): bool;

    public function eliminar(int $id): bool;

    public function listar(): array;
}