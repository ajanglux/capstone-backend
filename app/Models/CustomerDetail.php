<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        'status',
        'status_updated_at',
        'on_going_updated_at',
        'finished_updated_at',
        'ready_for_pickup_updated_at',
        'completed_updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($customerDetail) {
            if (empty($customerDetail->code)) {
                $customerDetail->code = 'CUST-' . Str::upper(Str::random(8));
            }
        });

        static::updating(function ($customerDetail) {
            $customerDetail->updateStatusTimestamps();
        });
    }

    public function productInfos()
    {
        return $this->hasMany(ProductInfo::class, 'customer_detail_id');
    }

    public function updateStatusTimestamps()
    {
        switch ($this->status) {
            case 'on-going':
                $this->on_going_updated_at = Carbon::now();
                break;

            case 'finished':
                $this->finished_updated_at = Carbon::now();
                break;

            case 'ready-for-pickup':
                $this->ready_for_pickup_updated_at = Carbon::now();
                break;

            case 'completed':
                $this->completed_updated_at = Carbon::now();
                break;
        }

        $this->status_updated_at = Carbon::now();
    }
}
