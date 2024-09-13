<?php

namespace App\Repositories;

use App\Models\ServiceList;
use App\Interfaces\ServiceInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ServiceRepository implements ServiceInterface
{
    public function getAll(): Collection
    {
        try {

            $services = ServiceList::all();

            $services = $services->map(function ($service) {
                if ($service->image) {
                    $service->image_url = url('storage/' . $service->image);
                } else {
                    $service->image_url = null;
                }
                return $service;
            });

            return $services;
        } catch (Exception $exception) {
            Log::error('Error fetching services: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function create(array $data): ?object
    {
        try {
            return ServiceList::create($data);
        } catch (Exception $exception) {
            Log::error('Error creating service: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function getById(int $id): ?object
    {
        try {
            $service = ServiceList::findOrFail($id);

            if ($service->image) {
                $service->image_url = url('storage/' . $service->image);
            }

            return $service;
        } catch (Exception $exception) {
            Log::error('Error fetching service by ID: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function update(int $id, array $data): ?object
    {
        try {
            $service = ServiceList::findOrFail($id);
            $service->update($data);

            if (isset($data['image']) && $data['image']) {
                $service->image_url = url('storage/' . $data['image']);
            }

            return $service;
        } catch (Exception $exception) {
            Log::error('Error updating service: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function delete(int $id): ?object
    {
        try {
            $service = ServiceList::findOrFail($id);
            $service->delete();
            return $service;
        } catch (Exception $exception) {
            Log::error('Error deleting service: ' . $exception->getMessage());
            throw $exception;
        }
    }
}
