<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'media_type',
    'media_id',
    'storage_provider_id',
    'path',
    'file_name',
    'size',
    'mime_type',
    'checksum',
    'is_primary',
    'priority',
    'status',
    'verification_status',
    'last_verified_at',
    'error_message',
])]
class MediaStorageLocation extends Model
{
    public const MEDIA_MAGAZINE = 'magazine';

    public const MEDIA_AUDIO = 'audio';

    public const STATUS_PENDING = 'pending';

    public const STATUS_UPLOADING = 'uploading';

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_FAILED = 'failed';

    public const STATUS_DELETED = 'deleted';

    public const VERIFICATION_PENDING = 'pending';

    public const VERIFICATION_VERIFIED = 'verified';

    public const VERIFICATION_FAILED = 'failed';

    public function storageProvider(): BelongsTo
    {
        return $this->belongsTo(StorageProvider::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'media_id' => 'integer',
            'storage_provider_id' => 'integer',
            'size' => 'integer',
            'is_primary' => 'boolean',
            'priority' => 'integer',
            'last_verified_at' => 'datetime',
        ];
    }
}
