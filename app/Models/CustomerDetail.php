<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'address',
    ];

    protected static function booted()
    {
        static::creating(function ($customerDetail) {
            if (empty($customerDetail->code)) {
                $customerDetail->code = 'CUST-' . Str::upper(Str::random(8));
            }
        });
    }

    /**
     * Get the product info associated with the customer detail.
     */
    public function productInfo()
    {
        return $this->hasOne(ProductInfo::class);
    }
}