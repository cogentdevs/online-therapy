<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqCategoryRequest;
use App\Http\Requests\Admin\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class FaqCategoryController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/faq-category';

    public function index(): View
    {
        $categories = FaqCategory::query()->with('creator.roles')->withCount('faqs')->latest()->get();

        return view('admin.faq-category.view-faq-category', [
            'categories' => $categories,
            'languageNames' => Language::query()->whereIn('code', $categories->pluck('language'))->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.faq-category.add-faq-category', ['languages' => $this->activeLanguages()]);
    }

    public function store(StoreFaqCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $iconPath = null;

        try {
            if ($request->hasFile('icon')) {
                $iconPath = $this->storeIcon($request->file('icon'));
            }

            DB::transaction(fn () => FaqCategory::query()->create([
                ...Arr::except($validated, ['icon']),
                'icon' => $iconPath,
                'isActive' => true,
            ]));
        } catch (Throwable $exception) {
            $this->deleteManagedIcon($iconPath);
            throw $exception;
        }

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ Category added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.faq-category.edit-faq-category', [
            'category' => FaqCategory::query()->findOrFail($id),
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function update(UpdateFaqCategoryRequest $request, int $id): RedirectResponse
    {
        $category = FaqCategory::query()->findOrFail($id);
        $data = Arr::except($request->validated(), ['icon', 'remove_icon']);
        $newIconPath = null;
        $previousIconPath = null;

        try {
            if ($request->hasFile('icon')) {
                $newIconPath = $this->storeIcon($request->file('icon'));
                $data['icon'] = $newIconPath;
                $previousIconPath = $category->icon;
            } elseif ($request->boolean('remove_icon')) {
                $data['icon'] = null;
                $previousIconPath = $category->icon;
            }

            DB::transaction(fn () => $category->update($data));
        } catch (Throwable $exception) {
            $this->deleteManagedIcon($newIconPath);
            throw $exception;
        }

        $this->deleteManagedIcon($previousIconPath);

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ Category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = FaqCategory::query()->withCount('faqs')->findOrFail($id);

        if ($category->faqs_count > 0) {
            return redirect()->route('admin.faq-categories.index')
                ->withErrors(['category' => 'This FAQ Category cannot be deleted because FAQs are attached to it. Move or delete those FAQs first.']);
        }

        try {
            $iconPath = $category->icon;
            DB::transaction(fn () => $category->delete());
        } catch (QueryException) {
            return redirect()->route('admin.faq-categories.index')
                ->withErrors(['category' => 'This FAQ Category cannot be deleted because FAQs are attached to it. Move or delete those FAQs first.']);
        }

        $this->deleteManagedIcon($iconPath);

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ Category deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']);
    }

    private function storeIcon(UploadedFile $icon): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = Str::ulid().'.'.Str::lower($icon->extension());
        $icon->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function deleteManagedIcon(?string $path): void
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
