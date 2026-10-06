<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
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

class BannerController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/banner';

    public function index(): View
    {
        $banners = Banner::query()->with('creator.roles')->latest()->get();

        return view('admin.banner.view-banner', [
            'banners' => $banners,
            'languageNames' => Language::query()
                ->whereIn('code', $banners->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.banner.add-banner', [
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newImagePaths = [];

        try {
            $newImagePaths['image'] = $this->storeImage($request->file('image'));

            if ($validated['type'] === 'side-by-side') {
                $newImagePaths['image_2'] = $this->storeImage($request->file('image_2'));
            }

            DB::transaction(function () use ($validated, $newImagePaths): void {
                Banner::query()->create([
                    ...Arr::except($validated, ['image', 'image_2']),
                    ...$newImagePaths,
                    'image_2' => $newImagePaths['image_2'] ?? null,
                    'isActive' => true,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImages(array_values($newImagePaths));

            throw $exception;
        }

        return redirect()
            ->route('admin.banner.index')
            ->with('status', 'Banner added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.banner.edit-banner', [
            'banner' => Banner::query()->findOrFail($id),
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function update(UpdateBannerRequest $request, int $id): RedirectResponse
    {
        $banner = Banner::query()->findOrFail($id);
        $validated = $request->validated();
        $bannerData = Arr::except($validated, ['image', 'image_2']);
        $newImagePaths = [];
        $previousImagePaths = [];

        try {
            if ($request->hasFile('image')) {
                $newImagePaths['image'] = $this->storeImage($request->file('image'));
                $bannerData['image'] = $newImagePaths['image'];
                $previousImagePaths[] = $banner->image;
            }

            if ($validated['type'] === 'side-by-side' && $request->hasFile('image_2')) {
                $newImagePaths['image_2'] = $this->storeImage($request->file('image_2'));
                $bannerData['image_2'] = $newImagePaths['image_2'];
                $previousImagePaths[] = $banner->image_2;
            }

            if ($validated['type'] === 'full') {
                $bannerData['image_2'] = null;
                $previousImagePaths[] = $banner->image_2;
            }

            DB::transaction(function () use ($banner, $bannerData): void {
                $banner->update($bannerData);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImages(array_values($newImagePaths));

            throw $exception;
        }

        $this->deleteManagedImages($previousImagePaths);

        return redirect()
            ->route('admin.banner.index')
            ->with('status', 'Banner updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $banner = Banner::query()->findOrFail($id);
        $imagePaths = [$banner->image, $banner->image_2];

        DB::transaction(function () use ($banner): void {
            $banner->delete();
        });

        $this->deleteManagedImages($imagePaths);

        return redirect()
            ->route('admin.banner.index')
            ->with('status', 'Banner deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'code']);
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);

        $filename = $this->availableFilename($image, $directory);
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function availableFilename(UploadedFile $image, string $directory): string
    {
        $originalBaseName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::of($originalBaseName)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9]+/', '-')
            ->trim('-')
            ->toString();
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'banner';
        $extension = strtolower($image->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    /** @param array<int, string|null> $paths */
    private function deleteManagedImages(array $paths): void
    {
        foreach (array_unique(array_filter($paths, 'is_string')) as $path) {
            if ($this->isManagedImagePath($path)) {
                File::delete(public_path($path));
            }
        }
    }

    private function isManagedImagePath(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        return $filename !== ''
            && $filename !== '.'
            && $filename !== '..'
            && $normalizedPath === self::IMAGE_DIRECTORY.'/'.$filename;
    }
}
