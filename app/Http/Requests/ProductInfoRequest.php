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
            'orig_box' => 'nullable|string',  
            'gen_box' => 'nullable|string',   
            'manual' => 'nullable|string',    
            'driver_cd' => 'nullable|string', 
            'sata_cable' => 'nullable|string',
            'simcard_memorycard_gb' => 'nullable|string|max:255',
            'remote_control' => 'nullable|string',
            'receiver' => 'nullable|string',   
            'backplate_metal_plate' => 'nullable|string',
            'ac_adapter' => 'nullable|string',
            'battery_pack' => 'nullable|string',
            'lithium_battery' => 'nullable|string',
            'vga_cable' => 'nullable|string',  
            'dvi_cable' => 'nullable|string',  
            'display_cable' => 'nullable|string',
            'bag_pn' => 'nullable|string|max:255',
            'swivel_base' => 'nullable|string',
            'hdd' => 'nullable|string',       
            'ram_brand' => 'nullable|string|max:255',
            'ram_size_gb' => 'nullable|string|min:0',
            'power_cord_qty' => 'nullable|string|min:0',
            'printer_cable_qty' => 'nullable|string|min:0',
            'usb_cable_qty' => 'nullable|string|min:0',
            'paper_tray_qty' => 'nullable|string|min:0',
            'screw_qty' => 'nullable|string|min:0',
            'jack_cable_qty' => 'nullable|string|min:0',
        ];        
    }
}