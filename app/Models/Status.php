<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    public $fillable = [
        'name',
    ];

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'status_id');
    }
}
