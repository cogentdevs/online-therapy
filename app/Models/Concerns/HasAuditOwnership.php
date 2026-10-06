<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuditOwnership
{
    protected static function bootHasAuditOwnership(): void
    {
        static::creating(function ($model): void {
            $actorId = auth()->id();

            if ($actorId !== null) {
                $model->setAttribute('created_by', $model->getAttribute('created_by') ?? $actorId);
                $model->setAttribute('updated_by', $model->getAttribute('updated_by') ?? $actorId);
            }
        });

        static::updating(function ($model): void {
            if (auth()->id() !== null) {
                $model->setAttribute('updated_by', auth()->id());
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
