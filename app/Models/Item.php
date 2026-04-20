<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'img',
        'is_active',
        'category_id',
    ];

    function category() {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
