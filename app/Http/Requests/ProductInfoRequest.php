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
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:20|unique:product_infos,serial_number,' . $this->route('id'),
            'purchase_date' => 'nullable|date',
            'warranty_status' => 'nullable|string|in:warranty,expired',
            'customer_detail_id' => 'nullable|exists:customer_details,id'
        ];
    }
}