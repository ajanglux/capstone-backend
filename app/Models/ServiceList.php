<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceList extends Model
{
    use HasFactory;

    protected $table = 'services'; // Updated to match migration

    protected $fillable = [
        'service_title',
        'description',
    ];
}
