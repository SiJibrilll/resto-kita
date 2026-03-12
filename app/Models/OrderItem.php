<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'item_id',
        'amount'
    ];

    public function getSubtotalAttribute()
    {
        return $this->amount * $this->item->price;
    }
    
    function item() {
        return $this->belongsTo(Item::class);
    }
}
