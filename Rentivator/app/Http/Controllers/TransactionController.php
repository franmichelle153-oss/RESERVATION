<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AppNotification;

class TransactionController extends Controller
{
    public function saveExpenses(Request $request, $txId)
{
    $transaction = Transaction::with('expenses')->findOrFail($txId);

    // Delete old expenses
    $transaction->expenses()->delete();

    // Save new expenses
    $totalExpenses = 0;
    foreach ($request->expenses as $exp) {
        if (!empty($exp['label'])) {
            $transaction->expenses()->create([
                'label'  => $exp['label'],
                'amount' => $exp['amount'] ?? 0,
            ]);
            $totalExpenses += $exp['amount'] ?? 0;
        }
    }

    $deductions   = $request->deductions ?? 0;
    $netAmount    = max($transaction->gross_amount - $totalExpenses - $deductions, 0);

    $transaction->update([
        'total_expenses'  => $totalExpenses,
        'deductions'      => $deductions,
        'deduction_notes' => $request->deduction_notes,
        'net_amount'      => $netAmount,
        'audit_status'    => 'audited',  // ← automatic na nag-aaudit
    ]);

    // Return updated Total Revenue para ma-update ang stats card
    $totalRevenue = Transaction::where('audit_status', 'audited')->sum('net_amount');

    return response()->json([
        'success'       => true,
        'net_amount'    => $netAmount,
        'total_expenses'=> $totalExpenses,
        'total_revenue' => $totalRevenue,  // ← ibabalik sa frontend
    ]);
}
public function getExpenses($txId)
{
    $transaction = Transaction::with('expenses')->findOrFail($txId);

    return response()->json([
        'expenses'        => $transaction->expenses,
        'deductions'      => $transaction->deductions ?? 0,
        'deduction_notes' => $transaction->deduction_notes ?? '',
    ]);
}
}