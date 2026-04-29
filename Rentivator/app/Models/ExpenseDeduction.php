<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseDeduction extends Model
{
    protected $table = 'expense_deductions'; // explicit para sigurado

    protected $fillable = [
        'amount',
        'reason',
        'deduction_date',
        // dagdag pa kung may iba pang columns sa table mo
    ];

    protected $casts = [
        'deduction_date' => 'date',
    ];

    // Kung may relationship sa ibang table, halimbawa:
    // public function user() {
    //     return $this->belongsTo(User::class);
    // }
}