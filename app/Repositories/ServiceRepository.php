<?php

namespace App\Repositories;

use App\Models\ServiceList;
use App\Interfaces\ServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class ServiceRepository implements ServiceInterface
{
    /**
     * Get all services without pagination.
     */
    public function getAll(): array
    {
        try {
            return ServiceList::all()->toArray();  // Fetch all services
        } catch (Exception $exception) {
            Log::error('Error fetching services: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Create a new service.
     */
    public function create(array $data): ?object
    {
        try {
            return ServiceList::create($data);
        } catch (Exception $exception) {
            Log::error('Error creating service: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Get a service by ID.
     */
    public function getById(int $id): ?object
    {
        try {
            return ServiceList::findOrFail($id);
        } catch (Exception $exception) {
            Log::error('Error fetching service by ID: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Update an existing service by ID.
     */
    public function update(int $id, array $data): ?object
    {
        try {
            $service = ServiceList::findOrFail($id);
            $service->update($data);
            return $service;
        } catch (Exception $exception) {
            Log::error('Error updating service: '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Delete a service by ID.
     */
    public function delete(int $id): ?object
    {
        try {
            $service = ServiceList::findOrFail($id);
            $service->delete();
            return $service;
        } catch (Exception $exception) {
            Log::error('Error deleting service: '.$exception->getMessage());
            throw $exception;
        }
    }
}
