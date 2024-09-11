<?php

namespace App\Interfaces;

interface CustomerDetailInterface
{
    public function getAll(): array; 

    public function create(array $data): object|null;

    public function getById(int $id): object|null;

    public function update(int $id, array $data): object|null;

    public function delete(int $id): object|null;
}
