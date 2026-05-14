<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\Request;

class AirCargoController extends Controller
{
    /**
     * Air Cargo Dashboard
     */
    public function dashboard()
    {
        $totalShipments = Shipment::where('shipment_type', 'air')->count();
        $inTransit = Shipment::where('shipment_type', 'air')->whereIn('current_status', ['In Transit', 'Picked Up'])->count();
        $pending = Shipment::where('shipment_type', 'air')->where('current_status', 'Pending')->count();
        $delivered = Shipment::where('shipment_type', 'air')->where('current_status', 'Delivered')->count();
        $onHold = Shipment::where('shipment_type', 'air')->whereIn('current_status', ['On Hold', 'Cancelled'])->count();
        $thisMonth = Shipment::where('shipment_type', 'air')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $revenueUgx = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'air')->where('currency', 'UGX');
        })->sum('amount');
        $revenueUsd = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'air')->where('currency', 'USD');
        })->sum('amount');

        $totalInvoiced = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'air');
        })->sum('total');
        $outstanding = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'air');
        })->whereIn('status', ['sent', 'partial', 'overdue'])->sum('total');

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[$date->format('M Y')] = Shipment::where('shipment_type', 'air')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $recentShipments = Shipment::where('shipment_type', 'air')
            ->with('client')
            ->latest()
            ->limit(5)
            ->get();

        $recentPayments = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'air');
        })->with(['invoice.shipment.client'])
            ->latest()
            ->limit(5)
            ->get();

        // Cumulative figures
        $totalRevenue = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'air');
        })->with(['invoice.shipment'])
            ->get()
            ->sum(function ($p) { return $p->amount; });

        $totalCost = Shipment::where('shipment_type', 'air')
            ->get()
            ->sum(function ($s) { return $s->cost_price ?? 0; });

        $totalProfit = $totalRevenue - $totalCost;

        return view('air-cargo.dashboard', compact(
            'totalShipments', 'inTransit', 'pending', 'delivered', 'onHold', 'thisMonth',
            'revenueUgx', 'revenueUsd', 'totalInvoiced', 'outstanding',
            'monthlyData', 'recentShipments', 'recentPayments',
            'totalRevenue', 'totalCost', 'totalProfit'
        ));
    }

    /**
     * Air Cargo Invoices
     */
    public function invoices()
    {
        $invoices = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'air');
        })->with(['shipment.client', 'payments'])->latest()->paginate(20);

        $totalInvoiced = $invoices->sum('total');
        $totalPaid = $invoices->sum(function ($i) { return $i->amount_paid; });
        $totalBalance = $invoices->sum(function ($i) { return $i->balance; });

        return view('air-cargo.invoices', compact('invoices', 'totalInvoiced', 'totalPaid', 'totalBalance'));
    }

    /**
     * Display a listing of air cargo shipments
     */
    public function index(Request $request)
    {
        $query = Shipment::where('shipment_type', 'air')
            ->with(['client', 'batch']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        $shipments = $query->latest()->paginate(20);

        return view('air-cargo.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new air cargo shipment
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('air-cargo.create', compact('clients'));
    }

    /**
     * Store a newly created air cargo shipment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'sender_name' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_id' => 'nullable|exists:clients,id',
        ]);

        $validated['shipment_type'] = 'air';
        $validated['delivery_time_unit'] = 'days';
        $validated['current_status'] = $validated['current_status'] ?? 'Pending';

        // Auto-generate tracking number if not provided
        if (empty($validated['tracking_number'])) {
            $validated['tracking_number'] = Shipment::generateTrackingNumber('EGL-AIR-');
        }

        // Set default currency to USD
        if (empty($validated['currency'])) {
            $validated['currency'] = 'USD';
        }

        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ceil(($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0));
        }

        $shipment = Shipment::create($validated);

        // Create packages if provided
        if ($request->has('packages') && is_array($request->packages)) {
            foreach ($request->packages as $index => $package) {
                if (! empty($package['description']) || ! empty($package['weight'])) {
                    $shipment->packages()->create([
                        'description' => $package['description'] ?? null,
                        'quantity' => $package['quantity'] ?? 1,
                        'length' => $package['length'] ?? null,
                        'width' => $package['width'] ?? null,
                        'height' => $package['height'] ?? null,
                        'weight' => $package['weight'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Auto-generate invoice for the shipment
        $invoice = \App\Models\Invoice::create([
            'shipment_id' => $shipment->id,
            'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $shipment->shipping_cost ?? 0,
            'tax' => $shipment->tax ?? 0,
            'discount' => 0,
            'total' => $shipment->total_amount ?? 0,
            'status' => 'sent',
            'created_by' => auth()->id(),
        ]);

        if ($shipment->shipping_cost > 0) {
            $invoice->items()->create([
                'description' => 'Air Cargo Service',
                'quantity' => 1,
                'rate' => $shipment->shipping_cost,
                'amount' => $shipment->shipping_cost,
                'order' => 0,
            ]);
        }

        if ($shipment->tax > 0) {
            $invoice->items()->create([
                'description' => 'Tax',
                'quantity' => 1,
                'rate' => $shipment->tax,
                'amount' => $shipment->tax,
                'order' => 1,
            ]);
        }

        return redirect()->route('admin.air-cargo.show', $shipment)
            ->with('success', 'Air cargo shipment created successfully. Tracking: '.$shipment->tracking_number);
    }

    /**
     * Display the specified air cargo shipment
     */
    public function show(Shipment $air_cargo)
    {
        if ($air_cargo->shipment_type !== 'air') {
            abort(404);
        }

        $air_cargo->load(['client', 'receiver', 'batch', 'statusUpdates', 'invoices.items', 'invoices.payments']);

        return view('air-cargo.show', ['shipment' => $air_cargo]);
    }

    /**
     * Show the form for editing the specified air cargo shipment
     */
    public function edit(Shipment $air_cargo)
    {
        if ($air_cargo->shipment_type !== 'air') {
            abort(404);
        }

        $clients = Client::orderBy('name')->get();

        return view('air-cargo.edit', ['shipment' => $air_cargo, 'clients' => $clients]);
    }

    /**
     * Update the specified air cargo shipment
     */
    public function update(Request $request, Shipment $air_cargo)
    {
        if ($air_cargo->shipment_type !== 'air') {
            abort(404);
        }

        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number,'.$air_cargo->id,
            'client_id' => 'required|exists:clients,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'delivery_time_min' => 'required|integer|min:1',
            'delivery_time_max' => 'required|integer|min:1|gte:delivery_time_min',
            'current_status' => 'nullable|string',
            'description' => 'nullable|string',
            'num_packages' => 'nullable|integer|min:1',
            'package_type' => 'nullable|in:box,pallet,envelope,custom',
            'fragile' => 'nullable|boolean',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'sender_name' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_id' => 'nullable|exists:clients,id',
        ]);

        $validated['delivery_time_unit'] = 'days';

        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ceil(($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0));
        }

        if (isset($validated['current_status']) && strtolower(trim($validated['current_status'])) === 'picked up') {
            $air_cargo->load('invoices.items');
            $invoice = $air_cargo->invoices->first();

            $hasStorageFee = false;
            if ($invoice && $invoice->items) {
                foreach ($invoice->items as $item) {
                    if (strtolower(trim($item->description)) === 'storage fee') {
                        $hasStorageFee = true;
                        break;
                    }
                }
            }

            if (! $hasStorageFee) {
                return redirect()->back()->withInput()
                    ->with('error', 'Cannot change status to "Picked Up". A "Storage Fee" must be added to the invoice. If there is no fee, please add it with an amount of 0.');
            }

            if ($invoice && $invoice->balance > 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Cannot change status to "Picked Up". The invoice has an outstanding balance. Please ensure the invoice is fully paid first.');
            }
        }

        $air_cargo->update($validated);

        // Update packages if provided
        if ($request->has('packages') && is_array($request->packages)) {
            $packageIds = collect($request->packages)->pluck('id')->filter()->toArray();
            $air_cargo->packages()->whereNotIn('id', $packageIds)->delete();

            foreach ($request->packages as $index => $package) {
                if (! empty($package['id'])) {
                    $air_cargo->packages()->where('id', $package['id'])->update([
                        'description' => $package['description'] ?? null,
                        'quantity' => $package['quantity'] ?? 1,
                        'length' => $package['length'] ?? null,
                        'width' => $package['width'] ?? null,
                        'height' => $package['height'] ?? null,
                        'weight' => $package['weight'] ?? null,
                        'sort_order' => $index,
                    ]);
                } elseif (! empty($package['description']) || ! empty($package['weight'])) {
                    $air_cargo->packages()->create([
                        'description' => $package['description'] ?? null,
                        'quantity' => $package['quantity'] ?? 1,
                        'length' => $package['length'] ?? null,
                        'width' => $package['width'] ?? null,
                        'height' => $package['height'] ?? null,
                        'weight' => $package['weight'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.air-cargo.show', $air_cargo)
            ->with('success', 'Air cargo shipment updated successfully.');
    }

    /**
     * Remove the specified air cargo shipment
     */
    public function destroy(Shipment $air_cargo)
    {
        if ($air_cargo->shipment_type !== 'air') {
            abort(404);
        }

        $air_cargo->delete();

        return redirect()->route('admin.air-cargo.index')
            ->with('success', 'Air cargo shipment deleted successfully.');
    }
}
