<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AppNotification;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Period & date
        $period     = $request->input('period', 'daily');
        $filterDate = $request->input('date', Carbon::today()->toDateString());
        $date       = Carbon::parse($filterDate);

        // Build date range based on period
        switch ($period) {
            case 'weekly':
                $startDate = $date->copy()->startOfWeek();
                $endDate   = $date->copy()->endOfWeek();
                break;
            case 'monthly':
                $startDate = $date->copy()->startOfMonth();
                $endDate   = $date->copy()->endOfMonth();
                break;
            default: // daily
                $startDate = $date->copy()->startOfDay();
                $endDate   = $date->copy()->endOfDay();
                break;
        }

        // Transactions within the selected range
        $transactions = Transaction::with(['reservation.user', 'vehicle', 'expenses'])
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Daily/period totals
        $dailyGross    = $transactions->sum('gross_amount');
        $dailyExpenses = $transactions->sum('total_expenses');
        $dailyNet      = $transactions->sum('net_amount');

        // Stats cards
        $totalVehicles      = Vehicle::count();
        $activeReservations = Reservation::where('status', 'approved')->count();
        $totalTenants       = Reservation::distinct('user_id')->count('user_id');

        // ✅ Total Revenue = lahat ng audited net_amount MINUS lahat ng expense_deductions
        $auditedNet      = Transaction::where('audit_status', 'audited')->sum('net_amount');
        $totalDeductions = DB::table('expense_deductions')->sum('amount');
        $totalRevenue    = max($auditedNet - $totalDeductions, 0);

        return view('admin.dashboard', compact(
            'transactions',
            'filterDate',
            'period',
            'dailyGross',
            'dailyExpenses',
            'dailyNet',
            'totalVehicles',
            'activeReservations',
            'totalTenants',
            'totalRevenue',
        ));
    }
}