<?php

namespace App\Http\Controllers;

use App\Jobs\SendBulkNotificationsJob;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        $clients = \App\Models\Client::all();

        return view('broadcast.index', compact('clients'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'channel' => 'required|in:email,sms,whatsapp',
            'recipients' => 'required|in:all,selected',
            'client_ids' => 'required_if:recipients,selected|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $clientIds = $validated['recipients'] === 'all' ? null : $validated['client_ids'];

        // Dispatch job to handle bulk sending
        SendBulkNotificationsJob::dispatch(
            $validated['subject'],
            $validated['message'],
            $validated['channel'],
            $clientIds
        );

        return redirect('admin/broadcast')
            ->with('success', 'Bulk notification process started. Messages will be sent in the background.');
    }
}
