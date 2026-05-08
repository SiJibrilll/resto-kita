<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{

protected $casts = [
    'seated_at'      => 'datetime',
    'checked_out_at' => 'datetime',
];

    protected $fillable = [
        'table_id',
        'token',
        'status',
        'seated_at',
        'checked_out_at',
        'customer_name'
    ];
    
    function orders() {
        return $this->hasMany(Order::class);
    }

    function invoice() {
        return $this->hasOne(Invoice::class);
    }

    function table() {
        return $this->belongsTo(Table::class);
    }
}
