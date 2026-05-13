<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermissionTo('manage shipments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'tracking_number' => ['nullable', 'string', 'max:255', 'unique:shipments,tracking_number,'.$this->shipment->id],
            'client_id' => ['required', 'exists:clients,id'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'shipment_type' => ['required', 'in:air,sea,road'],
            'current_status' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'expected_delivery_date' => ['nullable', 'date'],
            'china_warehouse_date' => ['nullable', 'date'],

            // Package Details
            'num_packages' => ['nullable', 'integer', 'min:1'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'package_type' => ['nullable', 'in:box,pallet,envelope,custom'],
            'fragile' => ['nullable', 'boolean'],
            'special_instructions' => ['nullable', 'string'],

            // Receiver Information
            'receiver_id' => ['nullable', 'exists:clients,id'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'receiver_phone' => ['nullable', 'string', 'max:20'],
            'receiver_address' => ['nullable', 'string'],

            // Pricing & Billing
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'insurance_value' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'payment_method' => ['nullable', 'in:cash,card,bank_transfer,cod'],
            'payment_status' => ['nullable', 'in:pending,paid,refunded'],

            // Additional Details
            'service_type' => ['nullable', 'in:express,standard,economy'],
            'delivery_instructions' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'special_notes' => ['nullable', 'string'],

            // Customs Information
            'is_international' => ['nullable', 'boolean'],
            'customs_value' => ['nullable', 'numeric', 'min:0'],
            'customs_description' => ['nullable', 'string'],
        ];
    }
}
