<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'name',
    'contact_number',
    'email',
    'qualification',
    'experience_detail',
    'experience_years',
    'speciality',
    'picture',
    'isActive',
])]
class Author extends Model
{
    use HasAuditOwnership;

    /** @var array<string, mixed> */
    protected $attributes = [
        'picture' => 'images/backend-images/author/author.png',
    ];

    public function siteVisits(): MorphMany
    {
        return $this->morphMany(SiteVisit::class, 'visitable');
    }

    public function magazines(): BelongsToMany
    {
        return $this->belongsToMany(Magazine::class, 'magazine_author_maps')->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_author_maps')->withTimestamps();
    }

    public function visibilities(): HasMany
    {
        return $this->hasMany(AuthorVisibility::class);
    }

    /**
     * @param  array<string, bool>  $defaultVisibility
     * @return array<string, bool>
     */
    public function effectiveVisibility(array $defaultVisibility): array
    {
        $visibility = $defaultVisibility;

        foreach ($this->visibilities as $fieldVisibility) {
            if (array_key_exists($fieldVisibility->column_name, $visibility)) {
                $visibility[$fieldVisibility->column_name] = (bool) $fieldVisibility->isView;
            }
        }

        return $visibility;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'isActive' => 'boolean',
        ];
    }
}
