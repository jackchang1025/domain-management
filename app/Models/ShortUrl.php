<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $short_url
 * @property string $long_url
 * @property int $visit_count
 * @property string|null $edit_link
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereEditLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereLongUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereShortUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShortUrl whereVisitCount($value)
 * @mixin \Eloquent
 */
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
}
