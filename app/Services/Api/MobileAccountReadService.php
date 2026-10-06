<?php

namespace App\Services\Api;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserContentVisit;
use App\Models\UserSubscription;
use App\Services\SubscriptionEntitlementService;
use App\Services\SubscriptionPresentationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MobileAccountReadService
{
    public function __construct(
        private readonly SubscriptionEntitlementService $entitlementService,
        private readonly SubscriptionPresentationService $presentationService,
    ) {}

    /** @return array<string, mixed> */
    public function activeSubscriptions(User $user): array
    {
        $subscriptions = $this->entitlementService->activeSubscriptions($user);

        return [
            'has_active_subscription' => $subscriptions->isNotEmpty(),
            'active_subscriptions' => $subscriptions->map(fn (UserSubscription $subscription): array => $this->subscription($subscription))->values()->all(),
        ];
    }

    /** @return array<string, mixed> */
    public function entitlements(User $user): array
    {
        return ['modules' => $this->entitlementService->activeEntitlementTypes($user)
            ->map(fn (SubscriptionType $type): array => $this->module($type))->all()];
    }

    /** @param array<string, mixed> $filters */
    public function subscriptions(User $user, array $filters): LengthAwarePaginator
    {
        $query = $user->userSubscriptions()->with(['subscriptionTypes', 'currency']);
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('product_name', 'like', '%'.$search.'%')
                    ->orWhere('product_for', 'like', '%'.$search.'%')
                    ->orWhere('payment_method', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('subscriptionTypes', fn (Builder $typeQuery): Builder => $typeQuery->where('name', 'like', '%'.$search.'%'));
            });
        }
        $column = match ($filters['sort'] ?? 'date') {
            'title' => 'product_name', 'amount' => 'total', 'payment_method' => 'payment_method',
            'status' => 'status', default => 'created_at',
        };

        return $query->orderBy($column, $filters['direction'] ?? 'desc')->orderByDesc('id')->paginate(10)->withQueryString();
    }

    /** @param array<string, mixed> $filters */
    public function bookmarks(User $user, array $filters): LengthAwarePaginator
    {
        $query = $user->bookmarks()->with('bookmarkable');
        $this->filterContent($query->getQuery(), 'bookmarkable', $filters);

        return $query->paginate(10)->withQueryString();
    }

    /** @param array<string, mixed> $filters */
    public function recentActivities(User $user, array $filters): LengthAwarePaginator
    {
        $query = $user->contentVisits()->with('visitable');
        $this->filterContent($query->getQuery(), 'visitable', $filters);

        return $query->paginate(10)->withQueryString();
    }

    /** @return array<string, mixed> */
    public function subscription(UserSubscription $subscription): array
    {
        $subscription->loadMissing(['subscriptionTypes', 'currency']);
        $this->presentationService->prepare([$subscription]);

        return [
            'id' => $subscription->id, 'order_no' => $subscription->order_no, 'invoice_no' => $subscription->invoice_no,
            'product_name' => $subscription->product_name, 'product_for' => $subscription->product_for,
            'product_type_label' => $subscription->frontend_product_type,
            'price' => $subscription->price, 'discount' => $subscription->discount, 'total' => $subscription->total,
            'currency' => $subscription->currency ? ['id' => $subscription->currency->id, 'code' => $subscription->currency->code, 'symbol' => $subscription->currency->symbol] : null,
            'payment_method' => $subscription->payment_method,
            'status' => $subscription->frontend_effective_status, 'status_label' => $subscription->frontend_status_label,
            'is_active' => (bool) $subscription->is_active,
            'purchased_at' => $subscription->created_at?->toIso8601String(),
            'start_date' => $subscription->start_date?->toDateString(), 'end_date' => $subscription->end_date?->toDateString(),
            'modules' => $subscription->subscriptionTypes->map(fn (SubscriptionType $type): array => $this->module($type))->values()->all(),
            'invoice' => [
                'view_url' => route('api.me.subscriptions.invoice.show', $subscription->id),
                'download_url' => route('api.me.subscriptions.invoice.download', $subscription->id),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function bookmark(Bookmark $bookmark): array
    {
        $content = $bookmark->bookmarkable;
        $type = $this->contentType($content);

        return [
            'id' => $bookmark->id, 'content_type' => $type, 'content_id' => $content?->id,
            'title' => $content?->title, 'image_url' => $this->contentImageUrl($content),
            'pdf_page' => $type === 'magazine' ? $bookmark->pdf_page : null,
            'bookmarked_at' => $bookmark->created_at?->toIso8601String(),
            'detail_url' => $this->contentUrl($content),
        ];
    }

    /** @return array<string, mixed> */
    public function recentActivity(UserContentVisit $visit): array
    {
        $content = $visit->visitable;

        return [
            'id' => $visit->id, 'content_type' => $this->contentType($content), 'content_id' => $content?->id,
            'title' => $content?->title, 'image_url' => $this->contentImageUrl($content),
            'last_visited_at' => $visit->last_visited_at?->toIso8601String(),
            'detail_url' => $this->contentUrl($content),
        ];
    }

    /** @return array<string, mixed> */
    public function pagination(LengthAwarePaginator $page): array
    {
        return [
            'current_page' => $page->currentPage(), 'last_page' => $page->lastPage(),
            'per_page' => $page->perPage(), 'total' => $page->total(),
            'from' => $page->firstItem(), 'to' => $page->lastItem(),
            'next_page_url' => $page->nextPageUrl(), 'previous_page_url' => $page->previousPageUrl(),
        ];
    }

    /** @return array{id: int, name: string, slug: string|null} */
    private function module(SubscriptionType $type): array
    {
        return ['id' => $type->id, 'name' => $type->name, 'slug' => $type->slug];
    }

    /** @param array<string, mixed> $filters */
    private function filterContent(Builder $query, string $prefix, array $filters): void
    {
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->whereHasMorph($prefix, [Article::class, Magazine::class],
                fn (Builder $contentQuery): Builder => $contentQuery->where('title', 'like', '%'.$search.'%'));
        }
        $direction = $filters['direction'] ?? 'desc';
        $sort = $filters['sort'] ?? 'date';
        if ($sort === 'type') {
            $query->orderBy($prefix.'_type', $direction);
        } elseif ($sort === 'title') {
            $query->orderByRaw("CASE WHEN {$prefix}_type = ? THEN (SELECT title FROM articles WHERE articles.id = {$prefix}_id) WHEN {$prefix}_type = ? THEN (SELECT title FROM magazines WHERE magazines.id = {$prefix}_id) ELSE NULL END {$direction}", [Article::class, Magazine::class]);
        } else {
            $query->orderBy($prefix === 'bookmarkable' ? 'created_at' : 'last_visited_at', $direction);
        }
        $query->orderByDesc('id');
    }

    private function contentType(mixed $content): ?string
    {
        return match (true) {
            $content instanceof Article => 'article', $content instanceof Magazine => 'magazine', default => null,
        };
    }

    private function contentImageUrl(mixed $content): ?string
    {
        $path = match (true) {
            $content instanceof Article => $content->image,
            $content instanceof Magazine => $content->cover_image,
            default => null,
        };

        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }

    private function contentUrl(mixed $content): ?string
    {
        return match (true) {
            $content instanceof Article => route('api.articles.show', $content->id),
            $content instanceof Magazine => route('api.magazines.show', $content->id),
            default => null,
        };
    }
}
