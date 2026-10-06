<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagsRequest;
use App\Http\Requests\Admin\UpdateTagsRequest;
use App\Models\Language;
use App\Models\Tags;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    public function index(): View
    {
        $tags = Tags::query()->with('creator.roles')->latest()->get();

        return view('admin.tags.view-tags', [
            'tags' => $tags,
            'languageNames' => Language::query()
                ->whereIn('code', $tags->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.tags.add-tags', ['languages' => $this->activeLanguages()]);
    }

    public function store(StoreTagsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $names = collect($validated['names'])
            ->filter(fn ($name): bool => is_string($name) && trim($name) !== '')
            ->map(fn (string $name): string => trim($name));

        DB::transaction(function () use ($validated, $names): void {
            foreach ($names as $name) {
                Tags::query()->create([
                    'language' => $validated['language'],
                    'name' => $name,
                    'isActive' => true,
                ]);
            }
        });

        return redirect()->route('admin.tags.index')->with('status', 'Tags added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.tags.edit-tags', [
            'tag' => Tags::query()->findOrFail($id),
            'languages' => $this->activeLanguages(),
        ]);
    }

    public function update(UpdateTagsRequest $request, int $id): RedirectResponse
    {
        Tags::query()->findOrFail($id)->update($request->validated());

        return redirect()->route('admin.tags.index')->with('status', 'Tag updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Tags::query()->findOrFail($id)->delete();

        return redirect()->route('admin.tags.index')->with('status', 'Tag deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']);
    }
}
