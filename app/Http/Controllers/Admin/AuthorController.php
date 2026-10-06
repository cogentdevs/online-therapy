<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAuthorRequest;
use App\Http\Requests\Admin\UpdateAuthorRequest;
use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class AuthorController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/author';

    private const DEFAULT_PICTURE = 'images/backend-images/author/author.png';

    public function index(): View
    {
        return view('admin.author.view-author', [
            'authors' => Author::query()->with('creator.roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.author.add-author', [
            'fieldLabels' => AuthorGeneralSetting::FIELD_LABELS,
            'visibilityValues' => $this->globalVisibilityValues(),
        ]);
    }

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $visibilityValues = Arr::pull($validated, 'visibility', []);
        $picturePath = self::DEFAULT_PICTURE;

        try {
            if ($request->hasFile('picture')) {
                $picturePath = $this->storePicture($request->file('picture'));
                $validated['picture'] = $picturePath;
            }

            DB::transaction(function () use ($validated, $visibilityValues): void {
                $author = Author::query()->create([
                    ...Arr::except($validated, ['picture']),
                    'picture' => $validated['picture'] ?? self::DEFAULT_PICTURE,
                    'isActive' => true,
                ]);

                $this->syncVisibilities($author, $visibilityValues, $this->globalVisibilityValues());
            });
        } catch (Throwable $exception) {
            if ($picturePath !== self::DEFAULT_PICTURE) {
                $this->deleteManagedPicture($picturePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.author.index')
            ->with('status', 'Author added successfully.');
    }

    public function edit(int $id): View
    {
        $author = Author::query()->with('visibilities')->findOrFail($id);
        $visibilityValues = $this->globalVisibilityValues();

        foreach ($author->visibilities as $visibility) {
            if (array_key_exists($visibility->column_name, $visibilityValues)) {
                $visibilityValues[$visibility->column_name] = (bool) $visibility->isView;
            }
        }

        return view('admin.author.edit-author', [
            'author' => $author,
            'fieldLabels' => AuthorGeneralSetting::FIELD_LABELS,
            'visibilityValues' => $visibilityValues,
        ]);
    }

    public function update(UpdateAuthorRequest $request, int $id): RedirectResponse
    {
        $author = Author::query()->findOrFail($id);
        $validated = $request->validated();
        $visibilityValues = Arr::pull($validated, 'visibility', []);
        $newPicturePath = null;
        $previousPicturePath = $author->picture;

        try {
            if ($request->hasFile('picture')) {
                $newPicturePath = $this->storePicture($request->file('picture'));
                $validated['picture'] = $newPicturePath;
            }

            DB::transaction(function () use ($author, $validated, $visibilityValues): void {
                $author->update($validated);
                $this->syncVisibilities($author, $visibilityValues, $this->globalVisibilityValues());
            });
        } catch (Throwable $exception) {
            if (is_string($newPicturePath)) {
                $this->deleteManagedPicture($newPicturePath);
            }

            throw $exception;
        }

        if (is_string($newPicturePath) && is_string($previousPicturePath)) {
            $this->deleteManagedPicture($previousPicturePath);
        }

        return redirect()
            ->route('admin.author.index')
            ->with('status', 'Author updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $author = Author::query()->findOrFail($id);
        $picturePath = $author->picture;

        DB::transaction(function () use ($author): void {
            $author->delete();
        });

        if (is_string($picturePath)) {
            $this->deleteManagedPicture($picturePath);
        }

        return redirect()
            ->route('admin.author.index')
            ->with('status', 'Author deleted successfully.');
    }

    /** @return array<string, bool> */
    private function globalVisibilityValues(): array
    {
        $storedSettings = AuthorGeneralSetting::query()
            ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
            ->get(['column_name', 'isView'])
            ->keyBy('column_name');

        $visibilityValues = [];

        foreach (AuthorGeneralSetting::DEFAULT_VISIBILITY as $field => $defaultValue) {
            $visibilityValues[$field] = (bool) ($storedSettings->get($field)?->isView ?? $defaultValue);
        }

        return $visibilityValues;
    }

    /**
     * @param  array<string, bool|int|string>  $submittedValues
     * @param  array<string, bool>  $defaultValues
     */
    private function syncVisibilities(Author $author, array $submittedValues, array $defaultValues): void
    {
        foreach (AuthorGeneralSetting::configurableFields() as $field) {
            $author->visibilities()->updateOrCreate(
                ['column_name' => $field],
                ['isView' => (bool) ($submittedValues[$field] ?? $defaultValues[$field])],
            );
        }
    }

    private function storePicture(UploadedFile $picture): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);

        $filename = $this->availableFilename($picture, $directory);
        $picture->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function availableFilename(UploadedFile $picture, string $directory): string
    {
        $originalBaseName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::of($originalBaseName)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9]+/', '-')
            ->trim('-')
            ->toString();
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'author';
        $extension = strtolower($picture->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    private function deleteManagedPicture(string $path): void
    {
        $normalizedPath = str_replace('\\', '/', $path);

        if ($normalizedPath === self::DEFAULT_PICTURE || ! $this->isManagedPicturePath($normalizedPath)) {
            return;
        }

        $physicalPath = public_path($normalizedPath);

        if (File::exists($physicalPath)) {
            File::delete($physicalPath);
        }
    }

    private function isManagedPicturePath(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        return $filename !== ''
            && $filename !== '.'
            && $filename !== '..'
            && $normalizedPath === self::IMAGE_DIRECTORY.'/'.$filename;
    }
}
