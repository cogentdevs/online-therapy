<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['language', 'position', 'title', 'image', 'description', 'is_active'])]
class HomeCard extends Model
{
    use HasAuditOwnership;

    public const POSITIONS = [
        'below slider' => 'Below Slider',
        'above footer' => 'Above Footer',
    ];

    protected $attributes = ['is_active' => true];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
