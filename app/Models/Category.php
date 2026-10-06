<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['language', 'name', 'image', 'isActive'])]
class Category extends Model
{
    use HasAuditOwnership;

    public function siteVisits(): MorphMany
    {
        return $this->morphMany(SiteVisit::class, 'visitable');
    }

    public function magazines(): BelongsToMany
    {
        return $this->belongsToMany(Magazine::class, 'magazine_category_maps')->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category_maps')->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
        ];
    }
}
