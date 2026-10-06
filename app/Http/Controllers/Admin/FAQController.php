<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class FAQController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->with(['category:id,name', 'creator.roles'])->latest()->get();

        return view('admin.faq.view-faq', [
            'faqs' => $faqs,
            'languageNames' => Language::query()
                ->whereIn('code', $faqs->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.faq.add-faq', [
            'languages' => $this->activeLanguages(),
            'categories' => $this->activeCategories(),
        ]);
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Faq::query()->create([
            ...$request->validated(),
            'isActive' => true,
        ]);

        return redirect()
            ->route('admin.faq.index')
            ->with('status', 'FAQ added successfully.');
    }

    public function edit(int $id): View
    {
        return view('admin.faq.edit-faq', [
            'faq' => Faq::query()->findOrFail($id),
            'languages' => $this->activeLanguages(),
            'categories' => $this->activeCategories(),
        ]);
    }

    public function update(UpdateFaqRequest $request, int $id): RedirectResponse
    {
        Faq::query()->findOrFail($id)->update($request->validated());

        return redirect()
            ->route('admin.faq.index')
            ->with('status', 'FAQ updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Faq::query()->findOrFail($id)->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('status', 'FAQ deleted successfully.');
    }

    /** @return Collection<int, Language> */
    private function activeLanguages(): Collection
    {
        return Language::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'code']);
    }

    /** @return Collection<int, FaqCategory> */
    private function activeCategories(): Collection
    {
        return FaqCategory::query()->where('isActive', true)->orderBy('name')->get(['id', 'language', 'name']);
    }
}
