<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class ServiceController extends Controller
{
    private const IMAGE_DIRECTORY = 'backend-images/services';

    public function index(): View
    {
        return view('admin.services.view-services', [
            'services' => Service::query()->with('creator.roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.add-service');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $imagePath = null;

        try {
            $imagePath = $this->storeImage($request->file('image'));
            $validated = Arr::except($request->validated(), ['image']);

            DB::transaction(fn () => Service::query()->create([...$validated, 'image' => $imagePath]));
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);

            throw $exception;
        }

        return redirect()->route('admin.services.index')->with('status', 'Service added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.services.edit-service', [
            'service' => Service::query()->findOrFail($id),
        ]);
    }

    public function update(UpdateServiceRequest $request, int $id): RedirectResponse
    {
        $service = Service::query()->findOrFail($id);
        $serviceData = Arr::except($request->validated(), ['image']);
        $newImagePath = null;
        $previousImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
                $serviceData['image'] = $newImagePath;
                $previousImagePath = $service->image;
            }

            DB::transaction(fn () => $service->update($serviceData));
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        $this->deleteManagedImage($previousImagePath);

        return redirect()->route('admin.services.index')->with('status', 'Service updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $service = Service::query()->findOrFail($id);
        $imagePath = $service->image;

        DB::transaction(fn () => $service->delete());
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.services.index')->with('status', 'Service deleted successfully.');
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
