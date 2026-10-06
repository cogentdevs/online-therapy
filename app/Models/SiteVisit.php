<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'user_id', 'visitor_id', 'public_token_hash', 'page_key', 'route_name', 'url', 'referrer',
    'visitable_type', 'visitable_id', 'ip_address', 'country', 'country_code', 'city', 'region',
    'latitude', 'longitude', 'device', 'device_type', 'browser', 'browser_version', 'os',
    'os_version', 'user_agent', 'started_at', 'last_activity_at', 'duration_seconds', 'ended_at',
])]
class SiteVisit extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_seconds' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }
}
