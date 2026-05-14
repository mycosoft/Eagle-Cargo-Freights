<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Http\Request;

class SeaCargoController extends Controller
{
    /**
     * Sea Cargo Dashboard
     */
    public function dashboard()
    {
        $totalShipments = Shipment::where('shipment_type', 'sea')->count();
        $inTransit = Shipment::where('shipment_type', 'sea')->whereIn('current_status', ['In Transit', 'Picked Up'])->count();
        $pending = Shipment::where('shipment_type', 'sea')->where('current_status', 'Pending')->count();
        $delivered = Shipment::where('shipment_type', 'sea')->where('current_status', 'Delivered')->count();
        $onHold = Shipment::where('shipment_type', 'sea')->whereIn('current_status', ['On Hold', 'Cancelled'])->count();
        $thisMonth = Shipment::where('shipment_type', 'sea')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $revenueUgx = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'sea')->where('currency', 'UGX');
        })->sum('amount');
        $revenueUsd = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'sea')->where('currency', 'USD');
        })->sum('amount');

        $totalInvoiced = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'sea');
        })->sum('total');
        $outstanding = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'sea');
        })->whereIn('status', ['sent', 'partial', 'overdue'])->sum('total');

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[$date->format('M Y')] = Shipment::where('shipment_type', 'sea')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $recentShipments = Shipment::where('shipment_type', 'sea')
            ->with('client')
            ->latest()
            ->limit(5)
            ->get();

        $recentPayments = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'sea');
        })->with(['invoice.shipment.client'])
            ->latest()
            ->limit(5)
            ->get();

        $totalRevenue = Payment::whereHas('invoice.shipment', function ($q) {
            $q->where('shipment_type', 'sea');
        })->sum('amount');

        $totalCost = Shipment::where('shipment_type', 'sea')
            ->get()
            ->sum(function ($s) { return $s->cost_price ?? 0; });

        $totalProfit = $totalRevenue - $totalCost;

        return view('sea-cargo.dashboard', compact(
            'totalShipments', 'inTransit', 'pending', 'delivered', 'onHold', 'thisMonth',
            'revenueUgx', 'revenueUsd', 'totalInvoiced', 'outstanding',
            'monthlyData', 'recentShipments', 'recentPayments',
            'totalRevenue', 'totalCost', 'totalProfit'
        ));
    }

    /**
     * Sea Cargo Invoices
     */
    public function invoices()
    {
        $invoices = Invoice::whereHas('shipment', function ($q) {
            $q->where('shipment_type', 'sea');
        })->with(['shipment.client', 'payments'])->latest()->paginate(20);

        $totalInvoiced = $invoices->sum('total');
        $totalPaid = $invoices->sum(function ($i) { return $i->amount_paid; });
        $totalBalance = $invoices->sum(function ($i) { return $i->balance; });

        return view('sea-cargo.invoices', compact('invoices', 'totalInvoiced', 'totalPaid', 'totalBalance'));
    }

    /**
     * Display a listing of sea cargo shipments
     */
    public function index(Request $request)
    {
        $query = Shipment::where('shipment_type', 'sea')
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

        return view('sea-cargo.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new sea cargo shipment
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('sea-cargo.create', compact('clients'));
    }

    /**
     * Store a newly created sea cargo shipment
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
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'cbm' => 'nullable|numeric|min:0',
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

        $validated['shipment_type'] = 'sea';
        $validated['delivery_time_unit'] = 'months';
        $validated['current_status'] = $validated['current_status'] ?? 'Pending';

        // Auto-generate tracking number if not provided
        if (empty($validated['tracking_number'])) {
            $validated['tracking_number'] = Shipment::generateTrackingNumber('EGL-SEA-');
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
                'description' => 'Sea Cargo Service',
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

        return redirect()->route('admin.sea-cargo.show', $shipment)
            ->with('success', 'Sea cargo shipment created successfully. Tracking: '.$shipment->tracking_number);
    }

    /**
     * Display the specified sea cargo shipment
     */
    public function show(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $sea_cargo->load(['client', 'receiver', 'batch', 'statusUpdates', 'invoices.items', 'invoices.payments']);

        return view('sea-cargo.show', ['shipment' => $sea_cargo]);
    }

    /**
     * Show the form for editing the specified sea cargo shipment
     */
    public function edit(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $clients = Client::orderBy('name')->get();

        return view('sea-cargo.edit', ['shipment' => $sea_cargo, 'clients' => $clients]);
    }

    /**
     * Update the specified sea cargo shipment
     */
    public function update(Request $request, Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number,'.$sea_cargo->id,
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
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'cbm' => 'nullable|numeric|min:0',
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

        $validated['delivery_time_unit'] = 'months';

        // Calculate total
        if (isset($validated['shipping_cost'])) {
            $validated['total_amount'] = ceil(($validated['shipping_cost'] ?? 0) + ($validated['tax'] ?? 0));
        }

        if (isset($validated['current_status']) && strtolower(trim($validated['current_status'])) === 'picked up') {
            $sea_cargo->load('invoices.items');
            $invoice = $sea_cargo->invoices->first();

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

        $sea_cargo->update($validated);

        // Update packages if provided
        if ($request->has('packages') && is_array($request->packages)) {
            $packageIds = collect($request->packages)->pluck('id')->filter()->toArray();
            $sea_cargo->packages()->whereNotIn('id', $packageIds)->delete();

            foreach ($request->packages as $index => $package) {
                if (! empty($package['id'])) {
                    $sea_cargo->packages()->where('id', $package['id'])->update([
                        'description' => $package['description'] ?? null,
                        'quantity' => $package['quantity'] ?? 1,
                        'length' => $package['length'] ?? null,
                        'width' => $package['width'] ?? null,
                        'height' => $package['height'] ?? null,
                        'weight' => $package['weight'] ?? null,
                        'sort_order' => $index,
                    ]);
                } elseif (! empty($package['description']) || ! empty($package['weight'])) {
                    $sea_cargo->packages()->create([
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

        return redirect()->route('admin.sea-cargo.show', $sea_cargo)
            ->with('success', 'Sea cargo shipment updated successfully.');
    }

    /**
     * Remove the specified sea cargo shipment
     */
    public function destroy(Shipment $sea_cargo)
    {
        if ($sea_cargo->shipment_type !== 'sea') {
            abort(404);
        }

        $sea_cargo->delete();

        return redirect()->route('admin.sea-cargo.index')
            ->with('success', 'Sea cargo shipment deleted successfully.');
    }
}
