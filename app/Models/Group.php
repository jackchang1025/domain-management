<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }
} 