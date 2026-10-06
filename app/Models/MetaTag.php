<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'table_name',
    'table_id',
    'language',
    'title',
    'keywords',
    'description',
    'slug_url',
    'canonical_url',
])]
class MetaTag extends Model
{
    use HasAuditOwnership;

    /** @var array<string, string> */
    public const CORE_PAGES = [
        'Home' => '/',
        'About' => 'about',
        'Contact' => 'contact',
        'FAQ' => 'faq',
        'Categories' => 'categories',
        'Magazine' => 'magazine',
        'Articles' => 'articles',
        'Audio Library' => 'audio-library',
        'Consultant/Consultancy' => 'consultancy',
    ];

    /** @var array<string, string> */
    public const MODULE_LABELS = [
        'categories' => 'Category',
        'magazines' => 'Magazine',
        'articles' => 'Article',
        'audios' => 'Audio',
    ];

    public function moduleLabel(): string
    {
        if ($this->table_name === null) {
            return 'Main Page';
        }

        return self::MODULE_LABELS[$this->table_name] ?? str($this->table_name)->singular()->headline()->toString();
    }

    public static function corePageSlug(string $title): ?string
    {
        return self::CORE_PAGES[$title] ?? null;
    }

    public static function moduleSlug(?string $title): ?string
    {
        $slug = Str::slug($title ?? '');

        return $slug !== '' ? $slug : null;
    }
}
