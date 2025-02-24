<?php

namespace App\Models;

use App\Services\Integrations\Aifabu\Enums\ChainType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id 主键ID
 * @property int $group_id 所属分组ID
 * @property string $group_name 分组名称
 * @property ChainType $chain_type 链接类型，使用ChainType枚举
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chain> $chains
 * @property-read int|null $chains_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereChainType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChainGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChainGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'group_name',
        'chain_type'
    ];

    protected $casts = [
        'chain_type' => ChainType::class
    ];

    public function chains()
    {
        return $this->hasMany(Chain::class, 'group_id');
    }
} 