<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'phone', 'email', 'subject', 'message'])]
class Contact extends Model
{
    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
