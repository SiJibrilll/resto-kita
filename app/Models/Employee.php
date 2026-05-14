<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'telephone',
        'user_id'
    ];

    function user() {
        return $this->belongsTo(User::class);
    }
}
