<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    
    protected static function booted()
    {
        static::creating(function ($order) {
           $order->order_id = 'ORD-' . now()->format('d-m-Y') . '-' . Str::upper(Str::random(6));
        });
    }
    
    protected $fillable = [
        'table_session_id',
        'order_id',
        'confirmed'
    ];

    protected $casts = [
        'confirmed' => 'boolean',
    ];

    function items() {
        return $this->hasMany(OrderItem::class);
    }

    function tableSession() {
        return $this->belongsTo(TableSession::class);
    }

    function getPaymentAttribute() {
        return $this->tableSession?->invoice?->payment;
    }

    function getTableAttribute() {
        return $this->tableSession?->table;
    }
}