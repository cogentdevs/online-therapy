<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'user_id',
    'user_name',
    'role_name',
    'module',
    'action',
    'subject_type',
    'subject_id',
    'description',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
])]
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Activity Log records are immutable.'));
        static::deleting(fn (): never => throw new LogicException('Activity Log records are immutable.'));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
