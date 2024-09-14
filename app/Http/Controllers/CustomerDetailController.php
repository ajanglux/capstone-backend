<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Repositories\CustomerDetailRepository;
use App\Http\Requests\CustomerDetailRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\ProductInfo;

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
        $productInfoData = $request->only(['brand', 'model', 'serial_number', 'purchase_date']);

        try {
            $customerDetail = $this->customerDetailRepository->create($data);

            if (!empty($productInfoData)) {
                $productInfo = new ProductInfo($productInfoData);
                $customerDetail->productInfos()->save($productInfo);
            }

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
        $productInfoData = $request->only(['brand', 'model', 'serial_number', 'purchase_date']);

        try {
            $updatedCustomerDetail = $this->customerDetailRepository->update($id, $data);

            if (!empty($productInfoData)) {
                $productInfo = ProductInfo::where('customer_detail_id', $id)->first();
                if ($productInfo) {
                    $productInfo->update($productInfoData);
                } else {
                    $newProductInfo = new ProductInfo($productInfoData);
                    $updatedCustomerDetail->productInfos()->save($newProductInfo);
                }
            }

            return $this->responseSuccess($updatedCustomerDetail, 'Customer detail updated successfully.');
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

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:pending,on-going,finished,completed',
        ]);

        try {
            $customerDetail = $this->customerDetailRepository->getById($id);

            if (!$customerDetail) {
                return $this->responseError([], 'Customer detail not found.', 404);
            }

            $customerDetail->status = $validatedData['status'];
            $customerDetail->save();

            return $this->responseSuccess($customerDetail, 'Status updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
}
