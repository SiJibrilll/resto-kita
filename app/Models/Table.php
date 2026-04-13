<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'number'
    ];

    function sessions() {
        return $this->hasMany(TableSession::class);
    }

    public function activeSessions()
    {
        return $this->sessions()->where('status', 'active');
    }

    public function hasActiveSession(): bool
    {
        return $this->activeSessions()->exists();
    }
}
