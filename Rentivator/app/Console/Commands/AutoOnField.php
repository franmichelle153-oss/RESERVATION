<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Vehicle;
use Carbon\Carbon;

class AutoOnField extends Command
{
    protected $signature   = 'reservations:auto-onfield';
    protected $description = 'Automatically set vehicles to on-field status on their confirmed reservation date';

    public function handle(): void
    {
        $today = Carbon::today()->toDateString();

        // Find all confirmed reservations whose date is today
        $reservations = Reservation::where('status', 'confirmed')
            ->where('reservation_date', $today)
            ->with('vehicle')
            ->get();

        foreach ($reservations as $reservation) {
            if ($reservation->vehicle && $reservation->vehicle->status !== 'maintenance') {
                $reservation->vehicle->update(['status' => 'onfield']);
                $this->info("Vehicle [{$reservation->vehicle->name}] set to ON FIELD for reservation #{$reservation->id}");
            }
        }

        // Also mark reservations from past dates as completed if vehicle was onfield
        $past = Reservation::where('status', 'confirmed')
            ->where('reservation_date', '<', $today)
            ->get();

        foreach ($past as $reservation) {
            $reservation->update(['status' => 'completed']);

            // Set vehicle back to available if no other confirmed reservation today
            if ($reservation->vehicle) {
                $hasOtherToday = Reservation::where('vehicle_id', $reservation->vehicle_id)
                    ->where('status', 'confirmed')
                    ->where('reservation_date', $today)
                    ->exists();

                if (!$hasOtherToday && $reservation->vehicle->status === 'onfield') {
                    $reservation->vehicle->update(['status' => 'available']);
                }
            }
        }

        $this->info('Auto on-field check completed.');
    }
}