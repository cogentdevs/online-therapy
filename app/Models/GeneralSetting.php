<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'app_name',
    'url',
    'google_ads_client_id',
    'logo',
    'footer_logo',
    'footer_text',
    'favicon',
    'contact_1',
    'contact_2',
    'email',
    'address',
    'facebook',
    'instagram',
    'youtube',
    'linkedin',
    'tiktok',
    'x',
    'app_section_heading',
    'app_section_text',
    'play_store_icon',
    'play_store_link',
    'app_store_icon',
    'app_store_link',
    'default_language_id',
    'max_devices_per_user',
    'max_concurrent_sessions',
    'cookie_consent_enabled',
    'maintenance_mode',
])]
class GeneralSetting extends Model
{
    use HasAuditOwnership;

    public function defaultLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'default_language_id');
    }

    public static function current(): self
    {
        return self::query()->firstOrNew();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_language_id' => 'integer',
            'max_devices_per_user' => 'integer',
            'max_concurrent_sessions' => 'integer',
            'cookie_consent_enabled' => 'boolean',
            'maintenance_mode' => 'boolean',
        ];
    }
}
