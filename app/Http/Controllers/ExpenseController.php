<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'recorder', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->has('currency') && $request->currency != '') {
            $query->where('currency', $request->currency);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Search by expense number or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest('expense_date')->paginate(20);
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $totalUGX = (clone $query)->where('currency', 'UGX')->sum('amount');
        $totalUSD = (clone $query)->where('currency', 'USD')->sum('amount');
        $pendingCount = Expense::where('status', 'pending')->count();

        return view('expenses.index', compact('expenses', 'categories', 'totalUGX', 'totalUSD', 'pendingCount'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:UGX,USD',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,check',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')
                ->store('expense-receipts', 'public');
            $validated['receipt_image'] = $path;
        }

        $expense = Expense::create($validated);

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense created successfully. Expense Number: '.$expense->expense_number);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'recorder', 'approver']);

        $companySettings = [
            'name' => 'Eagle Cargo Freights',
            'address' => 'P.O.Box 75529, Kampala',
            'phone' => '+256 200 991 118',
            'whatsapp' => '0777151635, +256 701 579417',
            'china' => '+86 130 7021 8275',
            'email' => 'eaglecargofreights@gmail.com',
            'website' => 'www.eaglecargofreights.com',
            'logo' => 'images/logo.jpeg',
        ];

        return view('expenses.show', compact('expense', 'companySettings'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        if (! $expense->canBeEdited()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Cannot edit '.$expense->status.' expenses.');
        }

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        if (! $expense->canBeEdited()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Cannot edit '.$expense->status.' expenses.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:UGX,USD',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,check',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            // Delete old image if exists
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $path = $request->file('receipt_image')
                ->store('expense-receipts', 'public');
            $validated['receipt_image'] = $path;
        }

        $expense->update($validated);

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        if (! $expense->canBeDeleted()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Cannot delete '.$expense->status.' expenses.');
        }

        // Delete receipt image if exists
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Approve the specified expense.
     */
    public function approve(Request $request, Expense $expense)
    {
        if (! $expense->canBeApproved()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'This expense cannot be approved.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($expense->recorded_by && $expense->recorded_by !== auth()->id()) {
            Notification::send(
                userId: $expense->recorded_by,
                title: 'Expense Approved',
                message: 'Expense #' . $expense->expense_number . ' has been approved.',
                link: route('admin.expenses.show', $expense),
                icon: 'fas fa-check-circle',
                color: 'success',
            );
        }

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense approved successfully.');
    }

    /**
     * Reject the specified expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if (! $expense->canBeApproved()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'This expense cannot be rejected.');
        }

        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        if ($expense->recorded_by && $expense->recorded_by !== auth()->id()) {
            Notification::send(
                userId: $expense->recorded_by,
                title: 'Expense Rejected',
                message: 'Expense #' . $expense->expense_number . ' has been rejected. Reason: ' . \Illuminate\Support\Str::limit($validated['rejection_reason'], 80),
                link: route('admin.expenses.show', $expense),
                icon: 'fas fa-times-circle',
                color: 'danger',
            );
        }

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense rejected successfully.');
    }

    /**
     * Mark the specified expense as paid.
     */
    public function markAsPaid(Expense $expense)
    {
        if (! $expense->isApproved()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Only approved expenses can be marked as paid.');
        }

        $expense->update(['status' => 'paid']);

        if ($expense->recorded_by && $expense->recorded_by !== auth()->id()) {
            Notification::send(
                userId: $expense->recorded_by,
                title: 'Expense Paid',
                message: 'Expense #' . $expense->expense_number . ' has been marked as paid.',
                link: route('admin.expenses.show', $expense),
                icon: 'fas fa-money-bill-wave',
                color: 'success',
            );
        }

        return redirect()->route('admin.expenses.show', $expense)
            ->with('success', 'Expense marked as paid successfully.');
    }

    /**
     * Generate PDF receipt for the expense.
     */
    public function generateReceipt(Expense $expense)
    {
        $expense->load(['category', 'recorder', 'approver']);

        $companySettings = [
            'name' => 'Eagle Cargo Freights',
            'address' => 'P.O.Box 75529, Kampala',
            'phone' => '+256 200 991 118',
            'whatsapp' => '0777151635, +256 701 579417',
            'china' => '+86 130 7021 8275',
            'email' => 'eaglecargofreights@gmail.com',
            'website' => 'www.eaglecargofreights.com',
            'logo' => 'images/logo.jpeg',
        ];

        $pdf = Pdf::loadView('expenses.receipt-pdf', [
            'expense' => $expense,
            'companySettings' => $companySettings,
        ]);

        return $pdf->download('expense-'.$expense->expense_number.'.pdf');
    }
}
