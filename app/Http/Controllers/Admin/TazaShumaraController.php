<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTazaShumaraArticleRequest;
use App\Http\Requests\Admin\StoreTazaShumaraRequest;
use App\Http\Requests\Admin\UpdateTazaShumaraArticleRequest;
use App\Http\Requests\Admin\UpdateTazaShumaraRequest;
use App\Models\Article;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\TazaShumara;
use App\Models\TazaShumaraArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class TazaShumaraController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/taza-shumara';

    public function index(): View
    {
        $tazaShumaras = TazaShumara::query()
            ->with(['magazine:id,title,issue_number', 'creator.roles'])
            ->withCount('articlePlacements')
            ->latest()
            ->get();

        return view('admin.taza-shumara.index', [
            'tazaShumaras' => $tazaShumaras,
            'languageNames' => Language::query()->pluck('name', 'code'),
        ]);
    }

    public function create(): View
    {
        return view('admin.taza-shumara.create', [
            ...$this->formOptions(),
            'tazaShumara' => null,
            'articlePlacements' => collect(),
        ]);
    }

    public function store(StoreTazaShumaraRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $coverImage = $request->hasFile('cover_image')
            ? $this->storeCover($request->file('cover_image'))
            : null;

        try {
            DB::transaction(function () use ($validated, $coverImage): void {
                $this->deactivateLanguageWhenRequired($validated['language'], (bool) $validated['is_active']);

                $tazaShumara = TazaShumara::query()->create([
                    ...Arr::except($validated, ['cover_image', 'articles']),
                    'cover_image' => $coverImage,
                ]);

                $tazaShumara->articlePlacements()->createMany($validated['articles'] ?? []);
            });
        } catch (Throwable $exception) {
            $this->deleteCover($coverImage);
            throw $exception;
        }

        return redirect()->route('admin.taza-shumara.index')
            ->with('status', 'Taza Shumara created successfully.');
    }

    public function edit(int $tazaShumara): View
    {
        $record = TazaShumara::query()->with([
            'articlePlacements' => fn ($query) => $query->orderBy('position')->orderBy('sort_order')->orderBy('id'),
        ])->findOrFail($tazaShumara);

        return view('admin.taza-shumara.edit', [
            ...$this->formOptions(),
            'tazaShumara' => $record,
            'articlePlacements' => $record->articlePlacements,
        ]);
    }

    public function update(UpdateTazaShumaraRequest $request, int $tazaShumara): RedirectResponse
    {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $validated = $request->validated();
        $oldCoverImage = $record->cover_image;
        $newCoverImage = $request->hasFile('cover_image')
            ? $this->storeCover($request->file('cover_image'))
            : null;
        try {
            DB::transaction(function () use ($record, $validated, $newCoverImage): void {
                $this->deactivateLanguageWhenRequired(
                    $validated['language'],
                    (bool) $validated['is_active'],
                    $record->id,
                );

                $data = Arr::except($validated, ['cover_image', 'articles']);

                if ($newCoverImage !== null) {
                    $data['cover_image'] = $newCoverImage;
                }

                $record->update($data);
                $this->syncArticlePlacements($record, $validated['articles'] ?? []);
            });
        } catch (Throwable $exception) {
            $this->deleteCover($newCoverImage);
            throw $exception;
        }

        if ($newCoverImage !== null) {
            $this->deleteCover($oldCoverImage);
        }

        return redirect()->route('admin.taza-shumara.index')->with('status', 'Taza Shumara updated successfully.');
    }

    public function destroyCover(int $tazaShumara): RedirectResponse
    {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $coverImage = $record->cover_image;

        if ($coverImage !== null) {
            $record->update(['cover_image' => null]);
            $this->deleteCover($coverImage);
        }

        return redirect()->route('admin.taza-shumara.edit', ['tazaShumara' => $record->id])
            ->with('status', 'Taza Shumara custom cover removed successfully.');
    }

    public function manage(int $tazaShumara): View
    {
        $record = TazaShumara::query()
            ->with([
                'magazine:id,title,issue_number,publish_date',
                'articlePlacements' => fn ($query) => $query
                    ->with(['article.categories:id,name'])
                    ->orderByRaw("CASE position WHEN 'top' THEN 1 WHEN 'center' THEN 2 ELSE 3 END")
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->findOrFail($tazaShumara);

        return view('admin.taza-shumara.manage', [
            'tazaShumara' => $record,
            'articles' => $this->availableArticles(),
            'displayWidths' => TazaShumaraArticle::DISPLAY_WIDTHS,
            'positions' => TazaShumaraArticle::POSITIONS,
        ]);
    }

    public function storeArticle(StoreTazaShumaraArticleRequest $request, int $tazaShumara): RedirectResponse
    {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $record->articlePlacements()->create($request->validated());

        return back()->with('status', 'Article placement added successfully.');
    }

    public function editArticle(int $tazaShumara, int $placement): View
    {
        $record = TazaShumara::query()->with('magazine:id,title,issue_number')->findOrFail($tazaShumara);
        $articlePlacement = $record->articlePlacements()->findOrFail($placement);

        return view('admin.taza-shumara.edit-article', [
            'tazaShumara' => $record,
            'articlePlacement' => $articlePlacement,
            'articles' => $this->availableArticles(),
            'displayWidths' => TazaShumaraArticle::DISPLAY_WIDTHS,
            'positions' => TazaShumaraArticle::POSITIONS,
        ]);
    }

    public function updateArticle(
        UpdateTazaShumaraArticleRequest $request,
        int $tazaShumara,
        int $placement,
    ): RedirectResponse {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $record->articlePlacements()->findOrFail($placement)->update($request->validated());

        return redirect()->route('admin.taza-shumara.manage', ['tazaShumara' => $record->id])
            ->with('status', 'Article placement updated successfully.');
    }

    public function destroyArticle(int $tazaShumara, int $placement): RedirectResponse
    {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $record->articlePlacements()->findOrFail($placement)->delete();

        return back()->with('status', 'Article placement deleted successfully.');
    }

    public function destroy(int $tazaShumara): RedirectResponse
    {
        $record = TazaShumara::query()->findOrFail($tazaShumara);
        $coverImage = $record->cover_image;
        $record->delete();
        $this->deleteCover($coverImage);

        return redirect()->route('admin.taza-shumara.index')->with('status', 'Taza Shumara deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'languages' => Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']),
            'magazines' => Magazine::query()->where('isActive', true)->orderByDesc('publish_date')
                ->orderBy('title')->get(['id', 'title', 'issue_number', 'publish_date', 'language']),
            'articles' => $this->availableArticles(),
            'displayWidths' => TazaShumaraArticle::DISPLAY_WIDTHS,
            'positions' => TazaShumaraArticle::POSITIONS,
        ];
    }

    /** @param array<int, array<string, mixed>> $submittedPlacements */
    private function syncArticlePlacements(TazaShumara $tazaShumara, array $submittedPlacements): void
    {
        $retainedPlacementIds = [];

        foreach ($submittedPlacements as $placementData) {
            $placementId = Arr::pull($placementData, 'id');

            if ($placementId) {
                $placement = $tazaShumara->articlePlacements()->findOrFail($placementId);
                $placement->update($placementData);
                $retainedPlacementIds[] = $placement->id;

                continue;
            }

            $retainedPlacementIds[] = $tazaShumara->articlePlacements()->create($placementData)->id;
        }

        $tazaShumara->articlePlacements()
            ->when($retainedPlacementIds !== [], fn ($query) => $query->whereKeyNot($retainedPlacementIds))
            ->delete();
    }

    /** @return Collection<int, Article> */
    private function availableArticles(): Collection
    {
        return Article::query()->where('isActive', true)->with('categories:id,name')->orderByDesc('publish_date')
            ->orderBy('title')->get(['id', 'title', 'publish_date', 'language']);
    }

    private function deactivateLanguageWhenRequired(string $language, bool $isActive, ?int $exceptId = null): void
    {
        if (! $isActive) {
            return;
        }

        TazaShumara::query()
            ->where('language', $language)
            ->where('is_active', true)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->update(['is_active' => false]);
    }

    private function storeCover(UploadedFile $coverImage): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = now()->format('YmdHis').'-'.Str::random(12).'.'.$coverImage->extension();
        $coverImage->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function deleteCover(?string $path): void
    {
        if (filled($path) && Str::startsWith($path, self::IMAGE_DIRECTORY.'/')) {
            File::delete(public_path($path));
        }
    }
}
