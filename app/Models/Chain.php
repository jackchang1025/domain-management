<?php

namespace App\Models;

use App\Services\Integrations\Aifabu\Enums\ChainType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
/**
 * @property int $id 主键ID
 * @property string|null $chain_title 链接标题
 * @property int $domain 域名ID
 * @property string $target_url 目标网址
 * @property string|null $status 状态
 * @property \Illuminate\Support\Carbon|null $create_time 创建时间
 * @property int $pv_history 历史访问量
 * @property int $pv_today 今日访问量
 * @property string $chain 链接后缀
 * @property string $domain_url 域名地址
 * @property int $domain_status 域名状态
 * @property int $type 链接类型
 * @property int $sub_type 链接子类型
 * @property string $render_url 渲染网址
 * @property int|null $group_id 所属分组ID
 */
class Chain extends Model
{
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
        'group_id'
    ];

    protected $casts = [
        'create_time' => 'datetime',
        'domain_status' => 'integer',
        'type' => ChainType::class,
        'sub_type' => 'integer'
    ];

    //1：已生效 99：未生效
    protected function domainStatus(): Attribute
    {
        return Attribute::make(
            get: fn (string|int $value) => (int)$value === 1 ? '已生效' : '未生效',
        );
    }

    public function getChainTypeLabelAttribute(): string
    {
        return ChainType::from($this->type)->label();
    }

    public function group()
    {
        return $this->belongsTo(ChainGroup::class, 'group_id', 'group_id');
    }
}
