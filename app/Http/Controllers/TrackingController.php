<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        $logo = \App\Models\Setting::get('site_logo');

        return view('tracking.index', compact('logo'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|exists:shipments,tracking_number',
        ], [
            'tracking_number.exists' => 'Tracking number not found. Please check and try again.',
        ]);

        $shipment = Shipment::with(['statusUpdates' => function ($query) {
            $query->latest();
        }, 'client'])->where('tracking_number', $request->tracking_number)->firstOrFail();

        $logo = \App\Models\Setting::get('site_logo');

        return view('tracking.result', compact('shipment', 'logo'));
    }
}
