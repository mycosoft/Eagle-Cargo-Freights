<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BatchExpense;
use App\Models\ShipmentBatch;
use Illuminate\Http\Request;

class BatchExpenseController extends Controller
{
    public function store(Request $request, ShipmentBatch $batch)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:UGX,USD',
            'description' => 'nullable|string|max:1000',
        ]);

        $batch->expenses()->create([
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'description' => $validated['description'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch expense added successfully.');
    }

    public function destroy(ShipmentBatch $batch, BatchExpense $expense)
    {
        if ($expense->batch_id !== $batch->id) {
            abort(404);
        }

        $expense->delete();

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', 'Batch expense removed.');
    }
}
