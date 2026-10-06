<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Language;
use App\Models\MetaTag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class CategoryController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/categories';

    public function index(): View
    {
        $categories = Category::query()->with('creator.roles')->latest()->get();

        return view('admin.categories.view-categories', [
            'categories' => $categories,
            'languageNames' => Language::query()
                ->whereIn('code', $categories->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.add-categories', ['languages' => $this->activeLanguages()]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newImagePaths = [];

        try {
            DB::transaction(function () use ($request, $validated, &$newImagePaths): void {
                foreach ($validated['entries'] as $index => $entry) {
                    $name = trim((string) ($entry['name'] ?? ''));
                    $image = $request->file("entries.$index.image");

                    if ($name === '' && ! ($image instanceof UploadedFile)) {
                        continue;
                    }

                    $imagePath = null;

                    if ($image instanceof UploadedFile) {
                        $imagePath = $this->storeImage($image);
                        $newImagePaths[] = $imagePath;
                    }

                    $category = Category::query()->create([
                        'language' => $validated['language'],
                        'name' => $name !== '' ? $name : null,
                        'image' => $imagePath,
                        'isActive' => true,
                    ]);

                    MetaTag::query()->create([
                        'table_name' => $category->getTable(),
                        'table_id' => $category->id,
                        'language' => $category->language,
                        'title' => $category->name,
                        'keywords' => $entry['keywords'] ?? null,
                        'description' => $entry['meta_description'] ?? null,
                        'slug_url' => MetaTag::moduleSlug($category->name),
                        'canonical_url' => null,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($newImagePaths as $newImagePath) {
                $this->deleteManagedImage($newImagePath);
            }

            throw $exception;
        }

        return redirect()->route('admin.categories.index')->with('status', 'Categories added successfully.');
    }

    public function edit(int $id): View
    {
        $category = Category::query()->findOrFail($id);

        return view('admin.categories.edit-categories', [
            'category' => $category,
            'languages' => $this->activeLanguages(),
            'metaTag' => $this->categoryMetaTag($category),
            'generatedSlug' => MetaTag::moduleSlug($category->name),
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $category = Category::query()->findOrFail($id);
        $validated = $request->validated();
        $categoryData = Arr::except($validated, [
            'image',
            'keywords',
            'meta_description',
            'canonical_url',
        ]);
        $metaTag = $this->categoryMetaTag($category);
        $newImagePath = null;
        $previousImagePath = $category->image;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
                $categoryData['image'] = $newImagePath;
            }

            DB::transaction(function () use ($category, $categoryData, $metaTag, $validated): void {
                $category->update($categoryData);

                $metaValues = [
                    'language' => $category->language,
                    'title' => $category->name,
                    'keywords' => $validated['keywords'] ?? null,
                    'description' => $validated['meta_description'] ?? null,
                    'slug_url' => MetaTag::moduleSlug($category->name),
                    'canonical_url' => array_key_exists('canonical_url', $validated)
                        ? $validated['canonical_url']
                        : $metaTag?->canonical_url,
                ];

                if ($metaTag !== null) {
                    $metaTag->update($metaValues);

                    return;
                }

                MetaTag::query()->updateOrCreate([
                    'table_name' => $category->getTable(),
                    'table_id' => $category->id,
                    'language' => $category->language,
                ], $metaValues);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        if ($newImagePath !== null) {
            $this->deleteManagedImage($previousImagePath);
        }

        return redirect()->route('admin.categories.index')->with('status', 'Category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::query()->findOrFail($id);
        $imagePath = $category->image;

        DB::transaction(function () use ($category): void {
            MetaTag::query()
                ->where('table_name', $category->getTable())
                ->where('table_id', $category->id)
                ->delete();
            $category->delete();
        });
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']);
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
        $safeBaseName = Str::of(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9]+/', '-')
            ->trim('-')
            ->toString();
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'category';
        $extension = strtolower($image->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    private function categoryMetaTag(Category $category): ?MetaTag
    {
        return MetaTag::query()
            ->where('table_name', $category->getTable())
            ->where('table_id', $category->id)
            ->where('language', $category->language)
            ->first();
    }

    private function deleteManagedImage(?string $path): void
    {
        if ($path === null) {
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
