<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConsultancyRequest;
use App\Http\Requests\Admin\UpdateConsultancyRequest;
use App\Models\Consultancy;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AdminContentOwnershipService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ConsultancyController extends Controller
{
    private const IMAGE_DIRECTORY = 'backend-images/consultancy';

    public function __construct(
        private readonly AdminContentOwnershipService $adminContentOwnershipService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): View
    {
        $consultancies = $this->adminContentOwnershipService
            ->scopeQuery(Consultancy::query(), $this->authenticatedAdmin($request))
            ->with('creator.roles')
            ->latest()
            ->get();

        return view('admin.consultancy.view-consultancy', compact('consultancies'));
    }

    public function create(Request $request): View
    {
        return view('admin.consultancy.add-consultancy', $this->formOptions(null, $this->authenticatedAdmin($request)));
    }

    public function store(StoreConsultancyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newImagePath = null;

        try {
            $newImagePath = $this->storeImage($request->file('image'));

            DB::transaction(function () use ($request, $validated, $newImagePath): void {
                $consultancy = Consultancy::query()->newModelInstance([
                    ...$this->consultancyData($validated),
                    'language' => config('content_language.code'),
                    'image' => $newImagePath,
                    'isActive' => (bool) $validated['isActive'],
                    'isFeatured' => (bool) $validated['isFeatured'],
                    'status' => Consultancy::STATUS_DRAFT,
                    'published_at' => null,
                    'published_by' => null,
                ]);
                $consultancy->forceFill(['owner_admin_id' => $this->authenticatedAdmin($request)->id]);
                $consultancy->save();

                $this->syncRelatedConsultancies($consultancy, $validated, $this->authenticatedAdmin($request));
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        return redirect()->route('admin.consultancy.index')->with('status', 'Consultancy added as a draft.');
    }

    public function show(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $consultancy = $this->findOwnedConsultancy($id, $admin, [
            'relatedConsultancies' => fn ($query) => $this->adminContentOwnershipService
                ->scopeQuery($query, $admin)
                ->select(['consultancies.id', 'title', 'status']),
        ]);

        return view('admin.consultancy.view-consultancy-detail', compact('consultancy'));
    }

    public function edit(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $consultancy = $this->findOwnedConsultancy($id, $admin, [
            'relatedConsultancies' => fn ($query) => $this->adminContentOwnershipService
                ->scopeQuery($query, $admin)
                ->select(['consultancies.id']),
        ]);

        return view('admin.consultancy.edit-consultancy', [
            ...$this->formOptions($consultancy, $admin),
            'consultancy' => $consultancy,
            'selectedRelatedConsultancyIds' => $consultancy->relatedConsultancies->modelKeys(),
        ]);
    }

    public function update(UpdateConsultancyRequest $request, int $id): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $consultancy = $this->findOwnedConsultancy($id, $admin);
        $validated = $request->validated();
        $previousImagePath = $consultancy->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
            }

            DB::transaction(function () use ($admin, $consultancy, $validated, $newImagePath): void {
                $consultancyData = $this->consultancyData($validated);

                if ($newImagePath !== null) {
                    $consultancyData['image'] = $newImagePath;
                }

                $consultancy->update($consultancyData);
                $this->syncRelatedConsultancies($consultancy, $validated, $admin);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        if ($newImagePath !== null) {
            $this->deleteManagedImage($previousImagePath);
        }

        return redirect()->route('admin.consultancy.index')->with('status', 'Consultancy updated successfully.');
    }

    public function publish(Request $request, int $id): RedirectResponse
    {
        $consultancy = $this->findOwnedConsultancy($id, $this->authenticatedAdmin($request));

        if ($consultancy->status === Consultancy::STATUS_PUBLISHED) {
            return back()->with('error', 'This Consultancy is already published.');
        }

        if (! $consultancy->isActive) {
            return back()->with('error', 'Activate the Consultancy before publishing it.');
        }

        $plainDescription = trim(html_entity_decode(strip_tags($consultancy->description ?? '')));

        if (blank($consultancy->title) || $plainDescription === '') {
            return back()->with('error', 'A title and Consultancy description are required before publishing.');
        }

        $consultancy->forceFill(['published_by' => auth()->id()]);
        $consultancy->update([
            'status' => Consultancy::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('status', 'Consultancy published successfully.');
    }

    public function removeRelatedConsultancy(Request $request, int $consultancy, int $relatedConsultancy): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $currentConsultancy = $this->findOwnedConsultancy($consultancy, $admin);
        $this->findOwnedConsultancy($relatedConsultancy, $admin);

        if (! $currentConsultancy->relatedConsultancies()->whereKey($relatedConsultancy)->exists()) {
            abort(404, 'The requested related Consultancy mapping does not exist.');
        }

        $currentConsultancy->relatedConsultancies()->detach($relatedConsultancy);

        $this->activityLogService->log(
            'consultancies',
            'related_removed',
            $currentConsultancy,
            "Removed related Consultancy #{$relatedConsultancy} from \"{$currentConsultancy->title}\".",
            ['related_consultancy_id' => $relatedConsultancy],
            null,
        );

        return back()->with('status', 'Related Consultancy removed successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $consultancy = $this->findOwnedConsultancy($id, $this->authenticatedAdmin($request));
        $imagePath = $consultancy->image;

        DB::transaction(fn () => $consultancy->delete());
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.consultancy.index')->with('status', 'Consultancy deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function formOptions(?Consultancy $consultancy, User $admin): array
    {
        return [
            'relatedConsultancies' => $this->adminContentOwnershipService
                ->scopeQuery(Consultancy::query(), $admin)
                ->where('isActive', true)
                ->where('language', config('content_language.code'))
                ->when($consultancy, fn ($query) => $query->whereKeyNot($consultancy->id))
                ->orderBy('title')
                ->get(['id', 'title', 'status']),
        ];
    }

    /** @param array<string, mixed> $validated */
    private function consultancyData(array $validated): array
    {
        return Arr::only($validated, [
            'title',
            'button_label',
            'short_description',
            'description',
            'duration_type',
            'duration_value',
            'consultancy_medium',
            'isActive',
            'isFeatured',
        ]);
    }

    /** @param array<string, mixed> $validated */
    private function syncRelatedConsultancies(Consultancy $consultancy, array $validated, User $admin): void
    {
        $relatedConsultancyIds = array_map('intval', $validated['related_consultancy_ids'] ?? []);

        if (in_array($consultancy->id, $relatedConsultancyIds, true)) {
            throw ValidationException::withMessages([
                'related_consultancy_ids' => 'A Consultancy cannot be related to itself.',
            ]);
        }

        $this->adminContentOwnershipService->assertRecordsAccessible(
            $admin,
            Consultancy::class,
            $relatedConsultancyIds,
            'related_consultancy_ids',
        );

        $consultancy->relatedConsultancies()->sync(array_values(array_unique($relatedConsultancyIds)));
    }

    /** @param array<int|string, mixed> $with */
    private function findOwnedConsultancy(int $id, User $admin, array $with = []): Consultancy
    {
        $consultancy = Consultancy::query()->with($with)->findOrFail($id);
        $this->adminContentOwnershipService->authorizeAccess($admin, $consultancy);

        return $consultancy;
    }

    private function authenticatedAdmin(Request $request): User
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        return $admin;
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = Str::ulid().'.'.Str::lower($image->extension());
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function deleteManagedImage(?string $path): void
    {
        if (! is_string($path)) {
            return;
        }

        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        if ($filename !== '' && $filename !== '.' && $filename !== '..' && $normalizedPath === self::IMAGE_DIRECTORY.'/'.$filename) {
            File::delete(public_path($normalizedPath));
        }
    }
}
