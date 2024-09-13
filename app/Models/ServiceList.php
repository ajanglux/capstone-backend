<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceList extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_title',
        'description',
        'image',
    ];
}
