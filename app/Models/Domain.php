<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Redis;

/**
 * @property string $domain
 * @property string $status
 * @property int $group_id
 * @property \Carbon\Carbon $registered_at
 * @property \Carbon\Carbon $expires_at
 */
class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'status',
        'group_id',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'expires_at' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        // 监听删除事件
        static::deleted(function ($domain) {
            // 从Redis队列中移除域名
            Redis::lrem(\App\Services\DomainRedirectService::QUEUE_KEY, 0, $domain->domain);
        });

        // 监听状态变更
        static::updated(function ($domain) {
            if ($domain->status === 'expired' && $domain->isDirty('status')) {
                // 如果状态改为expired，也从队列中移除
                Redis::lrem(\App\Services\DomainRedirectService::QUEUE_KEY, 0, $domain->domain);
            }
        });
    }
}
