<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreMagazineBookmarkRequest;
use App\Models\Article;
use App\Models\Magazine;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct(private readonly BookmarkService $bookmarkService) {}

    public function storeArticle(Request $request, Article $article): JsonResponse
    {
        $bookmark = $this->bookmarkService->storeArticle($request->user('web'), $article);

        return response()->json([
            'success' => true,
            'bookmarked' => true,
            'message' => $bookmark->wasRecentlyCreated
                ? 'مضمون بک مارک کر دیا گیا ہے۔'
                : 'یہ مضمون پہلے سے بک مارک ہے۔',
        ]);
    }

    public function destroyArticle(Request $request, Article $article): JsonResponse
    {
        $deleted = $this->bookmarkService->destroyArticle($request->user('web'), $article);

        return response()->json([
            'success' => true,
            'bookmarked' => false,
            'message' => $deleted
                ? 'مضمون بک مارک سے ہٹا دیا گیا ہے۔'
                : 'یہ مضمون بک مارک میں موجود نہیں تھا۔',
        ]);
    }

    public function storeMagazine(StoreMagazineBookmarkRequest $request, Magazine $magazine): JsonResponse
    {
        $pdfPage = (int) $request->validated('pdf_page');
        $bookmark = $this->bookmarkService->storeMagazinePage($request->user('web'), $magazine, $pdfPage);
        $wasCreated = $bookmark->wasRecentlyCreated;

        return response()->json([
            'success' => true,
            'bookmarked' => true,
            'pdf_page' => $bookmark->pdf_page,
            'created' => $wasCreated,
            'message' => $wasCreated
                ? "شمارہ صفحہ {$pdfPage} پر بک مارک کر دیا گیا ہے۔"
                : "محفوظ شدہ صفحہ {$pdfPage} کر دیا گیا ہے۔",
        ]);
    }

    public function destroyMagazine(Request $request, Magazine $magazine): JsonResponse
    {
        $deleted = $this->bookmarkService->destroyMagazinePage($request->user('web'), $magazine);

        return response()->json([
            'success' => true,
            'bookmarked' => false,
            'pdf_page' => null,
            'message' => $deleted
                ? 'شمارہ بک مارک سے ہٹا دیا گیا ہے۔'
                : 'یہ شمارہ بک مارک میں موجود نہیں تھا۔',
        ]);
    }

    public function destroyAccountBookmark(Request $request, int $bookmark): JsonResponse
    {
        $deleted = $request->user('web')->bookmarks()
            ->whereKey($bookmark)
            ->delete();

        return response()->json([
            'success' => true,
            'removed' => true,
            'message' => $deleted > 0
                ? 'بک مارک ہٹا دیا گیا ہے۔'
                : 'یہ بک مارک موجود نہیں تھا۔',
        ]);
    }
}
