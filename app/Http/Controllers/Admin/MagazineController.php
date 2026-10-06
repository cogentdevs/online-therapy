<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMagazineRequest;
use App\Http\Requests\Admin\UpdateMagazineRequest;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\MetaTag;
use App\Models\StorageProvider;
use App\Models\Tags;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AdminContentOwnershipService;
use App\Services\Storage\MediaStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MagazineController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/magazines';

    public function __construct(
        private readonly MediaStorageService $mediaStorageService,
        private readonly AdminContentOwnershipService $adminContentOwnershipService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): View
    {
        $magazines = $this->adminContentOwnershipService
            ->scopeQuery(Magazine::query(), $this->authenticatedAdmin($request))
            ->with(['categories:id,name', 'creator.roles'])
            ->latest()
            ->get();

        return view('admin.magazine.view-magazine', [
            'magazines' => $magazines,
            'languageNames' => Language::query()
                ->whereIn('code', $magazines->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.magazine.add-magazine', $this->formOptions(null, $this->authenticatedAdmin($request)));
    }

    public function store(StoreMagazineRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newCoverPath = null;
        $storedLocations = new Collection;

        try {
            if ($request->hasFile('cover_image')) {
                $newCoverPath = $this->storeCover($request->file('cover_image'));
            }

            DB::transaction(function () use ($request, $validated, $newCoverPath, &$storedLocations): void {
                $magazine = Magazine::query()->newModelInstance([
                    ...$this->magazineData(
                        $validated,
                        $request->boolean('is_downloadable'),
                        $request->boolean('show_visit_counter'),
                    ),
                    'cover_image' => $newCoverPath,
                    'isActive' => true,
                    'status' => Magazine::STATUS_DRAFT,
                ]);
                $magazine->forceFill(['owner_admin_id' => $this->authenticatedAdmin($request)->id]);
                $magazine->save();

                $this->syncRelationships($magazine, $validated, $this->authenticatedAdmin($request));
                $this->updateMetaTag($magazine, $validated);
                $storedLocations = $this->mediaStorageService->store(
                    mediaType: MediaStorageLocation::MEDIA_MAGAZINE,
                    mediaId: $magazine->id,
                    file: $request->file('pdf'),
                    providerIds: $validated['provider_ids'],
                );
                $this->ensureAvailableStorage($storedLocations);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedCover($newCoverPath);
            $this->cleanupNewStorageLocations($storedLocations);

            throw $exception;
        }

        return redirect()->route('admin.magazine.index')->with('status', 'Magazine added as a draft.');
    }

    public function show(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $magazine = $this->findOwnedMagazine($id, $admin, [
            'categories:id,name',
            'tags:id,name',
            'authors:id,name',
            'relatedMagazines' => fn ($query) => $this->adminContentOwnershipService
                ->scopeQuery($query, $admin)
                ->select(['magazines.id', 'title', 'issue_number', 'cover_image', 'status']),
            'storageLocations.storageProvider',
        ]);

        return view('admin.magazine.view-magazine-detail', [
            'magazine' => $magazine,
            'languageName' => Language::query()->where('code', $magazine->language)->value('name'),
            'metaTag' => $this->magazineMetaTag($magazine),
        ]);
    }

    public function edit(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $magazine = $this->findOwnedMagazine($id, $admin, [
            'categories:id',
            'tags:id',
            'authors:id',
            'relatedMagazines' => fn ($query) => $this->adminContentOwnershipService
                ->scopeQuery($query, $admin)
                ->select(['magazines.id']),
            'storageLocations.storageProvider',
        ]);

        return view('admin.magazine.edit-magazine', [
            ...$this->formOptions($magazine, $admin),
            'magazine' => $magazine,
            'metaTag' => $this->magazineMetaTag($magazine),
            'selectedCategoryIds' => $magazine->categories->modelKeys(),
            'selectedTagIds' => $magazine->tags->modelKeys(),
            'selectedAuthorIds' => $magazine->authors->modelKeys(),
            'selectedRelatedMagazineIds' => $magazine->relatedMagazines->modelKeys(),
            'selectedProviderIds' => $magazine->storageLocations
                ->whereNotIn('status', [MediaStorageLocation::STATUS_DELETED])
                ->pluck('storage_provider_id')->unique()->values()->all(),
            'generatedSlug' => MetaTag::moduleSlug($magazine->title),
        ]);
    }

    public function viewPdf(Request $request, int $magazine, int $storageLocation): StreamedResponse
    {
        return $this->pdfResponse($magazine, $storageLocation, $this->authenticatedAdmin($request), inline: true);
    }

    public function downloadPdf(Request $request, int $magazine, int $storageLocation): StreamedResponse
    {
        return $this->pdfResponse($magazine, $storageLocation, $this->authenticatedAdmin($request), inline: false);
    }

    public function removeRelatedMagazine(Request $request, int $magazine, int $relatedMagazine): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $currentMagazine = $this->findOwnedMagazine($magazine, $admin);
        $this->findOwnedMagazine($relatedMagazine, $admin);

        if (! $currentMagazine->relatedMagazines()->whereKey($relatedMagazine)->exists()) {
            abort(404, 'The requested related Magazine mapping does not exist.');
        }

        $currentMagazine->relatedMagazines()->detach($relatedMagazine);

        $this->activityLogService->log(
            'magazines',
            'related_removed',
            $currentMagazine,
            "Removed related Magazine #{$relatedMagazine} from \"{$currentMagazine->title}\".",
            ['related_magazine_id' => $relatedMagazine],
            null,
        );

        return back()->with('status', 'Related Magazine removed successfully.');
    }

    public function update(UpdateMagazineRequest $request, int $id): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $magazine = $this->findOwnedMagazine($id, $admin, ['storageLocations.storageProvider']);
        $validated = $request->validated();
        $previousCoverPath = $magazine->cover_image;
        $newCoverPath = null;
        $newLocations = new Collection;
        $existingLocations = $magazine->storageLocations;
        $providerIds = array_map('intval', $validated['provider_ids'] ?? []);

        try {
            if ($request->hasFile('cover_image')) {
                $newCoverPath = $this->storeCover($request->file('cover_image'));
            }

            DB::transaction(function () use ($request, $magazine, $validated, $newCoverPath, $providerIds, &$newLocations): void {
                $magazineData = $this->magazineData(
                    $validated,
                    $request->boolean('is_downloadable'),
                    $request->boolean('show_visit_counter'),
                );

                if ($newCoverPath !== null) {
                    $magazineData['cover_image'] = $newCoverPath;
                }

                $magazine->update($magazineData);
                $this->syncRelationships($magazine, $validated, $this->authenticatedAdmin($request));
                $this->updateMetaTag($magazine, $validated);

                if ($request->hasFile('pdf')) {
                    if ($providerIds === []) {
                        throw ValidationException::withMessages([
                            'provider_ids' => 'Select at least one active storage provider for the replacement PDF.',
                        ]);
                    }

                    $newLocations = $this->mediaStorageService->store(
                        mediaType: MediaStorageLocation::MEDIA_MAGAZINE,
                        mediaId: $magazine->id,
                        file: $request->file('pdf'),
                        providerIds: $providerIds,
                    );
                    $this->ensureAvailableStorage($newLocations);

                    return;
                }

                $newLocations = $this->replicateToNewProviders($magazine, $providerIds);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedCover($newCoverPath);
            $this->cleanupNewStorageLocations($newLocations);

            throw $exception;
        }

        if ($newCoverPath !== null) {
            $this->deleteManagedCover($previousCoverPath);
        }

        if ($request->hasFile('pdf')) {
            $this->deleteStorageLocations($existingLocations);
        } elseif ($providerIds !== []) {
            $activeProviderIds = $this->mediaStorageService->getActiveProviders()->modelKeys();
            $locationsToRemove = $existingLocations->filter(fn (MediaStorageLocation $location): bool => in_array(
                $location->storage_provider_id,
                $activeProviderIds,
                true,
            ) && ! in_array($location->storage_provider_id, $providerIds, true));
            $this->deleteStorageLocations($locationsToRemove);
        }

        return redirect()->route('admin.magazine.index')->with('status', 'Magazine updated successfully.');
    }

    public function publish(Request $request, int $id): RedirectResponse
    {
        $magazine = $this->findOwnedMagazine($id, $this->authenticatedAdmin($request));

        if ($magazine->status === Magazine::STATUS_PUBLISHED) {
            return back()->with('error', 'This Magazine is already published.');
        }

        if (! $magazine->isActive) {
            return back()->with('error', 'Activate the Magazine before publishing it.');
        }

        if (blank($magazine->title) || blank($magazine->language)) {
            return back()->with('error', 'A title and language are required before publishing.');
        }

        if (! $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id)) {
            return back()->with('error', 'The Magazine cannot be published without an available PDF storage copy.');
        }

        $magazine->forceFill(['published_by' => auth()->id()]);
        $magazine->update([
            'status' => Magazine::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('status', 'Magazine published successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $magazine = $this->findOwnedMagazine(
            $id,
            $this->authenticatedAdmin($request),
            ['storageLocations.storageProvider'],
        );
        $coverPath = $magazine->cover_image;

        foreach ($magazine->storageLocations as $location) {
            if (! $this->mediaStorageService->delete($location)) {
                return back()->with('error', 'A stored PDF copy could not be deleted. The Magazine was not removed.');
            }
        }

        DB::transaction(function () use ($magazine): void {
            $magazine->categories()->detach();
            $magazine->tags()->detach();
            $magazine->authors()->detach();
            MetaTag::query()->where('table_name', $magazine->getTable())->where('table_id', $magazine->id)->delete();
            $magazine->storageLocations()->delete();
            $magazine->delete();
        });
        $this->deleteManagedCover($coverPath);

        return redirect()->route('admin.magazine.index')->with('status', 'Magazine deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function formOptions(?Magazine $magazine, User $admin): array
    {
        return [
            'languages' => Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']),
            'categories' => Category::query()->where('isActive', true)->orderBy('name')->get(['id', 'name', 'language']),
            'tags' => Tags::query()->where('isActive', true)->orderBy('name')->get(['id', 'name', 'language']),
            'authors' => Author::query()->where('isActive', true)->orderBy('name')->get(['id', 'name']),
            'relatedMagazines' => $this->adminContentOwnershipService->scopeQuery(Magazine::query(), $admin)
                ->where('isActive', true)
                ->when($magazine, fn ($query) => $query->whereKeyNot($magazine->id))
                ->orderBy('title')
                ->get(['id', 'language', 'title', 'issue_number']),
            'storageProviders' => $this->mediaStorageService->getActiveProviders(),
        ];
    }

    /** @param array<string, mixed> $validated */
    private function magazineData(array $validated, bool $isDownloadable, bool $showVisitCounter): array
    {
        $magazineData = [
            ...Arr::only($validated, [
                'language', 'title', 'issue_number', 'publish_date',
                'description', 'isFree', 'isFeatured', 'isActive',
            ]),
            'is_downloadable' => $isDownloadable,
            'show_visit_counter' => $showVisitCounter,
        ];

        $magazineData['free_until'] = (bool) $magazineData['isFree']
            ? ($validated['free_until'] ?? null)
            : null;

        return $magazineData;
    }

    /** @param array<string, mixed> $validated */
    private function syncRelationships(Magazine $magazine, array $validated, User $admin): void
    {
        $relatedMagazineIds = array_map('intval', $validated['related_magazine_ids'] ?? []);

        if (in_array($magazine->id, $relatedMagazineIds, true)) {
            throw ValidationException::withMessages([
                'related_magazine_ids' => 'A Magazine cannot be related to itself.',
            ]);
        }

        $this->adminContentOwnershipService->assertRecordsAccessible(
            $admin,
            Magazine::class,
            $relatedMagazineIds,
            'related_magazine_ids',
        );

        $magazine->categories()->sync($validated['category_ids'] ?? []);
        if (array_key_exists('tag_ids', $validated)) {
            $magazine->tags()->sync($validated['tag_ids'] ?? []);
        }
        $magazine->authors()->sync($validated['author_ids'] ?? []);
        $magazine->relatedMagazines()->sync(array_values(array_unique($relatedMagazineIds)));
    }

    /** @param array<string, mixed> $validated */
    private function updateMetaTag(Magazine $magazine, array $validated): void
    {
        $metaTag = MetaTag::query()
            ->where('table_name', $magazine->getTable())->where('table_id', $magazine->id)->first();

        if ($metaTag && $metaTag->language !== $magazine->language) {
            $metaTag->update(['language' => $magazine->language]);
        }

        $values = [
            'language' => $magazine->language,
            'title' => $magazine->title,
            'keywords' => $validated['keywords'] ?? null,
            'description' => $validated['meta_description'] ?? null,
            'slug_url' => MetaTag::moduleSlug($magazine->title),
            'canonical_url' => $metaTag?->canonical_url,
        ];

        MetaTag::query()->updateOrCreate([
            'table_name' => $magazine->getTable(),
            'table_id' => $magazine->id,
            'language' => $magazine->language,
        ], $values);
    }

    private function magazineMetaTag(Magazine $magazine): ?MetaTag
    {
        return MetaTag::query()
            ->where('table_name', $magazine->getTable())
            ->where('table_id', $magazine->id)
            ->where('language', $magazine->language)
            ->first();
    }

    private function pdfResponse(
        int $magazineId,
        int $storageLocationId,
        User $admin,
        bool $inline,
    ): StreamedResponse {
        $this->findOwnedMagazine($magazineId, $admin);

        $location = MediaStorageLocation::query()
            ->with('storageProvider')
            ->whereKey($storageLocationId)
            ->where('media_type', MediaStorageLocation::MEDIA_MAGAZINE)
            ->where('media_id', $magazineId)
            ->firstOrFail();

        try {
            $stream = $this->mediaStorageService->openReadStream($location);
        } catch (RuntimeException) {
            abort(404, 'The requested Magazine PDF is not available.');
        }

        $fileName = filled($location->file_name) ? basename($location->file_name) : 'magazine.pdf';
        $mimeType = in_array($location->mime_type, ['application/pdf', 'application/x-pdf'], true)
            ? $location->mime_type
            : 'application/pdf';
        $disposition = HeaderUtils::makeDisposition(
            $inline ? HeaderUtils::DISPOSITION_INLINE : HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName,
            'magazine.pdf',
        );

        return response()->stream(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** @param array<int|string, mixed> $with */
    private function findOwnedMagazine(int $id, User $admin, array $with = []): Magazine
    {
        $magazine = Magazine::query()->with($with)->findOrFail($id);
        $this->adminContentOwnershipService->authorizeAccess($admin, $magazine);

        return $magazine;
    }

    private function authenticatedAdmin(Request $request): User
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        return $admin;
    }

    /** @param Collection<int, MediaStorageLocation> $locations */
    private function ensureAvailableStorage(Collection $locations): void
    {
        if (! $locations->contains('status', MediaStorageLocation::STATUS_AVAILABLE)) {
            throw ValidationException::withMessages(['pdf' => 'The PDF could not be stored on any selected provider.']);
        }
    }

    /** @return Collection<int, MediaStorageLocation> */
    private function replicateToNewProviders(Magazine $magazine, array $providerIds): Collection
    {
        $createdLocations = new Collection;

        if ($providerIds === []) {
            return $createdLocations;
        }

        $source = $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id);

        if (! $source) {
            throw ValidationException::withMessages([
                'provider_ids' => 'No available PDF copy exists to replicate to additional providers.',
            ]);
        }

        $availableProviderIds = $magazine->storageLocations()
            ->where('status', MediaStorageLocation::STATUS_AVAILABLE)
            ->pluck('storage_provider_id')->all();
        $targetProviders = StorageProvider::query()
            ->where('is_active', true)
            ->whereIn('id', array_diff($providerIds, $availableProviderIds))
            ->orderBy('priority')->get();

        foreach ($targetProviders as $provider) {
            $createdLocations->push($this->mediaStorageService->replicate($source, $provider));
        }

        $desiredAvailable = $magazine->storageLocations()
            ->whereIn('storage_provider_id', $providerIds)
            ->where('status', MediaStorageLocation::STATUS_AVAILABLE)
            ->exists();

        if (! $desiredAvailable) {
            throw ValidationException::withMessages([
                'provider_ids' => 'At least one selected provider must have an available PDF copy.',
            ]);
        }

        return $createdLocations;
    }

    /** @param iterable<int, MediaStorageLocation> $locations */
    private function deleteStorageLocations(iterable $locations): void
    {
        foreach ($locations as $location) {
            if ($this->mediaStorageService->delete($location)) {
                $location->delete();
            }
        }
    }

    /** @param Collection<int, MediaStorageLocation> $locations */
    private function cleanupNewStorageLocations(Collection $locations): void
    {
        $this->deleteStorageLocations($locations);
    }

    private function storeCover(UploadedFile $cover): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = $this->availableCoverFilename($cover, $directory);
        $cover->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function availableCoverFilename(UploadedFile $cover, string $directory): string
    {
        $safeBaseName = Str::of(pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME))
            ->ascii()->replaceMatches('/[^A-Za-z0-9]+/', '-')->trim('-')->toString();
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'magazine-cover';
        $extension = strtolower($cover->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    private function deleteManagedCover(?string $path): void
    {
        if (! is_string($path)) {
            return;
        }

        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        if ($filename === '' || $filename === '.' || $filename === '..'
            || $normalizedPath !== self::IMAGE_DIRECTORY.'/'.$filename) {
            return;
        }

        $physicalPath = public_path($normalizedPath);

        if (File::exists($physicalPath)) {
            File::delete($physicalPath);
        }
    }
}
