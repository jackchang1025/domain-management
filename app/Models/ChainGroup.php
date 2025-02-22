<?php

namespace App\Models;

use App\Services\Integrations\Aifabu\Enums\ChainType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $group_id 分组ID
 * @property string $group_name 分组名称
 * @property ChainType $chain_type 链接类型
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