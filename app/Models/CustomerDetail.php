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
        'user_id',
        'description',
        'status',
        'comment',
        'status_updated_at',
        'on_going_updated_at',
        'finished_updated_at',
        'ready_for_pickup_updated_at',
        'completed_updated_at',
        'cancelled_updated_at',
        'incomplete_updated_at',
        'responded_updated_at'
    ];

    /**
     * Get the user that owns the customer details.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
            case 'On-Going':
                $this->on_going_updated_at = Carbon::now();
                break;

            case 'Finished':
                $this->finished_updated_at = Carbon::now();
                break;

            case 'Ready-for-Pickup':
                $this->ready_for_pickup_updated_at = Carbon::now();
                break;

            case 'Completed':
                $this->completed_updated_at = Carbon::now();
                break;
                
            case 'Cancelled':
                $this->cancelled_updated_at = Carbon::now();
                break;

            case 'Incomplete':
                $this->incomplete_updated_at = Carbon::now();
                break;

            case 'Responded':
                $this->responded_updated_at = Carbon::now();
                break;
        }
        $this->status_updated_at = Carbon::now();
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst(strtolower($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst(strtolower($value));
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = ucfirst(strtolower($value));
    }

    public function setPhoneNumberAttribute($value)
    {
        $phoneNumber = preg_replace('/\D/', '', $value);
    
        if (substr($phoneNumber, 0, 3) === '639') {
            $this->attributes['phone_number'] = '+63 ' . substr($phoneNumber, 2);
        } elseif (substr($phoneNumber, 0, 2) == '09') {
            $this->attributes['phone_number'] = '+63 ' . substr($phoneNumber, 1);
        } else {
            throw new \InvalidArgumentException('Phone number must start with +63 9 and contain 11 digits.');
        }
    }
    
    public function isCompletelyFilled(): bool
    {
        // $requiredFields = ['first_name', 'last_name', 'phone_number', 'address', 'description'];
        $requiredFields = ['description'];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        if ($this->productInfos()->count() === 0) {
            return false;
        }

        return true;
    }
}
