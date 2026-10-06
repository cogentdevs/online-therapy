<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAboutRequest;
use App\Http\Requests\Admin\UpdateAboutRequest;
use App\Models\About;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class AboutController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/about';

    public function index(): View
    {
        $abouts = About::query()->with('creator.roles')->latest()->get();

        return view('admin.about.view-about', [
            'abouts' => $abouts,
            'languageNames' => Language::query()->whereIn('code', $abouts->pluck('language')->filter())->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.about.add-about', ['languages' => $this->activeLanguages(), 'sectionConditions' => About::SECTION_LABELS]);
    }

    public function store(StoreAboutRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newImagePaths = [];

        try {
            foreach (['image', 'image_2'] as $field) {
                if ($request->hasFile($field)) {
                    $newImagePaths[$field] = $this->storeImage($request->file($field));
                }
            }
            DB::transaction(fn () => About::query()->create([...$this->normalizedData($validated), ...$newImagePaths]));
        } catch (Throwable $exception) {
            $this->deleteManagedImages(array_values($newImagePaths));
            throw $exception;
        }

        return redirect()->route('admin.about.index')->with('status', 'About section added successfully.');
    }

    public function show(int $id): View
    {
        $about = About::query()->with(['creator.roles', 'updater.roles'])->findOrFail($id);

        return view('admin.about.view-about-detail', [
            'about' => $about,
            'languageName' => Language::query()->where('code', $about->language)->value('name'),
        ]);
    }

    public function edit(int $id): View
    {
        return view('admin.about.edit-about', ['about' => About::query()->findOrFail($id), 'languages' => $this->activeLanguages(), 'sectionConditions' => About::SECTION_LABELS]);
    }

    public function update(UpdateAboutRequest $request, int $id): RedirectResponse
    {
        $about = About::query()->findOrFail($id);
        $validated = $request->validated();
        $aboutData = $this->normalizedData($validated);
        $newImagePaths = [];
        $previousImagePaths = [];

        try {
            foreach (['image', 'image_2'] as $field) {
                if ($request->hasFile($field)) {
                    $newImagePaths[$field] = $this->storeImage($request->file($field));
                    $aboutData[$field] = $newImagePaths[$field];
                    $previousImagePaths[] = $about->getAttribute($field);
                }
            }
            foreach ($this->unusedImageFields((int) $validated['section_condition']) as $field) {
                $aboutData[$field] = null;
                $previousImagePaths[] = $about->getAttribute($field);
            }
            DB::transaction(fn () => $about->update($aboutData));
        } catch (Throwable $exception) {
            $this->deleteManagedImages(array_values($newImagePaths));
            throw $exception;
        }

        $this->deleteManagedImages($previousImagePaths);

        return redirect()->route('admin.about.index')->with('status', 'About section updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $about = About::query()->findOrFail($id);
        $imagePaths = [$about->image, $about->image_2];
        DB::transaction(fn () => $about->delete());
        $this->deleteManagedImages($imagePaths);

        return redirect()->route('admin.about.index')->with('status', 'About section deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']);
    }

    /** @param array<string, mixed> $validated @return array<string, mixed> */
    private function normalizedData(array $validated): array
    {
        $condition = (int) $validated['section_condition'];
        $data = Arr::except($validated, ['image', 'image_2']);

        if (! in_array($condition, [1, 3, 4, 5], true)) {
            $data['title'] = null;
            $data['description'] = null;
        }
        if ($condition !== 5) {
            $data['title_2'] = null;
            $data['description_2'] = null;
        }

        return $data;
    }

    /** @return array<int, string> */
    private function unusedImageFields(int $condition): array
    {
        return match ($condition) {
            1, 5 => ['image', 'image_2'],
            2, 3, 4 => ['image_2'],
            default => [],
        };
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = Str::ulid().'.'.Str::lower($image->extension());
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    /** @param array<int, string|null> $paths */
    private function deleteManagedImages(array $paths): void
    {
        foreach (array_unique(array_filter($paths, 'is_string')) as $path) {
            $normalizedPath = str_replace('\\', '/', $path);
            $filename = basename($normalizedPath);

            if ($filename !== '' && $filename !== '.' && $filename !== '..' && $normalizedPath === self::IMAGE_DIRECTORY.'/'.$filename) {
                File::delete(public_path($normalizedPath));
            }
        }
    }
}
