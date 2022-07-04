<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $fillable = [
        'name',
        'code',
        'status_id',
        'customer_id',
    ];
}
