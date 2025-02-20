<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = [
        'code',
        'short_url',
        'long_url',
        'visit_count',
        'edit_link',
        'last_synced_at'
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('id', 'desc');
        });
    }
}
