<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{

    protected $fillable = [
        'table_id',
        'token',
        'status',
        'seated_at',
        'checked_out_at'
    ];
    
    function orders() {
        return $this->hasMany(Order::class);
    }

    function invoice() {
        return $this->hasOne(Invoice::class);
    }
}
