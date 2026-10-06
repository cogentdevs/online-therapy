<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'language',
    'content_position',
    'top_heading',
    'main_heading',
    'bottom_text',
    'button_label',
    'button_url',
    'image',
    'isActive',
])]
class Slider extends Model
{
    use HasAuditOwnership;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
        ];
    }
}
