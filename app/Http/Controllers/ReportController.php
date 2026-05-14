<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentBatch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function revenue(Request $request)
    {
        $query = Payment::with(['invoice.shipment.client']);

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest('payment_date')->paginate(50);
        $totalRevenue = $query->sum('amount');

        return view('reports.revenue', compact('payments', 'totalRevenue'));
    }

    public function outstanding(Request $request)
    {
        $query = Invoice::with(['shipment.client', 'payments'])
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled');

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('issue_date')->paginate(50);
        $totalOutstanding = $invoices->sum('balance');

        return view('reports.outstanding', compact('invoices', 'totalOutstanding'));
    }

    public function payments(Request $request)
    {
        return $this->revenue($request);
    }

    public function shipments(Request $request)
    {
        $query = Shipment::with(['client', 'batch']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }
        if ($request->filled('shipment_type')) {
            $query->where('shipment_type', $request->shipment_type);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $shipments = $query->latest()->paginate(50);

        $stats = [
            'total' => Shipment::count(),
            'pending' => Shipment::where('current_status', 'Pending')->count(),
            'in_transit' => Shipment::where('current_status', 'In Transit')->count(),
            'delivered' => Shipment::where('current_status', 'Delivered')->count(),
        ];

        $clients = \App\Models\Client::orderBy('name')->get();

        return view('reports.shipments', compact('shipments', 'stats', 'clients'));
    }

    public function clients(Request $request)
    {
        $query = \App\Models\Client::withCount('shipments');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        $clients = $query->latest()->paginate(50);

        return view('reports.clients', compact('clients'));
    }

    public function batchRevenue(Request $request)
    {
        $query = ShipmentBatch::with(['shipments', 'creator']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }
        if ($request->filled('cargo_type')) {
            $query->where('cargo_type', $request->cargo_type);
        }

        $batches = $query->latest()->paginate(50);

        $totalRevenue = 0;
        $totalInvoiced = 0;
        $totalOutstanding = 0;

        foreach ($batches->items() as $batch) {
            $totalRevenue += $batch->revenue;
            $totalInvoiced += $batch->invoiced_amount;
            $totalOutstanding += $batch->outstanding_amount;
        }

        return view('reports.batch-revenue', compact('batches', 'totalRevenue', 'totalInvoiced', 'totalOutstanding'));
    }

    public function profitLoss(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');

        $query = Shipment::query();
        if ($type === 'air') $query->where('shipment_type', 'air');
        elseif ($type === 'sea') $query->where('shipment_type', 'sea');

        $shipments = $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59'])->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;
        $data = [];

        foreach ($shipments as $s) {
            $revenue = $s->revenue;
            $cost = $s->cost_price ?? 0;
            $profit = $revenue - $cost;
            $totalRevenue += $revenue;
            $totalCost += $cost;
            $totalProfit += $profit;

            $data[] = [
                'tracking' => $s->tracking_number,
                'client' => $s->client->name ?? 'N/A',
                'type' => $s->shipment_type,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'date' => $s->created_at->format('d M Y'),
            ];
        }

        $airTotal = $shipments->where('shipment_type', 'air')->count();
        $seaTotal = $shipments->where('shipment_type', 'sea')->count();

        return view('reports.profit-loss', compact(
            'data', 'totalRevenue', 'totalCost', 'totalProfit',
            'fromDate', 'toDate', 'type', 'airTotal', 'seaTotal'
        ));
    }

    public function expenses(Request $request)
    {
        $query = \App\Models\Expense::with(['category', 'recorder']);

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $expenses = $query->latest('expense_date')->paginate(50);

        $categoryTotals = \App\Models\Expense::query()
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('expense_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('expense_date', '<=', $request->date_to))
            ->where('status', '!=', 'rejected')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->pluck('total', 'category.name');

        $totalExpenses = $categoryTotals->sum();

        return view('reports.expenses', compact('expenses', 'categoryTotals', 'totalExpenses'));
    }

    public function analytics()
    {
        $data = [
            'totalShipments' => Shipment::count(),
            'totalClients' => \App\Models\Client::count(),
            'totalRevenue' => Payment::sum('amount'),
            'monthlyRevenue' => Payment::whereYear('payment_date', now()->year)
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
            'shipmentsByStatus' => Shipment::select('current_status', DB::raw('count(*) as total'))
                ->groupBy('current_status')
                ->get(),
            'shipmentsByType' => Shipment::select('shipment_type', DB::raw('count(*) as total'))
                ->groupBy('shipment_type')
                ->get(),
        ];

        return view('reports.analytics', $data);
    }

    public function exportShipmentsPdf(Request $request)
    {
        $shipments = Shipment::with(['client', 'batch'])->latest()->get();
        $pdf = Pdf::loadView('reports.pdf.shipments', compact('shipments'));
        return $pdf->download('shipments-report-'.date('Y-m-d').'.pdf');
    }

    public function exportClientsPdf(Request $request)
    {
        $clients = \App\Models\Client::withCount('shipments')->latest()->get();
        $pdf = Pdf::loadView('reports.pdf.clients', compact('clients'));
        return $pdf->download('clients-report-'.date('Y-m-d').'.pdf');
    }
}
