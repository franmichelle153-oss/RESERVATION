<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppNotification;
use App\Models\User;
use Carbon\Carbon;

class TenantController extends Controller
{
    public function vehicle()
{
    $vehicles = Vehicle::latest()->get();

    $slotData = [];
    foreach ($vehicles as $v) {
        $reservations = Reservation::where('vehicle_id', $v->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->select('reservation_date', 'hectares', 'operator_name', 'status')
            ->get();
        $byDate = [];
        foreach ($reservations as $r) {
            $date = \Carbon\Carbon::parse($r->reservation_date)->format('Y-m-d');
            if (!isset($byDate[$date])) $byDate[$date] = ['used' => 0, 'max' => $v->max_hectares, 'tenants' => []];
            $byDate[$date]['used'] += $r->hectares;
            $byDate[$date]['tenants'][] = ['name' => $r->operator_name, 'hectares' => $r->hectares, 'status' => $r->status];
        }
        $slotData[$v->id] = $byDate;
    }

    $blockedDates = [];
    foreach ($vehicles as $v) {
        $blockedDates[$v->id] = [];
        if (!empty($slotData[$v->id])) {
            foreach ($slotData[$v->id] as $date => $info) {
                if ($info['used'] >= $info['max']) $blockedDates[$v->id][] = $date;
            }
        }
    }

    $statusBlocked = [];
    foreach ($vehicles as $v) {
        if (in_array($v->status, ['onfield', 'maintenance'])) $statusBlocked[$v->id] = $v->status;
    }

    // Staff data for price breakdown on tenant side
    $vehicleStaffData = [];
    foreach ($vehicles as $v) {
        $vehicleStaffData[$v->id] = [
            'type'            => $v->type,
            'rate'            => (float) $v->rate,
            'driver_name'     => $v->driver_name,
            'driver_pay'      => (float) ($v->driver_pay     ?? 0),
            'helper1_name'    => $v->helper1_name,
            'helper2_name'    => $v->helper2_name,
            'helper3_name'    => $v->helper3_name,
            'helper_pay_each' => (float) ($v->helper_pay_each ?? 0),
            'diesel_cost'     => (float) ($v->diesel_cost    ?? 0),
        ];
    }

    return view('tenant.vehicle', [
        'vehicles'         => $vehicles,
        'blockedDates'     => $blockedDates,
        'pendingDates'     => [],
        'statusBlocked'    => $statusBlocked,
        'slotData'         => $slotData,
        'vehicleStaffData' => $vehicleStaffData,
    ]);
}
    // ──────────────────────────────────────────
    //  STORE RESERVATION  ← NOTIFICATIONS
    // ──────────────────────────────────────────
    public function storeReservation(Request $request)
    {
        $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'operator_name'    => 'required|string|max:255',
            'contact_number'   => 'required|string|max:50',
            'location'         => 'required|string|max:255',
            'hectares'         => 'required|numeric|min:1',
            'reservation_date' => 'required|date|after_or_equal:today',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        if ($vehicle->status === 'maintenance') {
            return response()->json(['success' => false, 'message' => 'This vehicle is under maintenance.']);
        }

       $usedHectares = Reservation::where('vehicle_id', $request->vehicle_id)
    ->whereDate('reservation_date', $request->reservation_date)
    ->whereIn('status', ['pending', 'confirmed'])
    ->sum('hectares');

$remaining = $vehicle->max_hectares - $usedHectares;

if ($request->hectares > $remaining) {
    return response()->json([
        'success' => false,
        'message' => "Only {$remaining} ha remaining for that date. Please reduce your hectares.",
    ]);
}

        $reservation = Reservation::create([
            'user_id'          => auth()->id(),
            'vehicle_id'       => $request->vehicle_id,
            'operator_name'    => $request->operator_name,
            'contact_number'   => $request->contact_number,
            'location'         => $request->location,
            'hectares'         => $request->hectares,
            'reservation_date' => $request->reservation_date,
            'status'           => 'pending',
        ]);

        // ── Notify all admins — new booking placed ──
        // Skip if the admin account is the same as the tenant (test accounts)
        $currentUserId = (int) auth()->id();
        $tenantName    = auth()->user()->name;
        $vehicleName   = $vehicle->name;
        $date          = Carbon::parse($request->reservation_date)->format('M d, Y');

        User::where('role', 'admin')
    ->get()
            ->each(function ($admin) use ($tenantName, $vehicleName, $date, $reservation) {
                try {
                    AppNotification::create([
                        'user_id' => $admin->id,
                        'type'    => 'booking_placed',
                       'title' => 'New Booking Request — ' . Carbon::today()->format('M d, Y'),
                        'message' => "{$tenantName} reserved {$vehicleName} for {$date}. Tap to review.",
                        'data'    => ['booking_id' => $reservation->id],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Notif to admin failed: ' . $e->getMessage());
                }
            });

        return response()->json(['success' => true]);
    }

    public function deleteReservation($id)
    {
        Reservation::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'cancelled'])
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true]);
    }
// ─────────────────────────────────────────────────────────────────────────────
// IDAGDAG SA TenantController — i-paste ito pagkatapos ng deleteReservation()
// ─────────────────────────────────────────────────────────────────────────────

public function cancelReservation(Request $request, $id)
{
    $request->validate([
        'reason' => 'required|string|min:5|max:500',
    ]);

    // Hanapin ang reservation ng tenant na ito, pending lang ang pwedeng i-cancel
    $reservation = Reservation::where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending')
        ->firstOrFail();

    $reservation->update([
        'status'               => 'cancelled',
        'cancellation_reason'  => $request->reason,
    ]);

    // ── Notify all admins — tenant cancelled ──
    $tenantName  = auth()->user()->name;
    $vehicleName = $reservation->vehicle->name ?? 'Unknown Vehicle';
    $date        = Carbon::parse($reservation->reservation_date)->format('M d, Y');
    $reason      = $request->reason;

    User::where('role', 'admin')
        ->get()
        ->each(function ($admin) use ($tenantName, $vehicleName, $date, $reason, $reservation) {
            try {
                AppNotification::create([
                    'user_id' => $admin->id,
                    'type'    => 'booking_cancelled',
                    'title'   => 'Reservation Cancelled ❌',
                    'message' => "{$tenantName} cancelled their booking for {$vehicleName} on {$date}. Reason: {$reason}",
                    'data'    => ['booking_id' => $reservation->id],
                ]);
            } catch (\Exception $e) {
                \Log::error('Cancel notif to admin failed: ' . $e->getMessage());
            }
        });

    return response()->json(['success' => true]);
}

    // BAGO
public function history()
{
    $reservations = Reservation::where('user_id', Auth::id())
        ->with('vehicle')
        ->where('status', 'completed')
        ->whereNull('tenant_deleted_at')
        ->latest()->get();

    return view('tenant.history', compact('reservations'));
}

    public function deleteHistory($id)
{
    Reservation::where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'completed')
        ->firstOrFail()
        ->update(['tenant_deleted_at' => now()]);

    return response()->json(['success' => true]);
}

    // BAGO
public function deleteSelectedHistory(Request $request)
{
    $ids = $request->input('ids', []);
    if (empty($ids)) {
        return response()->json(['success' => false, 'message' => 'No items selected.']);
    }

    Reservation::whereIn('id', $ids)
        ->where('user_id', auth()->id())
        ->where('status', 'completed')
        ->update(['tenant_deleted_at' => now()]);

    return response()->json(['success' => true]);
}

    public function reservation()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with('vehicle')
            ->whereIn('status', ['pending', 'confirmed', 'cancelled'])
            ->latest()->get();

        return view('tenant.reservation', compact('reservations'));
    }
    public function submitFeedback(Request $request)
{
    $request->validate([
        'reservation_id' => 'required|exists:reservations,id',
        'rating'         => 'required|integer|min:1|max:5',
        'comment'        => 'nullable|string|max:1000',
    ]);

    // Iwasan ang duplicate feedback
    \App\Models\Feedback::updateOrCreate(
        ['reservation_id' => $request->reservation_id],
        [
            'user_id' => auth()->id(),
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]
    );

    return response()->json(['success' => true]);
}
}