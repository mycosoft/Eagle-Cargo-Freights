<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentBatch;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin sees the full dashboard
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        // Staff with specific cargo permissions
        $canViewAir = $user->can('view air cargo');
        $canViewSea = $user->can('view sea cargo');

        if ($canViewAir && !$canViewSea) {
            return redirect()->route('admin.air-cargo.dashboard');
        }

        if ($canViewSea && !$canViewAir) {
            return redirect()->route('admin.sea-cargo.dashboard');
        }

        // If they have both or neither, show the full dashboard
        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $airCount = Shipment::where('shipment_type', 'air')->count();
        $seaCount = Shipment::where('shipment_type', 'sea')->count();
        $airInTransit = Shipment::where('shipment_type', 'air')->whereIn('current_status', ['In Transit', 'Picked Up'])->count();
        $seaInTransit = Shipment::where('shipment_type', 'sea')->whereIn('current_status', ['In Transit', 'Picked Up'])->count();
        $airDelivered = Shipment::where('shipment_type', 'air')->where('current_status', 'Delivered')->count();
        $seaDelivered = Shipment::where('shipment_type', 'sea')->where('current_status', 'Delivered')->count();
        $clientCount = \App\Models\Client::count();
        $totalShipments = Shipment::count();
        $batchCount = ShipmentBatch::count();
        $userCount = User::visible()->count();

        $totalRevenue = Payment::sum('amount');
        $airRevenue = Payment::whereHas('invoice.shipment', fn($q) => $q->where('shipment_type', 'air'))->sum('amount');
        $seaRevenue = Payment::whereHas('invoice.shipment', fn($q) => $q->where('shipment_type', 'sea'))->sum('amount');

        $monthlyRevenue = Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $outstanding = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->sum(\DB::raw('total - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id)'));
        $paidInvoices = Invoice::where('status', 'paid')->count();

        $recentAir = Shipment::where('shipment_type', 'air')->with('client')->latest()->limit(5)->get();
        $recentSea = Shipment::where('shipment_type', 'sea')->with('client')->latest()->limit(5)->get();

        $airMonthly = [];
        $seaMonthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $airMonthly[] = Shipment::where('shipment_type', 'air')
                ->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
            $seaMonthly[] = Shipment::where('shipment_type', 'sea')
                ->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
        }

        return view('dashboard', compact(
            'airCount', 'seaCount', 'airInTransit', 'seaInTransit',
            'airDelivered', 'seaDelivered', 'clientCount', 'totalShipments',
            'batchCount', 'userCount', 'totalRevenue', 'airRevenue', 'seaRevenue',
            'monthlyRevenue', 'outstanding', 'paidInvoices',
            'recentAir', 'recentSea', 'airMonthly', 'seaMonthly'
        ));
    }
}
