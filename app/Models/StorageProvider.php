<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'disk',
    'provider_type',
    'is_active',
    'is_default',
    'priority',
    'health_status',
    'last_checked_at',
])]
class StorageProvider extends Model
{
    public const TYPE_LOCAL = 'local';

    public const TYPE_R2 = 'r2';

    public const TYPE_S3 = 's3';

    public const HEALTH_UNKNOWN = 'unknown';

    public const HEALTH_HEALTHY = 'healthy';

    public const HEALTH_DEGRADED = 'degraded';

    public const HEALTH_UNAVAILABLE = 'unavailable';

    public function mediaLocations(): HasMany
    {
        return $this->hasMany(MediaStorageLocation::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'priority' => 'integer',
            'last_checked_at' => 'datetime',
        ];
    }
}
