<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\TransactionExpense;
use Carbon\Carbon;
use App\Models\ExpenseDeduction;
use App\Models\AppNotification;
use App\Models\User;

class AdminController extends Controller
{
    // ── DASHBOARD ──────────────────────────────
    public function dashboard(Request $request)
{
    try { $this->runAutoOnField(); } catch (\Exception $e) { \Log::error($e->getMessage()); }

    $totalVehicles      = Vehicle::count();
    $activeReservations = Reservation::where('status', 'confirmed')
    ->whereDate('reservation_date', Carbon::today())
    ->count();
    $totalTenants       = Reservation::distinct('user_id')->count('user_id');
    $totalRevenue       = Transaction::sum('net_amount') - ExpenseDeduction::sum('amount');

    // Monthly Revenue for Chart
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $revenueLabels = $months;
    $revenueData = [];

    $monthlyNet = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(net_amount) as total')
        ->whereYear('transaction_date', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

    $monthlyDeductions = ExpenseDeduction::selectRaw('MONTH(deduction_date) as month, SUM(amount) as total')
        ->whereYear('deduction_date', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month');

    for ($i = 1; $i <= 12; $i++) {
        $net = ($monthlyNet[$i] ?? 0) - ($monthlyDeductions[$i] ?? 0);
        $revenueData[] = max($net, 0);
    }

    return view('admin.dashboard', compact(
        'totalVehicles', 'activeReservations', 'totalRevenue', 'totalTenants',
        'revenueData', 'revenueLabels'
    ));
}

    // ── BOOKINGS ───────────────────────────────
    public function bookings(Request $request)
{
    try { $this->runAutoOnField(); } catch (\Exception $e) { \Log::error($e->getMessage()); }

    $period     = $request->input('period', 'daily');
        $filterDate = $request->input('date', Carbon::today()->toDateString());

        // Active bookings (non-completed)
        $bookings = Reservation::with(['user', 'vehicle'])
            ->where('hidden_from_admin', false)
            ->whereIn('status', ['pending', 'confirmed', 'cancelled'])
            ->orderByDesc('created_at')
            ->get();

        // Stats counts
        $confirmed = $bookings->where('status', 'confirmed')->count();
        $pending   = $bookings->where('status', 'pending')->count();
        $cancelled = $bookings->where('status', 'cancelled')->count();
        $completed = Reservation::where('status', 'completed')->count();

        // Completed transactions with period filter
        $date = Carbon::parse($filterDate);
        [$startDate, $endDate] = match ($period) {
            'weekly'  => [$date->copy()->startOfWeek(),  $date->copy()->endOfWeek()],
            'monthly' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            default   => [$date->copy()->startOfDay(),   $date->copy()->endOfDay()],
        };

        $completedTransactions = Transaction::with(['reservation.user', 'vehicle', 'expenses'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->get();

        $deductions = ExpenseDeduction::whereBetween('deduction_date', [
            $startDate->toDateString(), $endDate->toDateString(),
        ])->get();

        $completedNet = $completedTransactions->sum('net_amount') - $deductions->sum('amount');

        return view('admin.bookings', compact(
            'bookings', 'confirmed', 'pending', 'cancelled', 'completed',
            'completedTransactions', 'deductions', 'completedNet', 'period', 'filterDate'
        ));
    }

    // ── BOOKING ACTION ─────────────────────────
    public function bookingAction(Request $request, $id)
    {
        $booking = Reservation::with(['vehicle', 'user'])->findOrFail($id);
        $action  = $request->input('action');

        $tenantId           = (int) $booking->user_id;
        $vehicleName        = optional($booking->vehicle)->name ?? 'the vehicle';
        $date               = Carbon::parse($booking->reservation_date)->format('M d, Y');
        $shouldNotifyTenant = $tenantId > 0;

        switch ($action) {

            case 'confirm':
                if ($booking->status === 'pending') {
                    $booking->update(['status' => 'confirmed']);
                    $this->notifyTenant($shouldNotifyTenant, $tenantId, 'booking_approved',
                        'Booking Approved! ✅',
                        "Your reservation for {$vehicleName} on {$date} has been approved.",
                        ['booking_id' => $booking->id]);
                    return back()->with('success', 'Booking confirmed.');
                }
                break;

            case 'cancel':
                if (in_array($booking->status, ['pending', 'confirmed'])) {
                    $booking->update(['status' => 'cancelled']);
                    $this->releaseVehicleIfFree($booking);
                    $this->notifyTenant($shouldNotifyTenant, $tenantId, 'booking_cancelled',
                        'Booking Cancelled ❌',
                        "Sorry, your reservation for {$vehicleName} on {$date} was cancelled.",
                        ['booking_id' => $booking->id]);
                    return back()->with('success', 'Booking cancelled.');
                }
                break;

            case 'delete':
                if (in_array($booking->status, ['pending', 'cancelled', 'completed'])) {
                    if (in_array($booking->status, ['cancelled', 'completed'])) {
                        $booking->update(['hidden_from_admin' => true]);
                    } else {
                        $booking->delete();
                    }
                    return back()->with('success', 'Booking deleted.');
                }
                break;

            case 'complete':
                if ($booking->status === 'confirmed') {
                    $booking->update(['status' => 'completed']);
                    $this->releaseVehicleIfFree($booking);

                      if (Transaction::where('reservation_id', $booking->id)->exists()) {
            return redirect()->route('admin.bookings')
                ->with('success', 'Booking already completed.');
        }

                    $vehicle  = $booking->vehicle;
                    $hectares = (float) ($booking->hectares ?? 1);
                    $gross    = ($vehicle->rate ?? 0) * $hectares;

                    // ── Expenses × hectares ──────────────────────
                    $driverPay     = (float) ($vehicle->driver_pay     ?? 0) * $hectares;
                    $dieselCost    = (float) ($vehicle->diesel_cost    ?? 0) * $hectares;
                    $helperPayEach = (float) ($vehicle->helper_pay_each ?? 0) * $hectares;
                    $helperCount = collect(['helper1_name','helper2_name','helper3_name'])
    ->filter(fn($k) => !empty($vehicle->$k))
    ->count();
                    $helperTotal   = $helperCount * $helperPayEach;
                    $totalExpenses = $driverPay + $helperTotal + $dieselCost;
                    $net           = max($gross - $totalExpenses, 0);

                    $transaction = Transaction::create([
                        'reservation_id'   => $booking->id,
                        'user_id'          => $booking->user_id,
                        'vehicle_id'       => $booking->vehicle_id,
                        'gross_amount'     => $gross,
                        'total_expenses'   => $totalExpenses,
                        'deductions'       => 0,
                        'net_amount'       => $net,
                        'audit_status'     => 'audited',
                        'transaction_date' => $booking->reservation_date,
                    ]);

                    // Save auto expenses (amounts already multiplied by hectares)
                    if ($vehicle->driver_name && $driverPay > 0) {
                        TransactionExpense::create([
                            'transaction_id' => $transaction->id,
                            'label'          => 'Driver: ' . $vehicle->driver_name . " ({$hectares} ha)",
                            'amount'         => $driverPay,
                        ]);
                    }
                    if ($vehicle->type === 'Harvester') {
                        foreach (['helper1_name', 'helper2_name', 'helper3_name'] as $hKey) {
                            if ($vehicle->$hKey && $helperPayEach > 0) {
                                TransactionExpense::create([
                                    'transaction_id' => $transaction->id,
                                    'label'          => 'Helper: ' . $vehicle->$hKey . " ({$hectares} ha)",
                                    'amount'         => $helperPayEach,
                                ]);
                            }
                        }
                    }
                    if ($dieselCost > 0) {
                        TransactionExpense::create([
                            'transaction_id' => $transaction->id,
                            'label'          => "Diesel ({$hectares} ha)",
                            'amount'         => $dieselCost,
                        ]);
                    }

                    $this->notifyTenant($shouldNotifyTenant, $tenantId, 'booking_completed',
                        'Booking Completed 🏁',
                        "Your booking for {$vehicleName} on {$date} is complete. How was your experience?",
                        ['booking_id' => $booking->id, 'reservation_id' => $booking->id]);

                    return redirect()->route('admin.bookings')
                        ->with('success', 'Booking completed! Staff expenses auto-deducted.');
                }
                break;
        }

        return back()->with('error', 'Invalid action or booking state.');
    }

    // ── NOTIFICATION HELPER ────────────────────
    private function notifyTenant(bool $should, int $tenantId, string $type, string $title, string $message, array $data = []): void
    {
        if (!$should || $tenantId <= 0) return;
        try {
            AppNotification::create(['user_id' => $tenantId, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => $data]);
        } catch (\Exception $e) {
            \Log::error("Notif FAILED — {$e->getMessage()}");
        }
    }

    // ── EXPENSES (GET / SAVE) ──────────────────
    public function getExpenses($id)
    {
        $transaction = Transaction::with('expenses')->findOrFail($id);
        return response()->json([
            'expenses'        => $transaction->expenses,
            'deductions'      => (float) $transaction->deductions,
            'deduction_notes' => $transaction->deduction_notes,
            'gross_amount'    => (float) $transaction->gross_amount,
            'net_amount'      => (float) $transaction->net_amount,
            'audit_status'    => $transaction->audit_status,
        ]);
    }

    public function saveExpenses(Request $request, $id)
    {
        $request->validate([
            'expenses'          => 'nullable|array',
            'expenses.*.label'  => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0',
            'deductions'        => 'nullable|numeric|min:0',
            'deduction_notes'   => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::with(['reservation', 'vehicle'])->findOrFail($id);
        $transaction->expenses()->delete();

        $totalExpenses = 0;
        foreach ($request->input('expenses', []) as $exp) {
            if (!empty($exp['label']) && is_numeric($exp['amount'])) {
                TransactionExpense::create([
                    'transaction_id' => $transaction->id,
                    'label'          => $exp['label'],
                    'amount'         => $exp['amount'],
                ]);
                $totalExpenses += (float) $exp['amount'];
            }
        }

        $deductions = (float) $request->input('deductions', 0);
        $net        = max((float) $transaction->gross_amount - $totalExpenses - $deductions, 0);

        $transaction->update([
            'total_expenses'  => $totalExpenses,
            'deductions'      => $deductions,
            'deduction_notes' => $request->input('deduction_notes'),
            'net_amount'      => $net,
            'audit_status'    => 'audited',
        ]);

        return response()->json([
            'success'        => true,
            'total_expenses' => $totalExpenses,
            'deductions'     => $deductions,
            'net_amount'     => $net,
            'audit_status'   => 'audited',
        ]);
    }

    // ── DEDUCTIONS ─────────────────────────────
    public function storeDeduction(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'reason'         => 'required|string|max:255',
            'deduction_date' => 'required|date',
        ]);
        $ded = ExpenseDeduction::create([
            'amount'         => $request->amount,
            'reason'         => $request->reason,
            'deduction_date' => $request->deduction_date,
        ]);
        return response()->json(['success' => true, 'deduction' => $ded]);
    }

    public function destroyDeduction($id)
    {
        ExpenseDeduction::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ── MISC ───────────────────────────────────
    public function bookingDetail($id) { return redirect()->route('admin.bookings'); }

    public function feedback()
    {
        $feedbacks    = \App\Models\Feedback::with(['user', 'reservation'])->latest()->get();
        $avgRating    = $feedbacks->count() > 0 ? round($feedbacks->avg('rating'), 1) : 0;
        $ratingCounts = [5=>0,4=>0,3=>0,2=>0,1=>0];
        foreach ($feedbacks as $f) { if (isset($ratingCounts[$f->rating])) $ratingCounts[$f->rating]++; }
        return view('admin.feedback', compact('feedbacks', 'avgRating', 'ratingCounts'));
    }

    public function feedbackAction(Request $request, $id) { return redirect()->route('admin.feedback'); }

    public function tenants()
    {
        $tenants = User::where('role', 'tenant')
            ->whereHas('reservations')
            ->withCount('reservations')
            ->get(['id', 'name', 'email', 'phone_number', 'address']);
        return view('admin.tenant', compact('tenants'));
    }

    public function blockTenant($id)
{
    $tenant = User::findOrFail($id);
    $name   = $tenant->name;
    $tenant->delete();

    return redirect()->route('admin.tenants')
        ->with('success', $name . '\'s account has been blocked and removed.');
}

    // ── HELPERS ────────────────────────────────
    private function releaseVehicleIfFree(Reservation $booking): void
    {
        if ($booking->vehicle && $booking->vehicle->status === 'onfield') {
            $hasOther = Reservation::where('vehicle_id', $booking->vehicle_id)
                ->where('status', 'confirmed')
                ->where('reservation_date', Carbon::today())
                ->exists();
            if (!$hasOther) $booking->vehicle->update(['status' => 'available']);
        }
    }

    public function runAutoOnField(): void
    {
        $today = Carbon::today()->toDateString();

        Reservation::where('status', 'confirmed')
            ->where('reservation_date', $today)
            ->with('vehicle')
            ->get()
            ->each(function ($r) {
                if ($r->vehicle && $r->vehicle->status !== 'maintenance') {
                    $r->vehicle->update(['status' => 'onfield']);
                }
            });

        Reservation::where('status', 'confirmed')
            ->where('reservation_date', '<', $today)
            ->with('vehicle')
            ->get()
            ->each(function ($r) {
                $r->update(['status' => 'completed']);

                if (!Transaction::where('reservation_id', $r->id)->exists()) {
    $vehicle  = $r->vehicle;
    if (!$vehicle) return;
    $hectares = (float) ($r->hectares ?? 1);
    $gross    = ($vehicle->rate ?? 0) * $hectares;

                    // ── Expenses × hectares ──────────────────────
                    $driverPay     = (float) ($vehicle->driver_pay     ?? 0) * $hectares;
                    $dieselCost    = (float) ($vehicle->diesel_cost    ?? 0) * $hectares;
                    $helperPayEach = (float) ($vehicle->helper_pay_each ?? 0) * $hectares;
                    $helperTotal   = ($vehicle->type === 'Harvester') ? 3 * $helperPayEach : 0;
                    $totalExpenses = $driverPay + $helperTotal + $dieselCost;
                    $net           = max($gross - $totalExpenses, 0);

                    $tx = Transaction::create([
                        'reservation_id'   => $r->id,
                        'user_id'          => $r->user_id,
                        'vehicle_id'       => $r->vehicle_id,
                        'gross_amount'     => $gross,
                        'total_expenses'   => $totalExpenses,
                        'deductions'       => 0,
                        'net_amount'       => $net,
                        'audit_status'     => 'audited',
                        'transaction_date' => $r->reservation_date,
                    ]);

                    if ($vehicle->driver_name && $driverPay > 0) {
                        TransactionExpense::create([
                            'transaction_id' => $tx->id,
                            'label'          => 'Driver: ' . $vehicle->driver_name . " ({$hectares} ha)",
                            'amount'         => $driverPay,
                        ]);
                    }
                    if ($vehicle->type === 'Harvester') {
                        foreach (['helper1_name', 'helper2_name', 'helper3_name'] as $hk) {
                            if ($vehicle->$hk && $helperPayEach > 0) {
                                TransactionExpense::create([
                                    'transaction_id' => $tx->id,
                                    'label'          => 'Helper: ' . $vehicle->$hk . " ({$hectares} ha)",
                                    'amount'         => $helperPayEach,
                                ]);
                            }
                        }
                    }
                    if ($dieselCost > 0) {
                        TransactionExpense::create([
                            'transaction_id' => $tx->id,
                            'label'          => "Diesel ({$hectares} ha)",
                            'amount'         => $dieselCost,
                        ]);
                    }
                }

                $this->releaseVehicleIfFree($r);
            });
    }
}