<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ad_id', 'clicked_at'])]
class AdClick extends Model
{
    public $timestamps = false;

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    protected function casts(): array
    {
        return ['clicked_at' => 'datetime'];
    }
}
