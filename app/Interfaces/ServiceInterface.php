<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface ServiceInterface
{
    /**
     * Get all services without pagination.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Create a new service.
     *
     * @param array $data
     * @return object|null
     */
    public function create(array $data): ?object;

    /**
     * Get a service by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function getById(int $id): ?object;

    /**
     * Update an existing service by ID.
     *
     * @param int $id
     * @param array $data
     * @return object|null
     */
    public function update(int $id, array $data): ?object;

    /**
     * Delete a service by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function delete(int $id): ?object;
}
