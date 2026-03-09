<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    function items() {
        return $this->hasMany(OrderItem::class);
    }
}