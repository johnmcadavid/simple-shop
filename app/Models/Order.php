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

    public function status()
    {
        return $this->belongsTo('App\Models\Status');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }    
}
