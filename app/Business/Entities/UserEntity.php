<?php

namespace App\Business\Entities;

use InvalidArgumentException;

class UserEntity
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?int $role_id = null,
        public ?string $password = null,
        public ?string $uuid = null
    ) {}

    /**
     * Construye la entidad a partir de un arreglo de datos limpios.
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            role_id: isset($data['role_id']) ? (int) $data['role_id'] : null,
            password: $data['password'] ?? $data['new_password'] ?? null,
            uuid: $data['uuid'] ?? null
        );
    }

    /**
     * Convierte la entidad a un arreglo, filtrando nulos para actualizaciones parciales
     * y garantizando que 'uuid' e 'id' no se incluyan en payloads de actualización.
     */
    public function toArrayForUpdate(): array
    {
        $data = array_filter([
            'name'     => $this->name,
            'email'    => $this->email,
            'role_id'  => $this->role_id,
            'password' => $this->password,
        ], fn($value) => $value !== null);

        return $data;
    }

    /**
     * Exportación completa del estado de la entidad.
     */
    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'role_id'  => $this->role_id,
            'password' => $this->password,
            'uuid'     => $this->uuid,
        ];
    }
}