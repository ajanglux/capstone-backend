<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Repositories\CustomerDetailRepository;
use App\Http\Requests\CustomerDetailRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerDetailController extends Controller
{
    use ResponseTrait;

    protected $customerDetailRepository;

    public function __construct(CustomerDetailRepository $customerDetailRepository)
    {
        $this->customerDetailRepository = $customerDetailRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $customerDetails = $this->customerDetailRepository->getAll();
            return $this->responseSuccess($customerDetails, 'Customer details fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function store(CustomerDetailRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $customerDetail = $this->customerDetailRepository->create($data);
            return $this->responseSuccess($customerDetail, 'Customer detail created successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $customerDetail = $this->customerDetailRepository->getById($id);

            if (!$customerDetail) {
                throw new ModelNotFoundException();
            }

            return $this->responseSuccess($customerDetail, 'Customer detail fetched successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->responseError([], 'Customer detail not found.', 404);
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function update(CustomerDetailRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        try {
            $updatedCustomerDetail = $this->customerDetailRepository->update($id, $data);

            if ($updatedCustomerDetail) {
                return $this->responseSuccess($updatedCustomerDetail, 'Customer detail updated successfully.');
            } else {
                return $this->responseError([], 'Customer detail not updated. Please try again.', 500);
            }
        } catch (ModelNotFoundException $exception) {
            return $this->responseError([], 'Customer detail not found.', 404);
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode() ?: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deletedCustomerDetail = $this->customerDetailRepository->delete($id);

            if ($deletedCustomerDetail) {
                return $this->responseSuccess([], 'Customer detail deleted successfully.');
            } else {
                return $this->responseError([], 'Customer detail not found.', 404);
            }
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
