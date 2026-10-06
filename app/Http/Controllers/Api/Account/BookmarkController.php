<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\AccountListRequest;
use App\Http\Requests\Api\StoreMagazinePdfBookmarkRequest;
use App\Models\Article;
use App\Models\Magazine;
use App\Services\Api\MobileAccountReadService;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct(
        private readonly MobileAccountReadService $accountReadService,
        private readonly BookmarkService $bookmarkService,
    ) {}

    public function index(AccountListRequest $request): JsonResponse
    {
        $bookmarks = $this->accountReadService->bookmarks($request->user(), $request->validated());

        return response()->json(['success' => true, 'data' => [
            'bookmarks' => collect($bookmarks->items())->map(fn ($bookmark): array => $this->accountReadService->bookmark($bookmark))->all(),
            'pagination' => $this->accountReadService->pagination($bookmarks),
        ]]);
    }

    public function storeArticle(Request $request, Article $article): JsonResponse
    {
        $bookmark = $this->bookmarkService->storeArticle($request->user(), $article);
        $bookmark->setRelation('bookmarkable', $article);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark saved successfully.',
            'data' => ['bookmark' => $this->accountReadService->bookmark($bookmark)],
        ]);
    }

    public function destroyArticle(Request $request, Article $article): JsonResponse
    {
        $this->bookmarkService->destroyArticle($request->user(), $article);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark removed successfully.',
        ]);
    }

    public function storeMagazinePdf(StoreMagazinePdfBookmarkRequest $request, Magazine $magazine): JsonResponse
    {
        $bookmark = $this->bookmarkService->storeMagazinePage(
            $request->user(),
            $magazine,
            (int) $request->validated('pdf_page'),
        );
        $bookmark->setRelation('bookmarkable', $magazine);

        return response()->json([
            'success' => true,
            'message' => 'Magazine page bookmark saved successfully.',
            'data' => ['bookmark' => $this->accountReadService->bookmark($bookmark)],
        ]);
    }

    public function destroyMagazinePdf(Request $request, Magazine $magazine): JsonResponse
    {
        $this->bookmarkService->destroyMagazinePage($request->user(), $magazine);

        return response()->json([
            'success' => true,
            'message' => 'Magazine page bookmark removed successfully.',
        ]);
    }
}
