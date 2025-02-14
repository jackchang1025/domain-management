<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'status',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'expires_at' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // 监听删除事件
        static::deleted(function ($domain) {
            // 从Redis队列中移除域名
            Redis::lrem(\App\Http\Controllers\RedirectController::QUEUE_KEY, 0, $domain->domain);
        });

        // 监听状态变更
        static::updated(function ($domain) {
            if ($domain->status === 'expired' && $domain->isDirty('status')) {
                // 如果状态改为expired，也从队列中移除
                Redis::lrem(\App\Http\Controllers\RedirectController::QUEUE_KEY, 0, $domain->domain);
            }
        });
    }
}
