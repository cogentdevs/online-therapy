<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class GeneralSettingController extends Controller
{
    /** @var list<string> */
    private const IMAGE_FIELDS = ['logo', 'footer_logo', 'favicon', 'play_store_icon', 'app_store_icon'];

    private const IMAGE_DIRECTORY = 'images/backend-images/logo';

    public function edit(): View
    {
        $generalSetting = GeneralSetting::current();
        $imagePaths = [];

        foreach (self::IMAGE_FIELDS as $field) {
            $path = $generalSetting->getAttribute($field);
            $imagePaths[$field] = is_string($path) && $this->isManagedImagePath($path) && File::isFile(public_path($path))
                ? $path
                : null;
        }

        return view('admin.general-setting.edit-general-setting', [
            'generalSetting' => $generalSetting,
            'imagePaths' => $imagePaths,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2048'],
            'google_ads_client_id' => ['nullable', 'string', 'max:32', 'regex:/^ca-pub-[0-9]{16}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'footer_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'footer_text' => ['nullable', 'string'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'contact_1' => ['nullable', 'string', 'max:255'],
            'contact_2' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'facebook' => ['nullable', 'url', 'max:2048'],
            'instagram' => ['nullable', 'url', 'max:2048'],
            'youtube' => ['nullable', 'url', 'max:2048'],
            'linkedin' => ['nullable', 'url', 'max:2048'],
            'tiktok' => ['nullable', 'url', 'max:2048'],
            'x' => ['nullable', 'url', 'max:2048'],
            'app_section_heading' => ['nullable', 'string', 'max:255'],
            'app_section_text' => ['nullable', 'string'],
            'play_store_icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'play_store_link' => ['nullable', 'url', 'max:2048'],
            'app_store_icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'app_store_link' => ['nullable', 'url', 'max:2048'],
            'default_language_id' => ['nullable', 'integer', 'min:1'],
            'max_devices_per_user' => ['nullable', 'integer', 'min:1'],
            'max_concurrent_sessions' => ['nullable', 'integer', 'min:1'],
            'cookie_consent_enabled' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $generalSetting = GeneralSetting::current();
        $settingsData = Arr::except($validated, self::IMAGE_FIELDS);
        $newImagePaths = [];
        $previousImagePaths = [];

        try {
            foreach (self::IMAGE_FIELDS as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $storedPath = $this->storeImage($request->file($field));

                $newImagePaths[] = $storedPath;
                $previousPath = $generalSetting->getAttribute($field);

                if (is_string($previousPath) && $previousPath !== '') {
                    $previousImagePaths[] = $previousPath;
                }

                $settingsData[$field] = $storedPath;
            }

            DB::transaction(function () use ($generalSetting, $settingsData): void {
                $generalSetting->fill($settingsData)->save();
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImages($newImagePaths);

            throw $exception;
        }

        $this->deleteManagedImages($previousImagePaths);

        return redirect()
            ->route('admin.general-setting.edit')
            ->with('status', 'General settings updated successfully.');
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
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'image';
        $extension = strtolower($image->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    /** @param list<string> $paths */
    private function deleteManagedImages(array $paths): void
    {
        foreach ($paths as $path) {
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
