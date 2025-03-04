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
use Illuminate\Support\Facades\Auth;
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
            return $this->responseSuccess($customerDetails, 'Customer details fetched successfullyS.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function myListRepair(): JsonResponse
    {
        try {
            $user_id = auth()->id();
            $customerDetails = $this->customerDetailRepository->getUserAll($user_id);
            return $this->responseSuccess($customerDetails, 'Customer details fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function store(CustomerDetailRequest $request)
    {
        $data = $request->validated();

        // Define default values for customer details
        $defaultCustomerDetails = [
            'status' => 'Pending', // Default status
            'description' => 'No description provided', // Default description
            'user_id' => $request->has('user_id') ? $request->user_id : auth()->id(), // Default to logged-in user
        ];

        // Merge default values with incoming request data
        $data = array_merge($defaultCustomerDetails, $data);

        // Extract only the allowed product info fields
        $productInfoData = $request->only([
            'brand', 'model', 'serial_number', 'purchase_date', 'documentation', 'warranty_status',
            'ac_adapter', 'vga_cable', 'dvi_cable', 'display_cable', 'bag_pn',
            'hdd', 'ram_brand', 'ram_size_gb', 'power_cord_qty', 'description_of_repair'
        ]);

        try {
            $customerDetail = $this->customerDetailRepository->create($data);

            // Ensure product info has at least one non-empty value before saving
            if (!empty(array_filter($productInfoData))) { 

                // Assign a default value if serial_number is empty
                if (empty($productInfoData['serial_number'])) {
                    $productInfoData['serial_number'] = 'N/A';
                }

                if (empty($productInfoData['purchase_date'])) {
                    $productInfoData['purchase_date'] = null;  // Set to NULL instead of 'N/A'
                }         
                
                if (empty($productInfoData['warranty_status'])) {
                    $productInfoData['warranty_status'] = 'Not Specified';  // Set a default value
                }
                
                $productInfo = new ProductInfo($productInfoData);
                $customerDetail->productInfos()->save($productInfo);
            }

            return $this->responseSuccess($customerDetail, 'Customer detail created successfully.');
        } catch (Exception $exception) {
            $statusCode = ($exception->getCode() > 99 && $exception->getCode() < 600) ? (int) $exception->getCode() : 500;
            return $this->responseError([], $exception->getMessage(), $statusCode);
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
            'ac_adapter', 'vga_cable', 'dvi_cable', 'display_cable', 'bag_pn',
            'hdd', 'ram_brand', 'ram_size_gb', 'power_cord_qty', 'description_of_repair'
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
            'status' => 'sometimes|string|in:Pending,On-Going,Finished,Ready-for-Pickup,Completed,Cancelled,Unrepairable,Responded',
            'cancel_reason' => 'required_if:status,Cancelled|string|max:255',
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
            }

            if ($validatedData['status'] === 'Cancelled') {
                $customerDetail->cancel_reason = $validatedData['cancel_reason'];
                $customerDetail->cancelled_updated_at = now();
            }

            $customerDetail->status = $validatedData['status'];
            $customerDetail->status_updated_at = now();
            $customerDetail->save();

            return $this->responseSuccess($customerDetail, 'Status updated successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), 500); // Always return 500 for unexpected errors
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
                'unrepairable_updated_at' => $customerDetail->unrepairable_updated_at,
            ], 'Customer status fetched successfully.');
        } catch (ModelNotFoundException $exception) {
            return $this->responseError([], 'Customer detail not found.', 404);
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

    public function showHomeStatus()
    {
        try {
            $user = Auth::user();

            // Retrieve the latest customer detail associated with the user
            $customerDetailData = $user->customerDetail()
                ->orderBy('created_at', 'desc')  // Ensure it's the most recent inquiry
                ->first();

            if (!$customerDetailData) {
                throw new ModelNotFoundException();
            }

            return $this->responseSuccess([
                'status' => $customerDetailData->status,
                'status_updated_at' => $customerDetailData->status_updated_at,
                'on_going_updated_at' => $customerDetailData->on_going_updated_at,
                'finished_updated_at' => $customerDetailData->finished_updated_at,
                'ready_for_pickup_updated_at' => $customerDetailData->ready_for_pickup_updated_at,
                'completed_updated_at' => $customerDetailData->completed_updated_at,
                'cancelled_updated_at' => $customerDetailData->cancelled_updated_at,
                'unrepairable_updated_at' => $customerDetailData->unrepairable_updated_at,
                'responded_updated_at' => $customerDetailData->responded_updated_at,
                'comment' => $customerDetailData->comment,
                'admin_comment_updated_at' => $customerDetailData->admin_comment_updated_at,
                'code' => $customerDetailData->code,
                'description' => $customerDetailData->description,
                'description_updated_at' => $customerDetailData->description_updated_at,
                // 'cancel_reason' => $customerDetailData->cancel_reason,
                // 'cancel_reason_updated_at' => $customerDetailData->cancel_reason_updated_at,
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

            $customerDetail = CustomerDetail::with('productInfos', 'user')->findOrFail($id);

            return response()->json($customerDetail, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'CustomerDetail not found'], 404);
        }
    }

    // public function cancel_reason(Request $request, $id)
    // {
    //     $request->validate([
    //         'cancel_reason' => 'nullable|string|max:255',
    //         // 'status' => 'nullable|string|max:255',
    //     ]);

    //     $repair = CustomerDetail::findOrFail($id);
    //     $repair->update([
    //         'cancel_reason' => $request->cancel_reason,
    //         // 'status' => "Responded",
    //     ]);

    //     return response()->json([
    //         'message' => 'Reason updated successfully.',
    //         'data' => $repair
    //     ]);
    // }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:255',
            // 'status' => 'nullable|string|max:255',
        ]);

        $repair = CustomerDetail::findOrFail($id);
        $repair->update([
            'comment' => $request->comment,
            // 'status' => "Responded",
        ]);

        return response()->json([
            'message' => 'Comment updated successfully.',
            'data' => $repair
        ]);
    }

    public function checkInquiries(Request $request)
    {
        $userId = auth()->id();
        $hasPendingInquiry = CustomerDetail::where('user_id', $userId)
            ->whereNotIn('status', ['Completed', 'Responded'])
            ->exists();

        return response()->json([
            'hasPendingInquiry' => $hasPendingInquiry
        ]);
    }

    public function showAllDescriptions(): JsonResponse
    {
        try {
            $user = Auth::user();

            $customerDetails = $user->customerDetail()->get(['description']);

            if ($customerDetails->isEmpty()) {
                return $this->responseError([], 'No customer details found for this user.', 404);
            }
            
            return $this->responseSuccess($customerDetails, 'Customer descriptions fetched successfully.');
        } catch (Exception $exception) {
            return $this->responseError([], $exception->getMessage(), $exception->getCode());
        }
    }

}
