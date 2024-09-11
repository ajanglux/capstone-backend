<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:20|unique:product_infos,serial_number,' . $this->id,
            'purchase_date' => 'required|date',
            'status' => 'nullable|string|in:pending,approved,declined',
            'customer_detail_id' => 'required|exists:customer_details,id'
        ];
    }

}