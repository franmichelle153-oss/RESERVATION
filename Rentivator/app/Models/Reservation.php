<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'operator_name',
        'contact_number',
        'location',
        'hectares',
        'reservation_date',
        'status',
        'hidden_from_admin',
        'cancellation_reason', // ← BAGONG DAGDAG
    ];

    protected $casts = [
        'reservation_date'  => 'date',
        'hidden_from_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function feedback()
    {
        return $this->hasOne(\App\Models\Feedback::class);
    }
}