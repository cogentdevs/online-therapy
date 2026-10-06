<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'column_name',
    'isView',
])]
class AuthorGeneralSetting extends Model
{
    use HasAuditOwnership;

    /** @var array<string, bool> */
    public const DEFAULT_VISIBILITY = [
        'name' => true,
        'contact_number' => false,
        'email' => false,
        'qualification' => true,
        'experience_detail' => true,
        'experience_years' => true,
        'speciality' => true,
        'picture' => true,
    ];

    /** @var array<string, string> */
    public const FIELD_LABELS = [
        'name' => 'Name',
        'contact_number' => 'Contact Number',
        'email' => 'Email',
        'qualification' => 'Qualification',
        'experience_detail' => 'Experience Detail',
        'experience_years' => 'Experience Years',
        'speciality' => 'Speciality',
        'picture' => 'Picture',
    ];

    /** @return list<string> */
    public static function configurableFields(): array
    {
        return array_keys(self::FIELD_LABELS);
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
