<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'table_session_id',
        'grand_total',
        'status'
    ];

    function table_session() {
        return $this->belongsTo(TableSession::class);
    }
}
