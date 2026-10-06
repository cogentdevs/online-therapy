<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['language', 'page', 'title', 'description'])]
class InfoPage extends Model
{
    public function scopeForPage(Builder $query, string $page, string $language): Builder
    {
        return $query->where('page', $page)->where('language', $language);
    }
}
