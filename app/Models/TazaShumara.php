<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'language',
    'magazine_id',
    'cover_image',
    'show_title',
    'show_short_description',
    'is_active',
])]
class TazaShumara extends Model
{
    use HasAuditOwnership;

    protected $table = 'taza_shumara';

    /** @var array<string, mixed> */
    protected $attributes = [
        'show_title' => true,
        'show_short_description' => true,
        'is_active' => true,
    ];

    public function magazine(): BelongsTo
    {
        return $this->belongsTo(Magazine::class);
    }

    public function articlePlacements(): HasMany
    {
        return $this->hasMany(TazaShumaraArticle::class)->orderBy('position')->orderBy('sort_order');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'show_title' => 'boolean',
            'show_short_description' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
