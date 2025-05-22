<?php

namespace App\Models;

use App\Services\Integrations\Aifabu\Enums\ChainType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\DomainComparator;
/**
 * 
 *
 * @property int $id 主键ID
 * @property string|null $chain_title 链接标题
 * @property string|null $domain
 * @property string $target_url 目标网址
 * @property string|null $status 状态
 * @property \Illuminate\Support\Carbon|null $create_time 创建时间
 * @property int $pv_history 历史访问量
 * @property int $pv_today 今日访问量
 * @property string $chain 链接后缀
 * @property string|null $domain_url
 * @property int $domain_status 域名状态(1:已生效 99:未生效)
 * @property ChainType|null $type
 * @property int|null $sub_type
 * @property string $render_url 渲染网址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $group_id 所属分组ID
 * @property-read string $chain_type_label
 * @property-read \App\Models\ChainGroup|null $group
 * @method static \Database\Factories\ChainFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereChain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereChainTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereDomainStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereDomainUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain wherePvHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain wherePvToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereRenderUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereTargetUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Chain extends Model
{
    use HasFactory;
    protected $fillable = [
        'chain_title',
        'domain',
        'target_url',
        'status',
        'create_time',
        'pv_history',
        'pv_today',
        'chain',
        'domain_url',
        'domain_status',
        'type',
        'sub_type',
        'render_url',
        'group_id',
    ];

    protected $casts = [
        'create_time' => 'datetime',
        'domain_status' => 'integer',
        'type' => ChainType::class,
        'sub_type' => 'integer',
    ];

    //1：已生效 99：未生效
    protected function domainStatus(): Attribute
    {
        return Attribute::make(
            get: fn (string|int $value) => (int)$value === 1 ? '已生效' : '未生效',
        );
    }

    public function equalsDomain(string $domain): bool
    {
        return DomainComparator::equals($this->target_url, $domain);
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChainGroup::class, 'group_id', 'group_id');
    }

}
