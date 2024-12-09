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
use App\Models\CustomerDetail; 
use Twilio\Rest\Client;
use App\Services\TwilioService;

class CustomerDetailController extends Controller
{
    use ResponseTrait;

    protected $customerDetailRepository;
    protected $twilioService;

    public function __construct(CustomerDetailRepository $customerDetailRepository, TwilioService $twilioService)
    {
        $this->customerDetailRepository = $customerDetailRepository;
        $this->twilioService = $twilioService;
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
        $productInfoData = $request->only([
            'brand', 'model', 'serial_number', 'purchase_date', 'documentation', 'warranty_status',
            'orig_box', 'gen_box', 'manual', 'driver_cd', 'sata_cable', 'simcard_memorycard_gb',
            'remote_control', 'receiver', 'backplate_metal_plate', 'ac_adapter', 'battery_pack',
            'lithium_battery', 'vga_cable', 'dvi_cable', 'display_cable', 'bag_pn', 'swivel_base',
            'hdd', 'ram_brand', 'ram_size_gb', 'power_cord_qty', 'printer_cable_qty', 'usb_cable_qty',
            'paper_tray_qty', 'screw_qty', 'jack_cable_qty'
        ]);

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

            $customerDetail->load('productInfos');

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
        $productInfoData = $request->only([
            'brand', 'model', 'serial_number', 'purchase_date', 'documentation', 'warranty_status',
            'orig_box', 'gen_box', 'manual', 'driver_cd', 'sata_cable', 'simcard_memorycard_gb',
            'remote_control', 'receiver', 'backplate_metal_plate', 'ac_adapter', 'battery_pack',
            'lithium_battery', 'vga_cable', 'dvi_cable', 'display_cable', 'bag_pn', 'swivel_base',
            'hdd', 'ram_brand', 'ram_size_gb', 'power_cord_qty', 'printer_cable_qty', 'usb_cable_qty',
            'paper_tray_qty', 'screw_qty', 'jack_cable_qty'
        ]);
    
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

            $customerDetail = CustomerDetail::find($id);
            if ($customerDetail) {
                $customerDetail->productInfos()->delete();
            }

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
            'status' => 'sometimes|string|in:Pending,On-Going,Finished,Ready-for-Pickup,Completed,Cancelled,Incomplete,Responded',
        ]);

        try {
            $customerDetail = $this->customerDetailRepository->getById($id);

            if (!$customerDetail) {
                return $this->responseError([], 'Customer detail not found.', 404);
            }

            if ($validatedData['status'] === 'On-Going') {
                if (!$customerDetail->isCompletelyFilled()) {
                    return $this->responseError([], 'Customer details must be completely filled before updating status.', 422);
                }

                $getCode = $customerDetail->code;
                $this->twilioService->sendSms($customerDetail->phone_number, "Your code to check the status of your repair is: $getCode");
            }

            $customerDetail->status = $validatedData['status'];
            $customerDetail->status_updated_at = now();
            $customerDetail->save();

            return $this->responseSuccess($customerDetail, 'Status updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function showStatus(string $code): JsonResponse
    {
        try {
            $customerDetail = $this->customerDetailRepository->getByCode($code);
    
            if (!$customerDetail) {
                throw new ModelNotFoundException();
            }
    
            return $this->responseSuccess([
                'status' => $customerDetail->status,
                'status_updated_at' => $customerDetail->status_updated_at,
                'on_going_updated_at' => $customerDetail->on_going_updated_at,
                'finished_updated_at' => $customerDetail->finished_updated_at,
                'ready_for_pickup_updated_at' => $customerDetail->ready_for_pickup_updated_at,
                'completed_updated_at' => $customerDetail->completed_updated_at,
                'cancelled_updated_at' => $customerDetail->cancelled_updated_at,
                'incomplete_updated_at' => $customerDetail->incomplete_updated_at,
                'responded_updated_at' => $customerDetail->responded_updated_at,
            ], 'Customer status fetched successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->responseError([], 'Customer detail not found.', 404);
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }
    
    public function showWithProductInfo($id): JsonResponse
    {
        try {

            $customerDetail = CustomerDetail::with('productInfos')->findOrFail($id);

            return response()->json($customerDetail, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'CustomerDetail not found'], 404);
        }
    }
}
