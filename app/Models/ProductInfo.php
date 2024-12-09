<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand', 
        'model', 
        'serial_number', 
        'purchase_date', 
        'documentation',
        'warranty_status',
        'customer_detail_id',
        'orig_box',
        'gen_box',
        'manual',
        'driver_cd',
        'sata_cable',
        'simcard_memorycard_gb',
        'remote_control',
        'receiver',
        'backplate_metal_plate',
        'ac_adapter',
        'battery_pack',
        'lithium_battery',
        'vga_cable',
        'dvi_cable',
        'display_cable',
        'bag_pn',
        'swivel_base',
        'hdd',
        'ram_brand',
        'ram_size_gb',
        'power_cord_qty',
        'printer_cable_qty',
        'usb_cable_qty',
        'paper_tray_qty',
        'screw_qty',
        'jack_cable_qty'
    ];

    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class, 'customer_detail_id');
    }

    public function setBrandAttribute($value)
    {
        $this->attributes['brand'] = ucfirst(strtolower($value));
    }

    public function setModelAttribute($value)
    {
        $this->attributes['model'] = ucfirst(strtolower($value));
    }
}
