<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['language', 'name', 'isActive'])]
class Tags extends Model
{
    use HasAuditOwnership;

    public function magazines(): BelongsToMany
    {
        return $this->belongsToMany(Magazine::class, 'magazine_tag_maps', 'tag_id', 'magazine_id')->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag_maps', 'tag_id', 'article_id')->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
        ];
    }
}
