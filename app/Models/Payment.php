<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'grand_total',
        'payment_method',
        'status',
        'snap_token',
        'customer_name',
        'invoice_id'
    ];
}
