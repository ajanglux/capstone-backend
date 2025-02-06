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
            'serial_number' => 'nullable|string|max:20',
            'purchase_date' => 'nullable|date',
            'documentation' => 'nullable|string',
            'warranty_status' => 'nullable|string|in:warranty,out_of_warranty,chargeable',
            'customer_detail_id' => 'nullable|exists:customer_details,id',
            'ac_adapter' => 'nullable|string',
            'vga_cable' => 'nullable|string',  
            'dvi_cable' => 'nullable|string',  
            'display_cable' => 'nullable|string',
            'bag_pn' => 'nullable|string|max:255',
            'hdd' => 'nullable|string',       
            'ram_brand' => 'nullable|string|max:255',
            'ram_size_gb' => 'nullable|string|min:0',
            'power_cord_qty' => 'nullable|string|min:0',
            'description_of_repair' => 'nullable|string|min:0',
            'address' => 'nullable|string|min:0',
        ];        
    }
}