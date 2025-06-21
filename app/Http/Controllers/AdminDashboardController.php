<?php

namespace App\Http\Controllers;

use App\Models\CustomerDetail;
use App\Models\ServiceList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AdminDashboardController extends Controller
{
    public function getDashboardStats(): JsonResponse
    {
        try {
            $pendingRepairs = CustomerDetail::where('status', 'Pending')->count();
            $ongoingRepairs = CustomerDetail::where('status', 'On-Going')->count();

            $totalClients = User::Where('role', '0')->count();

            return response()->json([
                'pendingRepairs' => $pendingRepairs,
                'ongoingRepairs' => $ongoingRepairs,
                'totalClients' => $totalClients,
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to retrieve dashboard statistics.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function getMonthlyCompletedRepairs(): JsonResponse
    {
        try {
            $completedRepairs = CustomerDetail::selectRaw('MONTH(completed_updated_at) as month, COUNT(*) as count')
                ->where('status', 'Completed')
                ->whereNotNull('completed_updated_at')
                ->groupBy('month')
                ->pluck('count', 'month');

            $monthlyData = array_fill(0, 12, 0);

            foreach ($completedRepairs as $month => $count) {
                $monthlyData[$month - 1] = $count;
            }

            return response()->json($monthlyData, 200);

        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Failed to retrieve monthly completed repairs.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
