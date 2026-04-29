<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AppNotification;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::latest()->get();
        return view('admin.vehicle', compact('vehicles'));
    }

   public function store(Request $request)
{
    $request->validate([
        'name'            => 'required|string|max:255',
        'type'            => 'required|string',
        'rate'            => 'required|numeric|min:1',
        'max_hectares'    => 'required|integer|min:1',
        'status'          => 'required|in:available,onfield,maintenance',
        'photo'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'driver_name'     => 'nullable|string|max:255',
        'driver_pay'      => 'nullable|numeric|min:0',
        'helper1_name'    => 'nullable|string|max:255',
        'helper2_name'    => 'nullable|string|max:255',
        'helper3_name'    => 'nullable|string|max:255',
        'helper_pay_each' => 'nullable|numeric|min:0',
        'diesel_cost'     => 'nullable|numeric|min:0',
    ]);

    $imageData = null;
    if ($request->hasFile('photo')) {
        $imageData = 'data:' . $request->file('photo')->getMimeType() . ';base64,' . base64_encode(file_get_contents($request->file('photo')->getRealPath()));
    }

    Vehicle::create([
        'name'            => $request->name,
        'type'            => $request->type,
        'rate'            => $request->rate,
        'max_hectares'    => $request->max_hectares,
        'status'          => $request->status,
        'image_data'      => $imageData,
        'driver_name'     => $request->driver_name,
        'driver_pay'      => $request->driver_pay      ?? 0,
        'helper1_name'    => $request->helper1_name,
        'helper2_name'    => $request->helper2_name,
        'helper3_name'    => $request->helper3_name,
        'helper_pay_each' => $request->helper_pay_each ?? 0,
        'diesel_cost'     => $request->diesel_cost     ?? 0,
    ]);

    return redirect()->route('admin.vehicle')->with('success', 'Vehicle added successfully!');
}

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'type'               => 'required|string',
            'rate'               => 'required|numeric|min:1',
            'max_hectares'       => 'required|integer|min:1',
            'status'             => 'required|in:available,onfield,maintenance',
            'photo'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'estimated_fix_days' => 'nullable|integer|min:1|max:365',
            'driver_name'        => 'nullable|string|max:255',
            'driver_pay'         => 'nullable|numeric|min:0',
            'helper1_name'       => 'nullable|string|max:255',
            'helper2_name'       => 'nullable|string|max:255',
            'helper3_name'       => 'nullable|string|max:255',
            'helper_pay_each'    => 'nullable|numeric|min:0',
            'diesel_cost'        => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $vehicle->status;
        $newStatus = $request->status;

       if ($request->hasFile('photo')) {
    $vehicle->image_data = 'data:' . $request->file('photo')->getMimeType() . ';base64,' . base64_encode(file_get_contents($request->file('photo')->getRealPath()));
}

        $vehicle->name            = $request->name;
        $vehicle->type            = $request->type;
        $vehicle->rate            = $request->rate;
        $vehicle->max_hectares    = $request->max_hectares;
        $vehicle->status          = $newStatus;
        $vehicle->estimated_fix_days = $newStatus === 'maintenance' ? $request->estimated_fix_days : null;
        $vehicle->driver_name     = $request->driver_name;
        $vehicle->driver_pay      = $request->driver_pay      ?? 0;
        $vehicle->helper1_name    = $request->helper1_name;
        $vehicle->helper2_name    = $request->helper2_name;
        $vehicle->helper3_name    = $request->helper3_name;
        $vehicle->helper_pay_each = $request->helper_pay_each ?? 0;
        $vehicle->diesel_cost     = $request->diesel_cost     ?? 0;
        $vehicle->save();

       if ($newStatus === 'maintenance' && $oldStatus !== 'maintenance') {
    $today        = now()->toDateString();
    $fixDays      = (int) ($request->estimated_fix_days ?? 1);

    $lastBlockedDay = now()->addDays($fixDays - 1)->toDateString();
    $availableFrom  = now()->addDays($fixDays)->toDateString();

    $affectedReservations = \App\Models\Reservation::where('vehicle_id', $vehicle->id)
        ->whereIn('status', ['pending', 'confirmed'])
        ->whereDate('reservation_date', '>=', $today)
        ->whereDate('reservation_date', '<=', $lastBlockedDay)
        ->orderBy('reservation_date')
        ->orderBy('created_at')
        ->get();

    foreach ($affectedReservations as $reservation) {
        $originalDate  = $reservation->reservation_date->toDateString();
        $candidateDate = \Carbon\Carbon::parse($availableFrom);

        while (true) {
            $dateStr        = $candidateDate->toDateString();
            $bookedHectares = \App\Models\Reservation::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('reservation_date', $dateStr)
                ->where('id', '!=', $reservation->id)
                ->sum('hectares');
            if ($vehicle->max_hectares - $bookedHectares >= $reservation->hectares) break;
            $candidateDate->addDay();
            if ($candidateDate->diffInDays($today) > 365) break;
        }

        $newDate = $candidateDate->toDateString();
        $reservation->reservation_date = $newDate;
        $reservation->save();

        AppNotification::create([
            'user_id' => $reservation->user_id,
            'type'    => 'booking_rescheduled',
            'title'   => 'Reservation Rescheduled',
            'message' => "Due to maintenance of {$vehicle->name}, your reservation on {$originalDate} has been moved to {$newDate}.",
            'data'    => [
                'reservation_id' => $reservation->id,
                'vehicle_name'   => $vehicle->name,
                'original_date'  => $originalDate,
                'new_date'       => $newDate,
            ],
        ]);
    }
}

        return redirect()->route('admin.vehicle')->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->image_path) Storage::disk('public')->delete($vehicle->image_path);
        $vehicle->delete();
        return redirect()->route('admin.vehicle')->with('deleted', 'Vehicle deleted.');
    }
}