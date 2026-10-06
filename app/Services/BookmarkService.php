<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\QueryException;

class BookmarkService
{
    public function storeArticle(User $user, Article $article): Bookmark
    {
        $bookmark = $this->firstOrCreate($user, $article);

        if ($bookmark->pdf_page !== null) {
            $bookmark->update(['pdf_page' => null]);
        }

        return $bookmark;
    }

    public function destroyArticle(User $user, Article $article): bool
    {
        return $this->destroy($user, $article);
    }

    public function storeMagazinePage(User $user, Magazine $magazine, int $pdfPage): Bookmark
    {
        $bookmark = $this->firstOrCreate($user, $magazine, $pdfPage);

        if (! $bookmark->wasRecentlyCreated) {
            $bookmark->pdf_page = $pdfPage;

            if ($bookmark->isDirty('pdf_page')) {
                $bookmark->save();
            } else {
                $bookmark->touch();
            }
        }

        return $bookmark;
    }

    public function destroyMagazinePage(User $user, Magazine $magazine): bool
    {
        return $this->destroy($user, $magazine);
    }

    private function firstOrCreate(User $user, Article|Magazine $content, ?int $pdfPage = null): Bookmark
    {
        try {
            return $content->bookmarks()->firstOrCreate(
                ['user_id' => $user->getKey()],
                ['pdf_page' => $pdfPage],
            );
        } catch (QueryException $exception) {
            $bookmark = $content->bookmarks()
                ->where('user_id', $user->getKey())
                ->first();

            if ($bookmark === null) {
                throw $exception;
            }

            return $bookmark;
        }
    }

    private function destroy(User $user, Article|Magazine $content): bool
    {
        return $content->bookmarks()
            ->where('user_id', $user->getKey())
            ->delete() > 0;
    }
}
