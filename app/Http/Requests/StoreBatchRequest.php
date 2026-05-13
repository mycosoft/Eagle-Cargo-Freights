<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255', // Changed to nullable - will auto-generate if empty
            'description' => 'nullable|string',
            'cargo_type' => 'required|in:air,sea',
            'current_status' => 'required|in:Pending,Picked Up,In Transit,Arrived at Facility,Out for Delivery,Delivered,On Hold,Cancelled',
            'shipments' => 'nullable|array',
            'shipments.*.client_id' => 'required|exists:clients,id',
            'shipments.*.shipment_type' => 'required|in:air,sea',
            'shipments.*.origin' => 'required|string|max:255',
            'shipments.*.destination' => 'required|string|max:255',
            'shipments.*.delivery_time_min' => 'required|integer|min:1',
            'shipments.*.delivery_time_max' => 'required|integer|min:1|gte:shipments.*.delivery_time_min',
            'shipments.*.weight' => 'nullable|numeric|min:0',
            'shipments.*.num_packages' => 'nullable|integer|min:1',
            'shipments.*.package_type' => 'nullable|in:box,pallet,envelope,custom',
            'shipments.*.length' => 'nullable|numeric|min:0',
            'shipments.*.width' => 'nullable|numeric|min:0',
            'shipments.*.height' => 'nullable|numeric|min:0',
            'shipments.*.cbm' => 'nullable|numeric|min:0',
            'shipments.*.fragile' => 'nullable|boolean',
            'shipments.*.shipping_cost' => 'nullable|numeric|min:0',
            'shipments.*.tax' => 'nullable|numeric|min:0',
            'shipments.*.payment_status' => 'nullable|in:pending,paid,refunded',
            'shipments.*.description' => 'nullable|string',
        ];
    }
}
