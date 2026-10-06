<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMetaTagRequest;
use App\Http\Requests\Admin\UpdateMetaTagRequest;
use App\Models\Language;
use App\Models\MetaTag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MetaTagController extends Controller
{
    public function index(): View
    {
        $metaTags = MetaTag::query()->with('creator.roles')->latest()->get();

        return view('admin.meta-tags.view-meta-tags', [
            'metaTags' => $metaTags,
            'languageNames' => Language::query()
                ->whereIn('code', $metaTags->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.meta-tags.add-meta-tags', [
            'corePages' => MetaTag::CORE_PAGES,
        ]);
    }

    public function store(StoreMetaTagRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        MetaTag::query()->create([
            ...$validated,
            'table_name' => null,
            'table_id' => null,
            'slug_url' => MetaTag::corePageSlug($validated['title']),
            'canonical_url' => null,
        ]);

        return redirect()->route('admin.meta-tags.index')->with('status', 'Meta tags added successfully.');
    }

    public function edit(int $id): View
    {
        $metaTag = MetaTag::query()->findOrFail($id);

        return view('admin.meta-tags.edit-meta-tags', [
            'metaTag' => $metaTag,
            'moduleLabel' => $metaTag->moduleLabel(),
            'generatedSlug' => $metaTag->table_name === null
                ? MetaTag::corePageSlug($metaTag->title ?? '')
                : MetaTag::moduleSlug($metaTag->title),
        ]);
    }

    public function update(UpdateMetaTagRequest $request, int $id): RedirectResponse
    {
        $metaTag = MetaTag::query()->findOrFail($id);
        $validated = $request->validated();
        $validated['slug_url'] = $metaTag->table_name === null
            ? MetaTag::corePageSlug($metaTag->title ?? '')
            : MetaTag::moduleSlug($metaTag->title);
        $metaTag->update($validated);

        return redirect()->route('admin.meta-tags.index')->with('status', 'Meta tags updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        MetaTag::query()->findOrFail($id)->delete();

        return redirect()->route('admin.meta-tags.index')->with('status', 'Meta tags deleted successfully.');
    }
}
