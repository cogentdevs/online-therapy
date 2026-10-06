<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['language', 'title', 'page_name', 'place', 'ad_image', 'ad_url', 'google_ad_code', 'start_date', 'expiry_date', 'isActive', 'ad_request_id', 'ad_request_placement_id'])]
class Ad extends Model
{
    use HasAuditOwnership;

    protected $attributes = ['click_count' => 0];

    public const PAGE_PLACEMENTS = [
        'header' => [
            'label' => 'Header',
            'places' => [
                'header_ad' => 'Header Advertisement — 728 × 90',
            ],
        ],
        'home' => [
            'label' => 'Home',
            'places' => [
                'home_horizontal_large' => 'Horizontal (Large) — 1020 × 150',
                'home_horizontal_small' => 'Horizontal (Small) — 500 × 150',
            ],
        ],
        'taza_shumara' => [
            'label' => 'Taza Shumara',
            'places' => [
                'taza_sidebar_1_normal' => 'Right Sidebar (1st Normal) — 250 × 300',
                'taza_sidebar_1_tall' => 'Right Sidebar (1st Tall) — 600 × 300',
                'taza_sidebar_2_normal' => 'Right Sidebar (2nd Normal) — 250 × 300',
                'taza_sidebar_2_tall' => 'Right Sidebar (2nd Tall) — 600 × 300',
            ],
        ],
    ];

    public static function placementsForPage(?string $page): array
    {
        return self::PAGE_PLACEMENTS[$page]['places'] ?? [];
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AdClick::class);
    }

    public function adRequest(): BelongsTo
    {
        return $this->belongsTo(AdRequest::class);
    }

    public function adRequestPlacement(): BelongsTo
    {
        return $this->belongsTo(AdRequestPlacement::class);
    }

    public function scopeCurrentlyEligible(Builder $query): Builder
    {
        $today = today()->toDateString();

        return $query
            ->where('isActive', true)
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', $today);
            });
    }

    public function isCurrentlyTrackable(): bool
    {
        $scheme = is_string($this->ad_url) ? strtolower((string) parse_url($this->ad_url, PHP_URL_SCHEME)) : '';

        return $this->isActive
            && blank($this->google_ad_code)
            && filter_var($this->ad_url, FILTER_VALIDATE_URL) !== false
            && in_array($scheme, ['http', 'https'], true)
            && ($this->start_date === null || ! $this->start_date->isAfter(today()))
            && ($this->expiry_date === null || ! $this->expiry_date->isBefore(today()));
    }

    protected function casts(): array
    {
        return ['start_date' => 'date', 'expiry_date' => 'date', 'isActive' => 'boolean', 'click_count' => 'integer'];
    }
}
