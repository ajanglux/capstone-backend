<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Repositories\ServiceRepository;
use App\Http\Requests\ServiceListRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceListController extends Controller
{
    use ResponseTrait;

    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index(): JsonResponse
    {
        try {

            $services = $this->serviceRepository->getAll();

            $services = $services->map(function ($service) {
                if ($service->image) {
                    $service->image_url = url('storage/' . $service->image);
                }
                return $service;
            });

            return $this->responseSuccess($services, 'Services fetched successfully.');
        } catch (Exception $exception) {
            \Log::error('Error fetching services: ' . $exception->getMessage());
            return $this->responseError([], 'Unable to fetch services.', 500);
        }
    }

    public function store(ServiceListRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        try {
            $service = $this->serviceRepository->create($data);
            return $this->responseSuccess($service, 'Service created successfully.');
        } catch (Exception $exception) {
            \Log::error('Error creating service: ' . $exception->getMessage());
            return $this->responseError([], 'Unable to create service.', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $service = $this->serviceRepository->getById($id);

            if ($service && $service->image) {
                $service->image_url = url('storage/' . $service->image);
            }

            return $this->responseSuccess($service, 'Service fetched successfully.');
        } catch (Exception $exception) {
            \Log::error('Error fetching service: ' . $exception->getMessage());
            return $this->responseError([], 'Unable to fetch service.', 500);
        }
    }
 
    public function update(ServiceListRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = $imagePath;
        }

        try {
            $updatedService = $this->serviceRepository->update($id, $data);
            return $this->responseSuccess($updatedService, 'Service updated successfully.');
        } catch (Exception $exception) {
            \Log::error('Error updating service: ' . $exception->getMessage());
            return $this->responseError([], 'Unable to update service.', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deletedService = $this->serviceRepository->delete($id);

            if (!$deletedService) {
                throw new ModelNotFoundException('Service not found for deletion.');
            }

            return $this->responseSuccess([], 'Service deleted successfully.');
        } catch (ModelNotFoundException $exception) {
            \Log::error('Service not found for deletion: '.$exception->getMessage());
            return $this->responseError([], 'Service not found.', 404);
        } catch (Exception $exception) {
            \Log::error('Error deleting service: '.$exception->getMessage());
            return $this->responseError([], 'Unable to delete service.', 500);
        }
    }
}
