<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInfoPageRequest;
use App\Models\InfoPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class InfoPageController extends Controller
{
    public function edit(string $page): View
    {
        $definition = $this->definition($page);
        Gate::authorize($definition['permission'].'.view');
        $language = $this->language(config('content_language.code'));
        $infoPage = InfoPage::query()->forPage($definition['key'], $language)->firstOrNew([
            'language' => $language,
            'page' => $definition['key'],
        ]);
        $infoPage->title = $definition['titles'][$language];

        return view('admin.info-page.edit-info-page', [
            'definition' => $definition,
            'infoPage' => $infoPage,
            'language' => $language,
            'page' => $page,
        ]);
    }

    public function update(UpdateInfoPageRequest $request, string $page): RedirectResponse
    {
        $definition = $this->definition($page);
        Gate::authorize($definition['permission'].'.edit');
        $validated = $request->validated();
        $language = $this->language(config('content_language.code'));

        InfoPage::query()->updateOrCreate(
            ['language' => $language, 'page' => $definition['key']],
            ['title' => $definition['titles'][$language], 'description' => $this->sanitizeDescription($validated['description'] ?? null)],
        );

        return redirect()->route('admin.info-pages.edit', ['page' => $page, 'language' => $language])
            ->with('status', $definition['titles']['en'].' updated successfully.');
    }

    /** @return array{key: string, permission: string, titles: array<string, string>} */
    private function definition(string $page): array
    {
        $definition = config('info_pages.pages.'.$page);
        abort_unless(is_array($definition), 404);

        return $definition;
    }

    private function language(mixed $language): string
    {
        abort_unless(
            is_string($language)
            && in_array($language, config('info_pages.languages'), true)
            && Language::query()->where('code', $language)->where('is_active', true)->exists(),
            404,
        );

        return $language;
    }

    private function sanitizeDescription(?string $description): ?string
    {
        if (blank($description)) {
            return null;
        }

        $withoutExecutableBlocks = preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/isu', '', $description) ?? '';
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><table><thead><tbody><tfoot><tr><th><td><hr>';
        $sanitized = strip_tags($withoutExecutableBlocks, $allowedTags);
        $sanitized = preg_replace('/\s+(on\w+|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $sanitized) ?? '';
        $sanitized = preg_replace('/\s+href\s*=\s*(["\'])\s*(?:javascript|data):.*?\1/iu', '', $sanitized) ?? '';

        return trim($sanitized) ?: null;
    }
}
