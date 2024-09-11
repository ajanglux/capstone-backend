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
        'status',
        'customer_detail_id'
    ];

    /**
     * Get the customer detail that owns the product info.
     */
    public function customerDetail()
    {
        return $this->belongsTo(CustomerDetail::class);
    }
}