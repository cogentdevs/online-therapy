<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
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

class CourseController extends Controller
{
    private const IMAGE_DIRECTORY = 'backend-images/course';

    public function __construct(private readonly AdminContentOwnershipService $adminContentOwnershipService, private readonly ActivityLogService $activityLogService) {}

    public function index(Request $request): View
    {
        $courses = $this->adminContentOwnershipService->scopeQuery(Course::query(), $this->admin($request))->with('creator.roles')->latest()->get();

        return view('admin.course.view-course', compact('courses'));
    }

    public function create(Request $request): View
    {
        return view('admin.course.add-course', $this->options(null, $this->admin($request)));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $image = null;
        try {
            $image = $this->storeImage($request->file('image'));
            DB::transaction(function () use ($request, $validated, $image): void {
                $course = Course::query()->newModelInstance([...$this->data($validated), 'language' => config('content_language.code'), 'image' => $image, 'isActive' => (bool) $validated['isActive'], 'isFeatured' => (bool) $validated['isFeatured'], 'status' => Course::STATUS_DRAFT, 'published_at' => null, 'published_by' => null]);
                $course->forceFill(['owner_admin_id' => $this->admin($request)->id]);
                $course->save();
                $this->syncRelated($course, $validated, $this->admin($request));
            });
        } catch (Throwable $exception) {
            $this->deleteImage($image);
            throw $exception;
        }

        return to_route('admin.course.index')->with('status', 'Course added as a draft.');
    }

    public function show(Request $request, int $id): View
    {
        $admin = $this->admin($request);
        $course = $this->owned($id, $admin, ['relatedCourses' => fn ($query) => $this->adminContentOwnershipService->scopeQuery($query, $admin)->select(['courses.id', 'title', 'status'])]);

        return view('admin.course.view-course-detail', compact('course'));
    }

    public function edit(Request $request, int $id): View
    {
        $admin = $this->admin($request);
        $course = $this->owned($id, $admin, ['relatedCourses' => fn ($query) => $this->adminContentOwnershipService->scopeQuery($query, $admin)->select(['courses.id'])]);

        return view('admin.course.edit-course', [...$this->options($course, $admin), 'course' => $course, 'selectedRelatedCourseIds' => $course->relatedCourses->modelKeys()]);
    }

    public function update(UpdateCourseRequest $request, int $id): RedirectResponse
    {
        $admin = $this->admin($request);
        $course = $this->owned($id, $admin);
        $validated = $request->validated();
        $previous = $course->image;
        $image = null;
        try {
            if ($request->hasFile('image')) {
                $image = $this->storeImage($request->file('image'));
            } DB::transaction(function () use ($admin, $course, $validated, $image): void {
                $data = $this->data($validated);
                if ($image !== null) {
                    $data['image'] = $image;
                } $course->update($data);
                $this->syncRelated($course, $validated, $admin);
            });
        } catch (Throwable $exception) {
            $this->deleteImage($image);
            throw $exception;
        } if ($image !== null) {
            $this->deleteImage($previous);
        }

        return to_route('admin.course.index')->with('status', 'Course updated successfully.');
    }

    public function publish(Request $request, int $id): RedirectResponse
    {
        $course = $this->owned($id, $this->admin($request));
        if ($course->status === Course::STATUS_PUBLISHED) {
            return back()->with('error', 'This Course is already published.');
        } if (! $course->isActive) {
            return back()->with('error', 'Activate the Course before publishing it.');
        } if (blank($course->title) || trim(html_entity_decode(strip_tags($course->description ?? ''))) === '') {
            return back()->with('error', 'A title and Course description are required before publishing.');
        } $course->forceFill(['published_by' => auth()->id()]);
        $course->update(['status' => Course::STATUS_PUBLISHED, 'published_at' => now()]);

        return back()->with('status', 'Course published successfully.');
    }

    public function removeRelatedCourse(Request $request, int $course, int $relatedCourse): RedirectResponse
    {
        $admin = $this->admin($request);
        $current = $this->owned($course, $admin);
        $this->owned($relatedCourse, $admin);
        abort_unless($current->relatedCourses()->whereKey($relatedCourse)->exists(), 404, 'The requested related Course mapping does not exist.');
        $current->relatedCourses()->detach($relatedCourse);
        $this->activityLogService->log('courses', 'related_removed', $current, "Removed related Course #{$relatedCourse} from \"{$current->title}\".", ['related_course_id' => $relatedCourse], null);

        return back()->with('status', 'Related Course removed successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $course = $this->owned($id, $this->admin($request));
        $image = $course->image;
        DB::transaction(fn () => $course->delete());
        $this->deleteImage($image);

        return to_route('admin.course.index')->with('status', 'Course deleted successfully.');
    }

    private function options(?Course $course, User $admin): array
    {
        return ['relatedCourses' => $this->adminContentOwnershipService->scopeQuery(Course::query(), $admin)->where('isActive', true)->where('language', config('content_language.code'))->when($course, fn ($query) => $query->whereKeyNot($course->id))->orderBy('title')->get(['id', 'title', 'status'])];
    }

    /** @param array<string,mixed> $validated */
    private function data(array $validated): array
    {
        return Arr::only($validated, ['title', 'button_label', 'short_description', 'description', 'duration_type', 'duration_value', 'isActive', 'isFeatured']);
    }

    /** @param array<string,mixed> $validated */
    private function syncRelated(Course $course, array $validated, User $admin): void
    {
        $ids = array_map('intval', $validated['related_course_ids'] ?? []);
        if (in_array($course->id, $ids, true)) {
            throw ValidationException::withMessages(['related_course_ids' => 'A Course cannot be related to itself.']);
        } $this->adminContentOwnershipService->assertRecordsAccessible($admin, Course::class, $ids, 'related_course_ids');
        $course->relatedCourses()->sync(array_values(array_unique($ids)));
    }

    /** @param array<int|string,mixed> $with */
    private function owned(int $id, User $admin, array $with = []): Course
    {
        $course = Course::query()->with($with)->findOrFail($id);
        $this->adminContentOwnershipService->authorizeAccess($admin, $course);

        return $course;
    }

    private function admin(Request $request): User
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

    private function deleteImage(?string $path): void
    {
        if (! is_string($path)) {
            return;
        } $path = str_replace('\\', '/', $path);
        $filename = basename($path);
        if ($filename !== '' && $filename !== '.' && $filename !== '..' && $path === self::IMAGE_DIRECTORY.'/'.$filename) {
            File::delete(public_path($path));
        }
    }
}
