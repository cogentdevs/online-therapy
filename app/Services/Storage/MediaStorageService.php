<?php

namespace App\Services\Storage;

use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class MediaStorageService
{
    /**
     * Lower priority numbers are preferred.
     *
     * @return Collection<int, StorageProvider>
     */
    public function getActiveProviders(): Collection
    {
        return StorageProvider::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();
    }

    public function getDefaultProvider(): ?StorageProvider
    {
        return StorageProvider::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->first();
    }

    /**
     * @param  array<int, int|string>  $providerIds
     * @return Collection<int, MediaStorageLocation>
     */
    public function store(
        string $mediaType,
        int $mediaId,
        UploadedFile $file,
        array $providerIds = [],
    ): Collection {
        $this->ensureSupportedMediaType($mediaType);
        $providers = $this->selectedProviders($providerIds);

        if ($providers->isEmpty()) {
            throw new RuntimeException('No active storage provider is available for this media upload.');
        }

        $primaryProvider = $providers->firstWhere('is_default', true) ?? $providers->first();
        $path = $this->makePath($mediaType, $mediaId, $file);

        return $providers->map(fn (StorageProvider $provider, int $index): MediaStorageLocation => $this->storeOnProvider(
            mediaType: $mediaType,
            mediaId: $mediaId,
            file: $file,
            provider: $provider,
            path: $path,
            isPrimary: $provider->is($primaryProvider),
            priority: $index + 1,
        ));
    }

    public function replicate(
        MediaStorageLocation $source,
        StorageProvider $targetProvider,
        ?int $priority = null,
    ): MediaStorageLocation {
        if (! $targetProvider->is_active) {
            throw new InvalidArgumentException('Media can only be replicated to an active storage provider.');
        }

        $source->loadMissing('storageProvider');

        if ($source->status !== MediaStorageLocation::STATUS_AVAILABLE || ! $source->storageProvider) {
            throw new InvalidArgumentException('The source media location is not available.');
        }

        $location = MediaStorageLocation::query()->updateOrCreate(
            [
                'media_type' => $source->media_type,
                'media_id' => $source->media_id,
                'storage_provider_id' => $targetProvider->id,
                'path' => $source->path,
            ],
            [
                'file_name' => $source->file_name,
                'size' => $source->size,
                'mime_type' => $source->mime_type,
                'checksum' => $source->checksum,
                'is_primary' => false,
                'priority' => $priority ?? $targetProvider->priority,
                'status' => MediaStorageLocation::STATUS_UPLOADING,
                'verification_status' => MediaStorageLocation::VERIFICATION_PENDING,
                'error_message' => null,
            ],
        );

        $stream = null;

        try {
            $stream = Storage::disk($source->storageProvider->disk)->readStream($source->path);

            if (! is_resource($stream)) {
                throw new RuntimeException('Unable to read the source media file.');
            }

            if (! Storage::disk($targetProvider->disk)->writeStream($source->path, $stream)) {
                throw new RuntimeException('Unable to write the replicated media file.');
            }

            $location->update([
                'status' => MediaStorageLocation::STATUS_AVAILABLE,
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $location->update([
                'status' => MediaStorageLocation::STATUS_FAILED,
                'error_message' => Str::limit($exception->getMessage(), 2000),
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $location->fresh(['storageProvider']);
    }

    public function resolve(string $mediaType, int $mediaId): ?MediaStorageLocation
    {
        $this->ensureSupportedMediaType($mediaType);

        return MediaStorageLocation::query()
            ->with('storageProvider')
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->where('status', MediaStorageLocation::STATUS_AVAILABLE)
            ->whereHas('storageProvider', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_primary')
            ->orderBy('priority')
            ->orderBy('id')
            ->first();
    }

    /**
     * @return resource
     */
    public function openReadStream(MediaStorageLocation $location): mixed
    {
        $location->loadMissing('storageProvider');

        if ($location->status !== MediaStorageLocation::STATUS_AVAILABLE
            || blank($location->path)
            || ! $location->storageProvider) {
            throw new RuntimeException('The requested media file is not available.');
        }

        try {
            $disk = Storage::disk($location->storageProvider->disk);

            if (! $disk->exists($location->path)) {
                throw new RuntimeException('The requested media file does not exist.');
            }

            $stream = $disk->readStream($location->path);

            if (! is_resource($stream)) {
                throw new RuntimeException('Unable to read the requested media file.');
            }

            return $stream;
        } catch (Throwable $exception) {
            throw new RuntimeException('The requested media file could not be opened.', previous: $exception);
        }
    }

    public function delete(MediaStorageLocation $location): bool
    {
        $location->loadMissing('storageProvider');

        if ($location->status === MediaStorageLocation::STATUS_DELETED) {
            return true;
        }

        try {
            if ($location->path && $location->storageProvider) {
                Storage::disk($location->storageProvider->disk)->delete($location->path);
            }

            $location->update([
                'status' => MediaStorageLocation::STATUS_DELETED,
                'error_message' => null,
            ]);

            return true;
        } catch (Throwable $exception) {
            $location->update(['error_message' => Str::limit($exception->getMessage(), 2000)]);

            return false;
        }
    }

    public function verify(MediaStorageLocation $location): bool
    {
        $location->loadMissing('storageProvider');

        if (! $location->path || ! $location->storageProvider) {
            return false;
        }

        try {
            $exists = Storage::disk($location->storageProvider->disk)->exists($location->path);

            $location->update([
                'verification_status' => $exists
                    ? MediaStorageLocation::VERIFICATION_VERIFIED
                    : MediaStorageLocation::VERIFICATION_FAILED,
                'last_verified_at' => now(),
                'error_message' => $exists ? null : 'Media file was not found on the configured disk.',
            ]);
            $location->storageProvider->update([
                'health_status' => $exists
                    ? StorageProvider::HEALTH_HEALTHY
                    : StorageProvider::HEALTH_DEGRADED,
                'last_checked_at' => now(),
            ]);

            return $exists;
        } catch (Throwable $exception) {
            $location->update([
                'verification_status' => MediaStorageLocation::VERIFICATION_FAILED,
                'last_verified_at' => now(),
                'error_message' => Str::limit($exception->getMessage(), 2000),
            ]);
            $location->storageProvider->update([
                'health_status' => StorageProvider::HEALTH_UNAVAILABLE,
                'last_checked_at' => now(),
            ]);

            return false;
        }
    }

    /**
     * @param  array<int, int|string>  $providerIds
     * @return Collection<int, StorageProvider>
     */
    private function selectedProviders(array $providerIds): Collection
    {
        if ($providerIds === []) {
            $defaultProvider = $this->getDefaultProvider();

            return $defaultProvider ? new Collection([$defaultProvider]) : new Collection;
        }

        return StorageProvider::query()
            ->where('is_active', true)
            ->whereIn('id', array_values(array_unique(array_map('intval', $providerIds))))
            ->orderBy('priority')
            ->orderBy('id')
            ->get();
    }

    private function storeOnProvider(
        string $mediaType,
        int $mediaId,
        UploadedFile $file,
        StorageProvider $provider,
        string $path,
        bool $isPrimary,
        int $priority,
    ): MediaStorageLocation {
        $location = MediaStorageLocation::query()->create([
            'media_type' => $mediaType,
            'media_id' => $mediaId,
            'storage_provider_id' => $provider->id,
            'path' => $path,
            'file_name' => $this->safeOriginalName($file),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'checksum' => hash_file('sha256', $file->getRealPath()) ?: null,
            'is_primary' => $isPrimary,
            'priority' => $priority,
            'status' => MediaStorageLocation::STATUS_UPLOADING,
            'verification_status' => MediaStorageLocation::VERIFICATION_PENDING,
        ]);

        $stream = null;

        try {
            $stream = fopen($file->getRealPath(), 'rb');

            if (! is_resource($stream) || ! Storage::disk($provider->disk)->writeStream($path, $stream)) {
                throw new RuntimeException('Unable to write the uploaded media file.');
            }

            $location->update([
                'status' => MediaStorageLocation::STATUS_AVAILABLE,
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $location->update([
                'status' => MediaStorageLocation::STATUS_FAILED,
                'error_message' => Str::limit($exception->getMessage(), 2000),
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $location->fresh(['storageProvider']);
    }

    private function makePath(string $mediaType, int $mediaId, UploadedFile $file): string
    {
        $extension = Str::lower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $fileName = (string) Str::ulid().($extension !== '' ? '.'.$extension : '');

        return $mediaType.'/'.$mediaId.'/'.$fileName;
    }

    private function safeOriginalName(UploadedFile $file): string
    {
        $extension = Str::lower($file->getClientOriginalExtension());
        $baseName = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9._-]+/', '-')
            ->trim('-._')
            ->value();

        return ($baseName !== '' ? $baseName : 'media').($extension !== '' ? '.'.$extension : '');
    }

    private function ensureSupportedMediaType(string $mediaType): void
    {
        if (! in_array($mediaType, [MediaStorageLocation::MEDIA_MAGAZINE, MediaStorageLocation::MEDIA_AUDIO], true)) {
            throw new InvalidArgumentException('Unsupported media type.');
        }
    }
}
