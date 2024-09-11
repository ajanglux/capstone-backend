<?php

namespace App\Interfaces;

interface ServiceInterface
{
    public function getAll(): array;

    public function create(array $data): ?object;

    public function getById(int $id): ?object;

    public function update(int $id, array $data): ?object;

    public function delete(int $id): ?object;
}
