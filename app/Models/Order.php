<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    protected $fillable = [
        'table_session_id',
        'confirmed'
    ];

    protected $casts = [
        'confirmed' => 'boolean',
    ];

    function items() {
        return $this->hasMany(OrderItem::class);
    }
}