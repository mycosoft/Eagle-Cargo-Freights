<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['shipment.client', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Search by invoice number or tracking number
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('shipment', function ($sq) use ($search) {
                        $sq->where('tracking_number', 'like', "%{$search}%");
                    }
                    );
            });
        }

        $invoices = $query->latest('issue_date')->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['shipment.client', 'shipment.receiver', 'payments.recorder', 'items']);

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

        return view('invoices.show', compact('invoice', 'companySettings'));
    }

    /**
     * Generate PDF for the specified invoice.
     */
    public function generatePDF(Invoice $invoice)
    {
        $invoice->load(['shipment.client', 'shipment.receiver', 'shipment.packages', 'shipment.batch', 'items', 'payments.recorder']);

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

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'shipment' => $invoice->shipment,
            'companySettings' => $companySettings,
        ]);

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    /**
     * Send invoice to client via WhatsApp/Email.
     */
    public function sendInvoice(Request $request, Invoice $invoice)
    {
        $invoice->load(['shipment.client']);

        $client = $invoice->shipment->client;

        if (! $client) {
            return redirect()->back()->with('error', 'Client not found for this invoice.');
        }

        // Check notification preferences
        $notifyEmail = \App\Models\Setting::get('notify_status_change_email', 1);
        $notifyWhatsapp = \App\Models\Setting::get('notify_status_change_whatsapp', 1);

        $sentVia = [];

        // Send via Email
        if ($notifyEmail && $client->email) {
            try {
                $client->notify(new \App\Notifications\InvoiceSent($invoice));
                $sentVia[] = 'email';
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice email: '.$e->getMessage());
            }
        }

        // Send via WhatsApp
        if ($notifyWhatsapp && $client->phone) {
            try {
                $client->notify(new \App\Notifications\InvoiceSent($invoice));
                $sentVia[] = 'whatsapp';
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice WhatsApp: '.$e->getMessage());
            }
        }

        if (empty($sentVia)) {
            return redirect()->back()->with('error', 'No notification channels available or enabled.');
        }

        // Update invoice with delivery tracking
        $invoice->update([
            'sent_at' => now(),
            'sent_via' => implode(',', $sentVia),
        ]);

        $message = 'Invoice sent successfully via '.implode(' and ', $sentVia).'.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Add a Storage Fee line item to an invoice (charged at pickup).
     */
    public function addStorageFee(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'storage_amount' => 'required|numeric|min:0.01',
            'storage_days' => 'nullable|integer|min:1',
            'storage_notes' => 'nullable|string|max:255',
        ]);

        $days = $validated['storage_days'] ?? 1;
        $rate = round($validated['storage_amount'] / $days, 2);
        $description = 'Storage Bill'.($validated['storage_notes'] ? ' – '.$validated['storage_notes'] : '');

        // Create the line item
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $description,
            'quantity' => $days,
            'rate' => $rate,
            'amount' => $validated['storage_amount'],
            'order' => $invoice->items()->max('order') + 1,
        ]);

        // Recalculate invoice subtotal and total
        $newSubtotal = $invoice->items()->sum('amount');
        $newTotal = $newSubtotal + $invoice->tax - $invoice->discount;

        $invoice->update([
            'subtotal' => $newSubtotal,
            'total' => $newTotal,
        ]);

        // Recalculate status (in case it was marked paid before storage was added)
        $invoice->updateStatus();

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Storage Fee of '.\App\Models\Setting::getCurrencySymbol($invoice->shipment->currency ?? null).' '.number_format($validated['storage_amount'], 2).' added to invoice '.$invoice->invoice_number.'.');
    }
}
