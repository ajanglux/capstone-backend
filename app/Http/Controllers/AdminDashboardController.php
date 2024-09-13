<?php

namespace App\Http\Controllers;

use App\Models\CustomerDetail;
use App\Models\ServiceList;
use Illuminate\Http\JsonResponse;
use Exception;

class AdminDashboardController extends Controller
{
    public function getDashboardStats(): JsonResponse
    {
        try {
            $pendingRepairs = CustomerDetail::where('status', 'pending')->count();
            $ongoingRepairs = CustomerDetail::where('status', 'on-going')->count();
            $totalServices = ServiceList::count();
            $totalClients = CustomerDetail::count();

            return response()->json([
                'pendingRepairs' => $pendingRepairs,
                'ongoingRepairs' => $ongoingRepairs,
                'totalServices' => $totalServices,
                'totalClients' => $totalClients,
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to retrieve dashboard statistics.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
