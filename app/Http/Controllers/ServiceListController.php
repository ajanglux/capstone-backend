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

    /**
     * Display a listing of all services.
     */
    public function index(): JsonResponse
    {
        try {
            $services = $this->serviceRepository->getAll();
            return $this->responseSuccess($services, 'Services fetched successfully.');
        } catch (Exception $exception) {
            \Log::error('Error fetching services: '.$exception->getMessage());
            return $this->responseError([], 'Unable to fetch services.', 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceListRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $service = $this->serviceRepository->create($data);
            return $this->responseSuccess($service, 'Service created successfully.');
        } catch (Exception $exception) {
            // Log the error for debugging
            \Log::error('Error creating service: '.$exception->getMessage());
            return $this->responseError([], 'Unable to create service.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $service = $this->serviceRepository->getById($id);
            
            if (!$service) {
                throw new ModelNotFoundException('Service not found.');
            }

            return $this->responseSuccess($service, 'Service fetched successfully.');
        } catch (ModelNotFoundException $exception) {
            \Log::error('Service not found: '.$exception->getMessage());
            return $this->responseError([], 'Service not found.', 404);
        } catch (Exception $exception) {
            \Log::error('Error fetching service: '.$exception->getMessage());
            return $this->responseError([], 'Unable to fetch service.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceListRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $updatedService = $this->serviceRepository->update($id, $data);

            if (!$updatedService) {
                throw new ModelNotFoundException('Service not found for update.');
            }

            return $this->responseSuccess($updatedService, 'Service updated successfully.');
        } catch (ModelNotFoundException $exception) {
            \Log::error('Service not found for update: '.$exception->getMessage());
            return $this->responseError([], 'Service not found.', 404);
        } catch (Exception $exception) {
            \Log::error('Error updating service: '.$exception->getMessage());
            return $this->responseError([], 'Unable to update service.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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
