<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchStatusRequest extends FormRequest
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
            'current_status' => 'required|in:Pending,Picked Up,In Transit,Arrived at Facility,Out for Delivery,Delivered,On Hold,Cancelled',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'storage_fee' => 'nullable|numeric|min:0',
        ];
    }
}
