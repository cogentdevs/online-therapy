<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'language',
    'position',
    'section_condition',
    'title',
    'description',
    'title_2',
    'description_2',
    'image',
    'image_2',
    'is_active',
])]
class HomeSection extends Model
{
    use HasAuditOwnership;

    public const POSITIONS = [
        'below slider' => 'Below Slider',
        'above footer' => 'Above Footer',
    ];

    public const SECTION_LABELS = [
        1 => 'Only Text',
        2 => 'Only Image',
        3 => 'Text - Image',
        4 => 'Image - Text',
        5 => 'Text - Text',
        6 => 'Image - Image',
    ];

    protected $attributes = ['is_active' => true];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'section_condition' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
