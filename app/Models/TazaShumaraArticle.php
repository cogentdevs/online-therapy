<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'taza_shumara_id',
    'article_id',
    'display_width',
    'position',
    'sort_order',
])]
class TazaShumaraArticle extends Model
{
    public const DISPLAY_WIDTHS = [
        'full' => 'Full Width',
        'half' => '2 Columns',
        'third' => '3 Columns',
    ];

    public const POSITIONS = [
        'top' => 'Top',
        'center' => 'Center',
        'bottom' => 'Bottom',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'sort_order' => 0,
    ];

    public function tazaShumara(): BelongsTo
    {
        return $this->belongsTo(TazaShumara::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
