<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'item_id',
        'amount'
    ];
    
    function item() {
        return $this->belongsTo(Item::class);
    }
}
