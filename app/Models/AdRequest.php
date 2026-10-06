<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

#[Fillable(['user_id', 'name', 'email', 'phone', 'company', 'from_date', 'to_date', 'details'])]
class AdRequest extends Model
{
    protected $attributes = ['status' => 'pending'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function placements(): HasMany
    {
        return $this->hasMany(AdRequestPlacement::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function durationDays(): int
    {
        return (int) $this->from_date->diffInDays($this->to_date) + 1;
    }

    public function save(array $options = []): bool
    {
        if ($this->exists) {
            return parent::save($options);
        }

        return DB::transaction(fn (): bool => parent::save($options));
    }

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            $request->request_no = 'AD-TEMP-'.Str::uuid();
        });

        static::created(function (self $request): void {
            $request->forceFill(['request_no' => 'AD-'.str_pad((string) $request->getKey(), 6, '0', STR_PAD_LEFT)])
                ->saveQuietly();
        });

        static::updating(function (self $request): void {
            if ($request->isDirty('request_no')) {
                throw new LogicException('Advertising request numbers cannot be changed.');
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['from_date' => 'date', 'to_date' => 'date'];
    }
}
