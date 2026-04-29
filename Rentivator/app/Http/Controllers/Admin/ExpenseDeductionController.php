<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseDeduction;
use Illuminate\Http\Request;

class ExpenseDeductionController extends Controller
{
    public function store(Request $request)
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

    public function destroy($id)
    {
        $ded = ExpenseDeduction::findOrFail($id);
        $ded->delete();
        return response()->json(['success' => true]);
    }
}