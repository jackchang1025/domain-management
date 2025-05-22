<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Redis;

/**
 * 
 *
 * @property int $id
 * @property string $domain
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $group_id
 * @property-read \App\Models\Group|null $group
 * @method static \Database\Factories\DomainFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereUpdatedAt($value)
 * @property string|null $wording_title
 * @property string|null $wording
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereWording($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereWordingTitle($value)
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'status',
        'group_id',
        'wording_title',
        'wording',
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
