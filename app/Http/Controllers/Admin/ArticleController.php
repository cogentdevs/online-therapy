<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MetaTag;
use App\Models\Tags;
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

class ArticleController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/articles';

    public function __construct(
        private readonly AdminContentOwnershipService $adminContentOwnershipService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request): View
    {
        $articles = $this->adminContentOwnershipService
            ->scopeQuery(Article::query(), $this->authenticatedAdmin($request))
            ->with(['categories:id,name', 'creator.roles'])
            ->latest()
            ->get();

        return view('admin.article.view-article', [
            'articles' => $articles,
            'languageNames' => Language::query()
                ->whereIn('code', $articles->pluck('language')->filter())
                ->pluck('name', 'code'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.article.add-article', $this->formOptions(null, $this->authenticatedAdmin($request)));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
            }

            DB::transaction(function () use ($request, $validated, $newImagePath): void {
                $article = Article::query()->newModelInstance([
                    ...$this->articleData($validated),
                    'image' => $newImagePath,
                    'isActive' => true,
                    'status' => Article::STATUS_DRAFT,
                ]);
                $article->forceFill(['owner_admin_id' => $this->authenticatedAdmin($request)->id]);

                if ($article->show_on_editorial_featured) {
                    Article::query()->where('show_on_editorial_featured', true)->update([
                        'show_on_editorial_featured' => false,
                    ]);
                }

                $article->save();

                $this->syncRelationships($article, $validated, $this->authenticatedAdmin($request));
                $this->updateMetaTag($article, $validated);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        return redirect()->route('admin.article.index')->with('status', 'Article added as a draft.');
    }

    public function show(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $article = $this->findOwnedArticle($id, $admin, [
            'categories:id,name',
            'tags:id,name',
            'authors:id,name',
            'relatedArticles' => fn ($query) => $this->adminContentOwnershipService
                ->scopeQuery($query, $admin)
                ->select(['articles.id', 'title', 'issue_number', 'image', 'status']),
        ]);

        return view('admin.article.view-article-detail', [
            'article' => $article,
            'languageName' => Language::query()->where('code', $article->language)->value('name'),
            'metaTag' => $this->articleMetaTag($article),
        ]);
    }

    public function edit(Request $request, int $id): View
    {
        $admin = $this->authenticatedAdmin($request);
        $article = $this->findOwnedArticle(
            $id,
            $admin,
            [
                'categories:id',
                'tags:id',
                'authors:id',
                'relatedArticles' => fn ($query) => $this->adminContentOwnershipService
                    ->scopeQuery($query, $admin)
                    ->select(['articles.id']),
            ],
        );

        return view('admin.article.edit-article', [
            ...$this->formOptions($article, $admin),
            'article' => $article,
            'metaTag' => $this->articleMetaTag($article),
            'selectedCategoryId' => $article->categories->sortBy('pivot.created_at')->first()?->id,
            'selectedTagIds' => $article->tags->modelKeys(),
            'selectedAuthorId' => $article->authors->sortBy('pivot.created_at')->first()?->id,
            'selectedRelatedArticleIds' => $article->relatedArticles->modelKeys(),
            'generatedSlug' => MetaTag::moduleSlug($article->title),
        ]);
    }

    public function update(UpdateArticleRequest $request, int $id): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $article = $this->findOwnedArticle($id, $admin);
        $validated = $request->validated();
        $previousImagePath = $article->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeImage($request->file('image'));
            }

            DB::transaction(function () use ($admin, $article, $validated, $newImagePath): void {
                $articleData = $this->articleData($validated);

                if ($newImagePath !== null) {
                    $articleData['image'] = $newImagePath;
                }

                if ($articleData['show_on_editorial_featured']) {
                    Article::query()
                        ->whereKeyNot($article->id)
                        ->where('show_on_editorial_featured', true)
                        ->update(['show_on_editorial_featured' => false]);
                }

                $article->update($articleData);
                $this->syncRelationships($article, $validated, $admin);
                $this->updateMetaTag($article, $validated);
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);

            throw $exception;
        }

        if ($newImagePath !== null) {
            $this->deleteManagedImage($previousImagePath);
        }

        return redirect()->route('admin.article.index')->with('status', 'Article updated successfully.');
    }

    public function publish(Request $request, int $id): RedirectResponse
    {
        $article = $this->findOwnedArticle($id, $this->authenticatedAdmin($request));

        if ($article->status === Article::STATUS_PUBLISHED) {
            return back()->with('error', 'This Article is already published.');
        }

        if (! $article->isActive) {
            return back()->with('error', 'Activate the Article before publishing it.');
        }

        $plainArticleContent = trim(html_entity_decode(strip_tags($article->article ?? '')));

        if (blank($article->title) || $plainArticleContent === '') {
            return back()->with('error', 'A title and Article content are required before publishing.');
        }

        $article->forceFill(['published_by' => auth()->id()]);
        $article->update([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('status', 'Article published successfully.');
    }

    public function removeRelatedArticle(Request $request, int $article, int $relatedArticle): RedirectResponse
    {
        $admin = $this->authenticatedAdmin($request);
        $currentArticle = $this->findOwnedArticle($article, $admin);
        $this->findOwnedArticle($relatedArticle, $admin);

        if (! $currentArticle->relatedArticles()->whereKey($relatedArticle)->exists()) {
            abort(404, 'The requested related Article mapping does not exist.');
        }

        $currentArticle->relatedArticles()->detach($relatedArticle);

        $this->activityLogService->log(
            'articles',
            'related_removed',
            $currentArticle,
            "Removed related Article #{$relatedArticle} from \"{$currentArticle->title}\".",
            ['related_article_id' => $relatedArticle],
            null,
        );

        return back()->with('status', 'Related Article removed successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $article = $this->findOwnedArticle($id, $this->authenticatedAdmin($request));
        $imagePath = $article->image;

        DB::transaction(function () use ($article): void {
            $article->tazaShumaraPlacements()->delete();

            MetaTag::query()
                ->where('table_name', $article->getTable())
                ->where('table_id', $article->id)
                ->delete();
            $article->delete();
        });

        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.article.index')->with('status', 'Article deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function formOptions(?Article $article, User $admin): array
    {
        return [
            'languages' => Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']),
            'categories' => Category::query()
                ->where(function ($query) use ($article): void {
                    $query->where('isActive', true)
                        ->when($article, fn ($query) => $query->orWhereIn(
                            'id',
                            $article->categories()->select('categories.id'),
                        ));
                })
                ->orderBy('name')
                ->get(['id', 'name', 'language']),
            'tags' => Tags::query()->where('isActive', true)->orderBy('name')->get(['id', 'name', 'language']),
            'authors' => Author::query()
                ->where(function ($query) use ($article): void {
                    $query->where('isActive', true)
                        ->when($article, fn ($query) => $query->orWhereIn(
                            'id',
                            $article->authors()->select('authors.id'),
                        ));
                })
                ->orderBy('name')
                ->get(['id', 'name']),
            'relatedArticles' => $this->adminContentOwnershipService->scopeQuery(Article::query(), $admin)
                ->where('isActive', true)
                ->when($article, fn ($query) => $query->whereKeyNot($article->id))
                ->orderBy('title')
                ->get(['id', 'language', 'title', 'issue_number']),
        ];
    }

    /** @param array<string, mixed> $validated */
    private function articleData(array $validated): array
    {
        $articleData = Arr::only($validated, [
            'language', 'magazine_id', 'title', 'publish_date',
            'short_description', 'article', 'isFree', 'isFeatured', 'isActive',
            'show_on_latest', 'show_on_editorial_center', 'show_on_editorial_featured',
            'show_visit_counter',
        ]);

        foreach (['show_on_latest', 'show_on_editorial_center', 'show_on_editorial_featured', 'show_visit_counter'] as $field) {
            $articleData[$field] = (bool) ($validated[$field] ?? false);
        }

        if (array_key_exists('magazine_id', $articleData)) {
            $articleData['issue_number'] = filled($articleData['magazine_id'])
                ? Magazine::query()->whereKey($articleData['magazine_id'])->value('issue_number')
                : null;
        }

        $articleData['free_until'] = (bool) $articleData['isFree']
            ? ($validated['free_until'] ?? null)
            : null;

        return $articleData;
    }

    /** @param array<string, mixed> $validated */
    private function syncRelationships(Article $article, array $validated, User $admin): void
    {
        $relatedArticleIds = array_map('intval', $validated['related_article_ids'] ?? []);

        if (in_array($article->id, $relatedArticleIds, true)) {
            throw ValidationException::withMessages([
                'related_article_ids' => 'An Article cannot be related to itself.',
            ]);
        }

        $this->adminContentOwnershipService->assertRecordsAccessible(
            $admin,
            Article::class,
            $relatedArticleIds,
            'related_article_ids',
        );

        $article->categories()->sync(
            filled($validated['category_id'] ?? null) ? [(int) $validated['category_id']] : [],
        );
        if (array_key_exists('tag_ids', $validated)) {
            $article->tags()->sync($validated['tag_ids'] ?? []);
        }
        $article->authors()->sync(
            filled($validated['author_id'] ?? null) ? [(int) $validated['author_id']] : [],
        );
        $article->relatedArticles()->sync(array_values(array_unique($relatedArticleIds)));
    }

    /** @param array<string, mixed> $validated */
    private function updateMetaTag(Article $article, array $validated): void
    {
        $metaTag = MetaTag::query()
            ->where('table_name', $article->getTable())
            ->where('table_id', $article->id)
            ->first();

        if ($metaTag && $metaTag->language !== $article->language) {
            $metaTag->update(['language' => $article->language]);
        }

        MetaTag::query()->updateOrCreate([
            'table_name' => $article->getTable(),
            'table_id' => $article->id,
            'language' => $article->language,
        ], [
            'title' => $article->title,
            'keywords' => $validated['keywords'] ?? null,
            'description' => $validated['meta_description'] ?? null,
            'slug_url' => MetaTag::moduleSlug($article->title),
            'canonical_url' => $metaTag?->canonical_url,
        ]);
    }

    private function articleMetaTag(Article $article): ?MetaTag
    {
        return MetaTag::query()
            ->where('table_name', $article->getTable())
            ->where('table_id', $article->id)
            ->where('language', $article->language)
            ->first();
    }

    /** @param array<int|string, mixed> $with */
    private function findOwnedArticle(int $id, User $admin, array $with = []): Article
    {
        $article = Article::query()->with($with)->findOrFail($id);
        $this->adminContentOwnershipService->authorizeAccess($admin, $article);

        return $article;
    }

    private function authenticatedAdmin(Request $request): User
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);

        return $admin;
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $filename = $this->availableImageFilename($image, $directory);
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function availableImageFilename(UploadedFile $image, string $directory): string
    {
        $safeBaseName = Str::of(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
            ->ascii()->replaceMatches('/[^A-Za-z0-9]+/', '-')->trim('-')->toString();
        $safeBaseName = $safeBaseName !== '' ? $safeBaseName : 'article-image';
        $extension = Str::lower($image->extension());
        $filename = $safeBaseName.'.'.$extension;
        $suffix = 2;

        while (File::exists($directory.DIRECTORY_SEPARATOR.$filename)) {
            $filename = $safeBaseName.'-'.$suffix.'.'.$extension;
            $suffix++;
        }

        return $filename;
    }

    private function deleteManagedImage(?string $path): void
    {
        if (! is_string($path)) {
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
