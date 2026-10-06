<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Http\Requests\Admin\UpdateVideoRequest;
use App\Models\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class VideoController extends Controller
{
    private const THUMBNAIL_DIRECTORY = 'backend-images/video';

    public function index(): View
    {
        return view('admin.video.view-video', [
            'videos' => Video::query()->with('creator.roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.video.add-video');
    }

    public function store(StoreVideoRequest $request): RedirectResponse
    {
        $thumbnailPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $this->storeThumbnail($request->file('thumbnail'));
            }

            $validated = Arr::except($request->validated(), ['thumbnail']);
            DB::transaction(fn () => Video::query()->create([
                ...$validated,
                'thumbnail' => $thumbnailPath,
                'status' => Video::STATUS_DRAFT,
            ]));
        } catch (Throwable $exception) {
            $this->deleteManagedThumbnail($thumbnailPath);

            throw $exception;
        }

        return redirect()->route('admin.video.index')->with('status', 'Video added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.video.edit-video', [
            'video' => Video::query()->findOrFail($id),
        ]);
    }

    public function update(UpdateVideoRequest $request, int $id): RedirectResponse
    {
        $video = Video::query()->findOrFail($id);
        $videoData = Arr::except($request->validated(), ['thumbnail']);
        $newThumbnailPath = null;
        $previousThumbnailPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $newThumbnailPath = $this->storeThumbnail($request->file('thumbnail'));
                $videoData['thumbnail'] = $newThumbnailPath;
                $previousThumbnailPath = $video->thumbnail;
            }

            DB::transaction(fn () => $video->update($videoData));
        } catch (Throwable $exception) {
            $this->deleteManagedThumbnail($newThumbnailPath);

            throw $exception;
        }

        $this->deleteManagedThumbnail($previousThumbnailPath);

        return redirect()->route('admin.video.index')->with('status', 'Video updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $video = Video::query()->findOrFail($id);
        $thumbnailPath = $video->thumbnail;

        DB::transaction(fn () => $video->delete());
        $this->deleteManagedThumbnail($thumbnailPath);

        return redirect()->route('admin.video.index')->with('status', 'Video deleted successfully.');
    }

    public function publish(int $id): RedirectResponse
    {
        $video = Video::query()->findOrFail($id);

        if ($video->status === Video::STATUS_PUBLISHED) {
            return back()->with('error', 'This Video is already published.');
        }

        if (! $video->is_active) {
            return back()->with('error', 'Activate the Video before publishing it.');
        }

        if (blank($video->title) || blank($video->video_link)) {
            return back()->with('error', 'A title and Video link are required before publishing.');
        }

        $video->forceFill(['published_by' => auth()->id()]);
        $video->update([
            'status' => Video::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('status', 'Video published successfully.');
    }

    private function storeThumbnail(UploadedFile $thumbnail): string
    {
        $directory = public_path(self::THUMBNAIL_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = Str::ulid().'.'.Str::lower($thumbnail->extension());
        $thumbnail->move($directory, $filename);

        return self::THUMBNAIL_DIRECTORY.'/'.$filename;
    }

    private function deleteManagedThumbnail(?string $path): void
    {
        if (! is_string($path)) {
            return;
        }

        $normalizedPath = str_replace('\\', '/', $path);
        $filename = basename($normalizedPath);

        if ($filename !== '' && $filename !== '.' && $filename !== '..' && $normalizedPath === self::THUMBNAIL_DIRECTORY.'/'.$filename) {
            File::delete(public_path($normalizedPath));
        }
    }
}
