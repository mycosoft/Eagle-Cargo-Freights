<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$invoice->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric|min:0.01',
        ]);

        $validated['invoice_id'] = $invoice->id;

        $payment = Payment::create($validated);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully. Receipt Number: '.$payment->receipt_number);
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.shipment.client', 'recorder']);

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

        return view('payments.show', compact('payment', 'companySettings'));
    }

    /**
     * Generate PDF receipt for the specified payment.
     */
    public function generateReceipt(Payment $payment)
    {
        $payment->load(['invoice.shipment.client', 'recorder']);

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

        $pdf = Pdf::loadView('payments.receipt-pdf', [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'shipment' => $payment->invoice->shipment,
            'companySettings' => $companySettings,
        ]);

        return $pdf->download('receipt-'.$payment->receipt_number.'.pdf');
    }

    /**
     * Send receipt to client via WhatsApp/Email.
     */
    public function sendReceipt(Request $request, Payment $payment)
    {
        $payment->load(['invoice.shipment.client']);

        $client = $payment->invoice->shipment->client;

        if (! $client) {
            return redirect()->back()->with('error', 'Client not found for this payment.');
        }

        // Check notification preferences
        $notifyEmail = Setting::get('notify_status_change_email', 1);
        $notifyWhatsapp = Setting::get('notify_status_change_whatsapp', 1);

        $sentVia = [];

        // Send via Email
        if ($notifyEmail && $client->email) {
            try {
                $client->notify(new \App\Notifications\ReceiptSent($payment));
                $sentVia[] = 'email';
            } catch (\Exception $e) {
                \Log::error('Failed to send receipt email: '.$e->getMessage());
            }
        }

        // Send via WhatsApp
        if ($notifyWhatsapp && $client->phone) {
            try {
                $client->notify(new \App\Notifications\ReceiptSent($payment));
                $sentVia[] = 'whatsapp';
            } catch (\Exception $e) {
                \Log::error('Failed to send receipt WhatsApp: '.$e->getMessage());
            }
        }

        if (empty($sentVia)) {
            return redirect()->back()->with('error', 'No notification channels available or enabled.');
        }

        // Update payment with delivery tracking
        $payment->update([
            'receipt_sent_at' => now(),
            'receipt_sent_via' => implode(',', $sentVia),
        ]);

        $message = 'Receipt sent successfully via '.implode(' and ', $sentVia).'.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Payment deleted successfully.');
    }
}
