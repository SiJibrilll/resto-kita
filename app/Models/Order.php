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