<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionExpense extends Model
{
    protected $fillable = ['transaction_id', 'label', 'amount'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}