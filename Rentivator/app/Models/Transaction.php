<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'vehicle_id',
        'gross_amount',
        'total_expenses',
        'deductions',
        'deduction_notes',
        'net_amount',
        'audit_status',
        'transaction_date',
    ];

    protected $casts = [
        'gross_amount'     => 'decimal:2',
        'total_expenses'   => 'decimal:2',
        'deductions'       => 'decimal:2',
        'net_amount'       => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function expenses()
    {
        return $this->hasMany(TransactionExpense::class);
    }
}