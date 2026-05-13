<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of all payments/transactions.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.shipment.client', 'recorder'])
            ->latest('payment_date');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('invoice.shipment.client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice.shipment', function ($q3) use ($search) {
                        $q3->where('tracking_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Date from
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        // Date to
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(25);

        // Summary stats
        $totalCollected = Payment::sum('amount');
        $totalCount = Payment::count();
        $thisMonthTotal = Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
        $todayTotal = Payment::whereDate('payment_date', today())->sum('amount');

        return view('transactions.index', compact(
            'payments', 'totalCollected', 'totalCount', 'thisMonthTotal', 'todayTotal'
        ));
    }

    /**
     * Show the form to record a new payment.
     */
    public function create()
    {
        // Only show invoices that still have a balance
        $invoices = Invoice::with('shipment.client')
            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
            ->latest()
            ->get()
            ->filter(fn ($inv) => $inv->balance > 0);

        return view('transactions.create', compact('invoices'));
    }

    /**
     * Store a new payment (independent of the invoice page).
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Extra guard: amount must not exceed balance
        if ($request->amount > $invoice->balance) {
            return back()->withInput()
                ->withErrors(['amount' => 'Amount exceeds the invoice balance of '.number_format($invoice->balance, 2)]);
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.transactions.show', $payment)
            ->with('success', 'Payment recorded successfully. Receipt: '.$payment->receipt_number);
    }

    /**
     * Display a single transaction.
     */
    public function show(Payment $transaction)
    {
        $transaction->load(['invoice.shipment.client', 'recorder']);
        $payment = $transaction;

        return view('transactions.show', compact('payment'));
    }
}
