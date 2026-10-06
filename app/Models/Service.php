<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'image', 'description', 'isactive'])]
class Service extends Model
{
    use HasAuditOwnership;

    protected $attributes = ['isactive' => true];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['isactive' => 'boolean'];
    }
}
