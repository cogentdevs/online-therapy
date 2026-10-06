<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'author_id',
    'column_name',
    'isView',
])]
class AuthorVisibility extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'isView' => 'boolean',
        ];
    }
}
