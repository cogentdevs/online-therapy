<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Http\Requests\Admin\UpdateSliderRequest;
use App\Models\Language;
use App\Models\Slider;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class SliderController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/slider';

    public function index(): View
    {
        $sliders = Slider::query()->with('creator.roles')->latest()->get();

        return view('admin.slider.view-slider', [
            'sliders' => $sliders,
            'languageNames' => Language::query()
                ->whereIn('code', $sliders->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.slider.add-slider', [
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function store(StoreSliderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $this->storeImage($request->file('image'));

        try {
            DB::transaction(function () use ($validated, $imagePath): void {
                Slider::query()->create([
                    ...Arr::except($validated, ['image']),
                    'image' => $imagePath,
                    'isActive' => true,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.slider.index')
            ->with('status', 'Slider added successfully.');
    }

    public function edit(int $id): View
    {
        $slider = Slider::query()->findOrFail($id);

        return view('admin.slider.edit-slider', [
            'slider' => $slider,
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function update(UpdateSliderRequest $request, int $id): RedirectResponse
    {
        $slider = Slider::query()->findOrFail($id);
        $validated = $request->validated();
        $sliderData = Arr::except($validated, ['image']);
        $newImagePath = null;
        $previousImagePath = $slider->image;

        if ($request->hasFile('image')) {
            $newImagePath = $this->storeImage($request->file('image'));
            $sliderData['image'] = $newImagePath;
        }

        try {
            DB::transaction(function () use ($slider, $sliderData): void {
                $slider->update($sliderData);
            });
        } catch (Throwable $exception) {
            if (is_string($newImagePath)) {
                $this->deleteManagedImage($newImagePath);
            }

            throw $exception;
        }

        if (is_string($newImagePath) && is_string($previousImagePath)) {
            $this->deleteManagedImage($previousImagePath);
        }

        return redirect()
            ->route('admin.slider.index')
            ->with('status', 'Slider updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $slider = Slider::query()->findOrFail($id);
        $imagePath = $slider->image;

        DB::transaction(function () use ($slider): void {
            $slider->delete();
        });

        if (is_string($imagePath)) {
            $this->deleteManagedImage($imagePath);
        }

        return redirect()
            ->route('admin.slider.index')
            ->with('status', 'Slider deleted successfully.');
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
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'slider';
        $extension = strtolower($image->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    private function deleteManagedImage(string $path): void
    {
        if (! $this->isManagedImagePath($path)) {
            return;
        }

        File::delete(public_path($path));
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
