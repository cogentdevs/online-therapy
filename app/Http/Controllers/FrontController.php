<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\StoreContactRequest;
use App\Http\Requests\Frontend\StoreSubscriptionPurchaseRequest;
use App\Mail\ContactMailToAdmin;
use App\Models\About;
use App\Models\Article;
use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Consultancy;
use App\Models\Contact;
use App\Models\Course;
use App\Models\FaqCategory;
use App\Models\GeneralSetting;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\HomePageSectionHeading;
use App\Models\InfoPage;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\MetaTag;
use App\Models\PaymentAccount;
use App\Models\Slider;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\Tags;
use App\Models\TazaShumara;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use App\Services\PaidContentLoginIntentService;
use App\Services\SiteVisitService;
use App\Services\Storage\MediaStorageService;
use App\Services\SubscriptionContentAccessService;
use App\Services\SubscriptionPaymentMailService;
use App\Services\SubscriptionPurchaseService;
use App\Services\SubscriptionRenewalService;
use App\Services\UserContentVisitService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FrontController extends Controller
{
    public function __construct(
        private readonly MediaStorageService $mediaStorageService,
        private readonly SubscriptionPurchaseService $subscriptionPurchaseService,
        private readonly SubscriptionPaymentMailService $subscriptionPaymentMailService,
        private readonly SubscriptionRenewalService $subscriptionRenewalService,
        private readonly SubscriptionContentAccessService $subscriptionContentAccessService,
        private readonly PaidContentLoginIntentService $paidContentLoginIntentService,
        private readonly UserContentVisitService $userContentVisitService,
        private readonly SiteVisitService $siteVisitService,
    ) {}

    public function subscriptions(Request $request): View
    {
        $plans = SubscriptionProduct::query()
            ->availableToFrontend()
            ->where('product_for', SubscriptionProduct::FOR_PLAN)
            ->with([
                'currency:id,code,symbol,isActive',
                'subscriptionTypes' => fn ($query) => $query
                    ->where('subscription_types.isActive', true)
                    ->orderBy('subscription_types.name')
                    ->select(['subscription_types.id', 'name', 'slug', 'isActive']),
                'videos' => fn ($query) => $query
                    ->where('videos.is_active', true)
                    ->where('videos.status', Video::STATUS_PUBLISHED)
                    ->orderBy('videos.title')
                    ->select(['videos.id', 'videos.title']),
            ])->orderBy('price')->orderBy('id')->get();

        $memberships = SubscriptionProduct::query()
            ->availableToFrontend()
            ->where('product_for', SubscriptionProduct::FOR_MEMBERSHIP)
            ->with([
                'currency:id,code,symbol,isActive',
                'subscriptionTypes' => fn ($query) => $query
                    ->where('subscription_types.isActive', true)
                    ->orderBy('subscription_types.name')
                    ->select(['subscription_types.id', 'name', 'slug', 'isActive']),
            ])->orderBy('price')->orderBy('id')->get();

        $plans->concat($memberships)->each($this->prepareSubscriptionProduct(...));
        $this->prepareSubscriptionPurchaseStates($plans->concat($memberships));

        $planGroups = $plans
            ->flatMap(fn (SubscriptionProduct $plan): Collection => $plan->subscriptionTypes->map(
                fn ($type): array => ['type' => $type, 'plan' => $plan],
            ))
            ->groupBy(fn (array $item): int => $item['type']->id)
            ->map(fn (Collection $items): array => [
                'type' => $items->first()['type'],
                'label' => $this->frontendSubscriptionTypeLabel($items->first()['type']),
                'plans' => $items->pluck('plan')->values(),
            ])->values();

        $membershipTypes = SubscriptionType::query()
            ->where('isActive', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'isActive'])
            ->each($this->prepareMembershipType(...));

        $this->siteVisitService->record($request);

        return view('frontend.subscriptions', compact('memberships', 'membershipTypes', 'planGroups'));
    }

    public function privacyPolicy(): View
    {
        return $this->infoPage('privacy-policy');
    }

    public function termsAndConditions(): View
    {
        return $this->infoPage('terms-and-conditions');
    }

    public function disclaimer(): View
    {
        return $this->infoPage('disclaimer');
    }

    public function storeSubscriptionIntent(Request $request, SubscriptionProduct $subscriptionProduct): JsonResponse
    {
        abort_unless($this->isAvailableSubscriptionProduct($subscriptionProduct), 404);

        if ($request->user('web') !== null) {
            $purchaseState = $this->subscriptionRenewalService->stateFor($request->user('web'), $subscriptionProduct);

            if (! $this->subscriptionPurchaseStateAllowsCheckout($purchaseState['state'])) {
                return response()->json([
                    'message' => $this->subscriptionPurchaseStateWarning($purchaseState['state']),
                ], 422);
            }
        }

        $request->session()->put('front_subscription_product_id', $subscriptionProduct->getKey());

        return response()->json([
            'checkout_url' => route('front.subscriptions.checkout', $subscriptionProduct),
        ]);
    }

    public function clearSubscriptionIntent(Request $request): JsonResponse
    {
        $request->session()->forget('front_subscription_product_id');

        return response()->json(['cleared' => true]);
    }

    public function clearPaidContentIntent(Request $request): JsonResponse
    {
        $this->paidContentLoginIntentService->clear($request);

        return response()->json(['cleared' => true]);
    }

    public function subscriptionCheckout(Request $request, SubscriptionProduct $subscriptionProduct): View|RedirectResponse
    {
        abort_unless($this->isAvailableSubscriptionProduct($subscriptionProduct), 404);
        $purchaseState = $this->subscriptionRenewalService->stateFor($request->user('web'), $subscriptionProduct);

        if (! $this->subscriptionPurchaseStateAllowsCheckout($purchaseState['state'])) {
            return to_route('front.subscriptions')
                ->with('warning', $this->subscriptionPurchaseStateWarning($purchaseState['state']));
        }

        $subscriptionProduct->loadMissing([
            'currency:id,code,symbol,isActive',
            'subscriptionTypes' => fn ($query) => $query
                ->where('subscription_types.isActive', true)
                ->orderBy('subscription_types.name')
                ->select(['subscription_types.id', 'name', 'slug', 'isActive']),
        ]);
        $this->prepareSubscriptionProduct($subscriptionProduct);
        $subscriptionProduct->subscriptionTypes->each($this->prepareMembershipType(...));
        $subscriptionProduct->setAttribute('frontend_product_for', match ($subscriptionProduct->product_for) {
            SubscriptionProduct::FOR_PLAN => 'منصوبہ',
            SubscriptionProduct::FOR_MEMBERSHIP => 'رکنیت',
            default => (string) $subscriptionProduct->product_for,
        });
        $paymentAccounts = PaymentAccount::query()
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->orderBy('id')
            ->get(['id', 'bank_name', 'account_title', 'iban', 'account_no', 'branch_code']);

        return view('frontend.subscription-checkout', compact('subscriptionProduct', 'paymentAccounts'));
    }

    public function storeSubscriptionCheckout(
        StoreSubscriptionPurchaseRequest $request,
        SubscriptionProduct $subscriptionProduct,
    ): RedirectResponse {
        abort_unless($this->isAvailableSubscriptionProduct($subscriptionProduct), 404);

        try {
            $validated = $request->validated();
            $userSubscription = $this->subscriptionPurchaseService->submitPaymentSlip(
                $request->user('web'),
                $subscriptionProduct,
                (int) $validated['payment_account_id'],
                $request->file('payment_slip'),
                $validated['transaction_id'] ?? null,
            );
            $this->subscriptionPaymentMailService->sendPending($userSubscription);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'subscription' => 'ادائیگی کی رسید جمع نہیں ہو سکی۔ براہ کرم دوبارہ کوشش کریں۔',
            ]);
        }

        return to_route('front.account.subscriptions')->with(
            'success',
            'آپ کی ادائیگی کی رسید کامیابی سے جمع ہو گئی ہے اور ایڈمن کی تصدیق کی منتظر ہے۔',
        );
    }

    public function resubmitSubscriptionPayment(Request $request, int $subscription): View
    {
        $userSubscription = $request->user('web')->userSubscriptions()
            ->with(['subscriptionProduct.currency', 'subscriptionTypes', 'currency'])
            ->where('status', UserSubscription::STATUS_REJECTED)
            ->where('payment_status', UserSubscription::PAYMENT_STATUS_REJECTED)
            ->findOrFail($subscription);
        $subscriptionProduct = $userSubscription->subscriptionProduct;
        abort_unless($subscriptionProduct !== null, 404);

        $subscriptionProduct->setRelation('subscriptionTypes', $userSubscription->subscriptionTypes);
        $subscriptionProduct->setRelation('currency', $userSubscription->currency);
        $this->prepareSubscriptionProduct($subscriptionProduct);
        $subscriptionProduct->subscriptionTypes->each($this->prepareMembershipType(...));
        $subscriptionProduct->setAttribute('frontend_membership_name', $userSubscription->product_name);
        $subscriptionProduct->setAttribute('frontend_price', $userSubscription->price);
        $subscriptionProduct->setAttribute('frontend_discounted_price', $userSubscription->total);
        $subscriptionProduct->setAttribute('frontend_has_discount', false);
        $subscriptionProduct->setAttribute('frontend_currency', $userSubscription->currency?->code ?: $userSubscription->currency?->symbol);
        $subscriptionProduct->setAttribute('frontend_duration', trim($userSubscription->duration_value_snapshot.' '.$userSubscription->duration_unit_snapshot));
        $subscriptionProduct->setAttribute('frontend_product_for', match ($userSubscription->product_for) {
            SubscriptionProduct::FOR_PLAN => 'منصوبہ',
            SubscriptionProduct::FOR_MEMBERSHIP => 'رکنیت',
            default => (string) $userSubscription->product_for,
        });
        $paymentAccounts = PaymentAccount::query()
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->orderBy('id')
            ->get(['id', 'bank_name', 'account_title', 'iban', 'account_no', 'branch_code']);

        return view('frontend.subscription-checkout', [
            'subscriptionProduct' => $subscriptionProduct,
            'paymentAccounts' => $paymentAccounts,
            'paymentResubmission' => $userSubscription,
        ]);
    }

    public function storeResubmittedSubscriptionPayment(
        StoreSubscriptionPurchaseRequest $request,
        int $subscription,
    ): RedirectResponse {
        $userSubscription = $request->user('web')->userSubscriptions()->findOrFail($subscription);
        $validated = $request->validated();

        try {
            $resubmittedSubscription = $this->subscriptionPurchaseService->resubmitRejectedPayment(
                $request->user('web'),
                $userSubscription,
                (int) $validated['payment_account_id'],
                $request->file('payment_slip'),
                $validated['transaction_id'] ?? null,
            );
            $this->subscriptionPaymentMailService->sendPending($resubmittedSubscription);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'subscription' => 'Payment evidence could not be resubmitted. Please try again.',
            ]);
        }

        return to_route('front.account.subscriptions')->with(
            'success',
            'Your payment has been resubmitted and is pending verification.',
        );
    }

    public function sabqa_shumare(Request $request): View
    {
        $currentLanguage = config('content_language.code');

        $publicMagazines = fn (): Builder => $this->publicMagazineQuery($currentLanguage);

        $selectedCategory = $request->integer('category') ?: null;
        $selectedYear = $request->integer('year') ?: null;
        $selectedTag = $request->integer('tag') ?: null;
        $selectedAuthor = $request->integer('author') ?: null;
        $search = Str::limit(trim((string) $request->query('search', '')), 100, '');

        $magazines = $publicMagazines()
            ->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image', 'description', 'show_visit_counter'])
            ->withCount('siteVisits')
            ->with(['authors' => fn ($query) => $query
                ->where('isActive', true)
                ->orderBy('name')
                ->select(['authors.id', 'name', 'isActive'])])
            ->when($selectedCategory, fn (Builder $query, int $categoryId): Builder => $query->whereHas('categories', fn (Builder $categoryQuery): Builder => $categoryQuery->whereKey($categoryId)))
            ->when($selectedYear, fn (Builder $query, int $year): Builder => $query->whereYear('publish_date', $year))
            ->when($selectedTag, fn (Builder $query, int $tagId): Builder => $query->whereHas('tags', fn (Builder $tagQuery): Builder => $tagQuery->whereKey($tagId)))
            ->when($selectedAuthor, fn (Builder $query, int $authorId): Builder => $query->whereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery->whereKey($authorId)))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';

                $query->where(function (Builder $searchQuery) use ($searchPattern): void {
                    $searchQuery
                        ->where('title', 'like', $searchPattern)
                        ->orWhere('description', 'like', $searchPattern)
                        ->orWhere('issue_number', 'like', $searchPattern)
                        ->orWhereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery
                            ->where('isActive', true)
                            ->where('name', 'like', $searchPattern));
                });
            })
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $magazines->getCollection()->each(function (Magazine $magazine) use ($search): void {
            $magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($magazine));

            if ($search !== '') {
                $magazine->setAttribute('search_result_url', URL::signedRoute('front.search-content.track', [
                    'type' => 'magazine',
                    'id' => $magazine->id,
                    'search' => $search,
                ]));
            }
        });

        $applyPublicMagazineRules = function (Builder $query) use ($currentLanguage): void {
            $query
                ->where('magazines.language', $currentLanguage)
                ->where('magazines.isActive', true)
                ->where('magazines.status', Magazine::STATUS_PUBLISHED)
                ->whereNotNull('magazines.publish_date')
                ->whereDate('magazines.publish_date', '<=', today())
                ->where(function (Builder $query): void {
                    $query->whereNull('magazines.published_at')->orWhere('magazines.published_at', '<=', now());
                });
        };

        $categories = Category::query()
            ->where('language', $currentLanguage)
            ->where('isActive', true)
            ->whereHas('magazines', $applyPublicMagazineRules)
            ->orderBy('name')
            ->get(['id', 'name']);

        $tags = Tags::query()
            ->where('language', $currentLanguage)
            ->where('isActive', true)
            ->whereHas('magazines', $applyPublicMagazineRules)
            ->orderBy('name')
            ->get(['id', 'name']);

        $authors = Author::query()
            ->where('isActive', true)
            ->whereHas('magazines', $applyPublicMagazineRules)
            ->orderBy('name')
            ->get(['id', 'name', 'picture']);

        $yearExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', publish_date)",
            'pgsql' => 'EXTRACT(YEAR FROM publish_date)',
            default => 'YEAR(publish_date)',
        };
        $years = $publicMagazines()
            ->whereNotNull('publish_date')
            ->selectRaw($yearExpression.' as publication_year')
            ->distinct()
            ->orderByDesc('publication_year')
            ->pluck('publication_year')
            ->map(fn (mixed $year): int => (int) $year);

        $this->siteVisitService->record($request);

        return view('frontend.sabqa-shumare', compact(
            'authors',
            'categories',
            'magazines',
            'selectedAuthor',
            'selectedCategory',
            'selectedTag',
            'selectedYear',
            'search',
            'tags',
            'years',
        ));
    }

    public function tazaShumara(Request $request): View
    {
        $currentLanguage = config('content_language.code');

        $tazaShumara = TazaShumara::query()
            ->where('language', $currentLanguage)
            ->where('is_active', true)
            ->with([
                'magazine' => fn ($query) => $query
                    ->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image', 'description'])
                    ->with([
                        'authors' => fn ($authorQuery) => $authorQuery->where('isActive', true)->select(['authors.id', 'name', 'picture', 'isActive']),
                        'categories' => fn ($categoryQuery) => $categoryQuery->where('categories.language', $currentLanguage)->where('categories.isActive', true)->select(['categories.id', 'language', 'name', 'isActive']),
                        'tags' => fn ($tagQuery) => $tagQuery->where('tags.language', $currentLanguage)->where('tags.isActive', true)->select(['tags.id', 'language', 'name', 'isActive']),
                        'relatedMagazines' => fn ($relatedQuery) => $relatedQuery
                            ->where('magazines.language', $currentLanguage)
                            ->where('magazines.isActive', true)
                            ->where('magazines.status', Magazine::STATUS_PUBLISHED)
                            ->select(['magazines.id', 'language', 'title', 'issue_number', 'publish_date', 'cover_image', 'isActive', 'status']),
                    ]),
                'articlePlacements' => fn ($query) => $query
                    ->select(['id', 'taza_shumara_id', 'article_id', 'display_width', 'position', 'sort_order'])
                    ->with(['article' => fn ($articleQuery) => $articleQuery
                        ->select(['id', 'language', 'title', 'publish_date', 'short_description', 'image', 'isActive', 'status'])
                        ->with('categories:id,name')])
                    ->orderByRaw("CASE position WHEN 'top' THEN 1 WHEN 'center' THEN 2 ELSE 3 END")
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->first();

        $articlePlacements = collect(['top' => collect(), 'center' => collect(), 'bottom' => collect()]);

        if ($tazaShumara?->magazine) {
            $tazaShumara->magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($tazaShumara->magazine));

            $tazaShumara->magazine->relatedMagazines->each(function ($magazine): void {
                $magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($magazine));
            });

            $eligiblePlacements = $tazaShumara->articlePlacements
                ->filter(function ($placement) use ($currentLanguage): bool {
                    $article = $placement->article;

                    if (! $article || $article->language !== $currentLanguage || ! $article->isActive || $article->status !== Article::STATUS_PUBLISHED) {
                        return false;
                    }

                    $slug = Str::slug($article->title ?? '');
                    $article->setAttribute('frontend_url', url('/articles/'.$article->id.'/'.($slug ?: 'article-'.$article->id)));
                    $article->setAttribute(
                        'image_available',
                        filled($article->image) && File::isFile(public_path($article->image)),
                    );

                    return true;
                });

            foreach (['top', 'center', 'bottom'] as $position) {
                $articlePlacements[$position] = $eligiblePlacements->where('position', $position)->values();
            }
        }

        $this->siteVisitService->record($request);

        return view('frontend.taza-shumara', [
            'articlePlacements' => $articlePlacements,
            'tazaShumara' => $tazaShumara,
            'tazaShumaraAds' => collect(),
        ]);
    }

    public function mazameen(Request $request): View
    {
        $currentLanguage = config('content_language.code');
        $publicArticles = fn (): Builder => $this->publicArticleQuery($currentLanguage);
        $selectedCategory = $request->integer('category') ?: null;
        $selectedYear = $request->integer('year') ?: null;
        $selectedTag = $request->integer('tag') ?: null;
        $selectedAuthor = $request->integer('author') ?: null;
        $search = Str::limit(trim((string) $request->query('search', '')), 100, '');

        $articles = $publicArticles()
            ->select(['id', 'title', 'issue_number', 'publish_date', 'short_description', 'image', 'show_visit_counter'])
            ->withCount('siteVisits')
            ->with([
                'authors' => fn ($query) => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture', 'isActive']),
                'categories' => fn ($query) => $query->where('categories.language', $currentLanguage)->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name', 'language', 'isActive']),
            ])
            ->when($selectedCategory, fn (Builder $query, int $categoryId): Builder => $query->whereHas('categories', fn (Builder $categoryQuery): Builder => $categoryQuery->whereKey($categoryId)))
            ->when($selectedYear, fn (Builder $query, int $year): Builder => $query->whereYear('publish_date', $year))
            ->when($selectedTag, fn (Builder $query, int $tagId): Builder => $query->whereHas('tags', fn (Builder $tagQuery): Builder => $tagQuery->whereKey($tagId)))
            ->when($selectedAuthor, fn (Builder $query, int $authorId): Builder => $query->whereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery->whereKey($authorId)))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';

                $query->where(function (Builder $searchQuery) use ($searchPattern): void {
                    $searchQuery
                        ->where('title', 'like', $searchPattern)
                        ->orWhere('short_description', 'like', $searchPattern)
                        ->orWhere('issue_number', 'like', $searchPattern)
                        ->orWhereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery
                            ->where('isActive', true)
                            ->where('name', 'like', $searchPattern));
                });
            })
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        $articles->getCollection()->each(function (Article $article) use ($search): void {
            $article->setAttribute('frontend_url', $this->mazmoonDetailUrl($article));
            $article->setAttribute('image_available', filled($article->image) && File::isFile(public_path($article->image)));

            if ($search !== '') {
                $article->setAttribute('search_result_url', URL::signedRoute('front.search-content.track', [
                    'type' => 'article',
                    'id' => $article->id,
                    'search' => $search,
                ]));
            }
        });

        $applyPublicArticleRules = function (Builder $query) use ($currentLanguage): void {
            $query
                ->where('articles.language', $currentLanguage)
                ->where('articles.isActive', true)
                ->where('articles.status', Article::STATUS_PUBLISHED)
                ->whereNotNull('articles.publish_date')
                ->whereDate('articles.publish_date', '<=', today())
                ->where(function (Builder $query): void {
                    $query->whereNull('articles.published_at')->orWhere('articles.published_at', '<=', now());
                });
        };

        $categories = Category::query()->where('language', $currentLanguage)->where('isActive', true)
            ->whereHas('articles', $applyPublicArticleRules)->orderBy('name')->get(['id', 'name']);
        $tags = Tags::query()->where('language', $currentLanguage)->where('isActive', true)
            ->whereHas('articles', $applyPublicArticleRules)->orderBy('name')->get(['id', 'name']);
        $authors = Author::query()->where('isActive', true)
            ->whereHas('articles', $applyPublicArticleRules)->orderBy('name')->get(['id', 'name', 'picture']);

        $yearExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', publish_date)",
            'pgsql' => 'EXTRACT(YEAR FROM publish_date)',
            default => 'YEAR(publish_date)',
        };
        $years = $publicArticles()->selectRaw($yearExpression.' as publication_year')->distinct()
            ->orderByDesc('publication_year')->pluck('publication_year')->map(fn (mixed $year): int => (int) $year);

        $this->siteVisitService->record($request);

        return view('frontend.mazameen', compact(
            'articles',
            'authors',
            'categories',
            'search',
            'selectedAuthor',
            'selectedCategory',
            'selectedTag',
            'selectedYear',
            'tags',
            'years',
        ));
    }

    public function consultancies(Request $request): View
    {
        $consultancies = Consultancy::query()
            ->publiclyAvailable()
            ->select([
                'id',
                'title',
                'button_label',
                'image',
                'short_description',
                'duration_type',
                'duration_value',
                'consultancy_medium',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        $this->siteVisitService->record($request);

        return view('frontend.consultancies.index', compact('consultancies'));
    }

    public function consultancy(Request $request, int $consultancy): View
    {
        $consultancy = Consultancy::query()
            ->publiclyAvailable()
            ->with([
                'relatedConsultancies' => fn ($query) => $query
                    ->publiclyAvailable()
                    ->orderByDesc('consultancies.published_at')
                    ->orderByDesc('consultancies.id')
                    ->select([
                        'consultancies.id',
                        'consultancies.title',
                        'consultancies.button_label',
                        'consultancies.image',
                        'consultancies.short_description',
                        'consultancies.duration_type',
                        'consultancies.duration_value',
                        'consultancies.consultancy_medium',
                    ]),
            ])
            ->findOrFail($consultancy);

        $this->siteVisitService->record($request, $consultancy);

        return view('frontend.consultancies.show', compact('consultancy'));
    }

    public function courses(Request $request): View
    {
        $courses = Course::query()
            ->publiclyAvailable()
            ->select(['id', 'title', 'button_label', 'image', 'short_description', 'duration_type', 'duration_value'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        $this->siteVisitService->record($request);

        return view('frontend.courses.index', compact('courses'));
    }

    public function course(Request $request, int $course): View
    {
        $course = Course::query()
            ->publiclyAvailable()
            ->with([
                'relatedCourses' => fn ($query) => $query
                    ->publiclyAvailable()
                    ->orderByDesc('courses.published_at')
                    ->orderByDesc('courses.id')
                    ->select(['courses.id', 'courses.title', 'courses.button_label', 'courses.image', 'courses.short_description', 'courses.duration_type', 'courses.duration_value']),
            ])
            ->findOrFail($course);

        $this->siteVisitService->record($request, $course);

        return view('frontend.courses.show', compact('course'));
    }

    public function taaruf(Request $request): View
    {
        $aboutSections = About::query()
            ->where('language', config('content_language.code'))
            ->where('is_active', true)
            ->orderBy('id')
            ->get([
                'id',
                'section_condition',
                'title',
                'description',
                'title_2',
                'description_2',
                'image',
                'image_2',
            ]);

        $aboutSections->each(function (About $section): void {
            $section->setAttribute(
                'image_available',
                filled($section->image) && File::isFile(public_path($section->image)),
            );
            $section->setAttribute(
                'image_2_available',
                filled($section->image_2) && File::isFile(public_path($section->image_2)),
            );
        });

        $this->siteVisitService->record($request);

        return view('frontend.taaruf', compact('aboutSections'));
    }

    public function contact(Request $request): View
    {
        $this->siteVisitService->record($request);

        return view('frontend.contact');
    }

    public function faqs(Request $request): View
    {
        $language = config('content_language.code');

        $faqCategories = FaqCategory::query()
            ->where('language', $language)
            ->where('isActive', true)
            ->with(['faqs' => fn ($query) => $query
                ->where('language', $language)
                ->where('isActive', true)
                ->orderBy('id')
                ->select(['id', 'faq_category_id', 'language', 'question', 'answer', 'isActive'])])
            ->orderBy('id')
            ->get(['id', 'language', 'name', 'icon', 'isActive']);

        $faqCategories->each(fn (FaqCategory $category) => $category->setAttribute(
            'icon_available',
            filled($category->icon) && File::isFile(public_path($category->icon)),
        ));

        $this->siteVisitService->record($request);

        return view('frontend.faq', compact('faqCategories', 'language'));
    }

    public function storeContact(StoreContactRequest $request): RedirectResponse
    {
        $contact = Contact::query()->create($request->safe()->only([
            'name',
            'phone',
            'email',
            'subject',
            'message',
        ]));

        $adminEmail = 'cogentdevs@gmail.com';

        if (is_string($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) !== false) {
            try {
                Mail::to($adminEmail)->send(new ContactMailToAdmin($contact));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return to_route('front.contact')->with(
            'success',
            'آپ کا پیغام کامیابی سے موصول ہو گیا ہے۔ ہم جلد آپ سے رابطہ کریں گے۔',
        );
    }

    public function mozoaat(Request $request): View
    {
        $categories = $this->frontendCategoryQuery(withContentCounts: true)
            ->paginate(16, ['id', 'name', 'image'])
            ->withQueryString();

        $this->prepareFrontendCategories($categories->getCollection());

        $this->siteVisitService->record($request);

        return view('frontend.mozoaat', compact('categories'));
    }

    public function mazmoonNigaar(Request $request): View
    {
        $search = Str::limit(trim((string) $request->query('q', '')), 100, '');
        $defaultVisibility = $this->authorDefaultVisibility();

        $authors = Author::query()
            ->where('isActive', true)
            ->with(['visibilities' => fn ($query) => $query
                ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
                ->select(['id', 'author_id', 'column_name', 'isView'])])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';

                $query->where(function (Builder $searchQuery) use ($searchPattern): void {
                    $searchQuery->where('name', 'like', $searchPattern)
                        ->orWhere('speciality', 'like', $searchPattern)
                        ->orWhere('qualification', 'like', $searchPattern);
                });
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        $authors->getCollection()->each(function (Author $author) use ($defaultVisibility): void {
            $this->prepareFrontendAuthor($author, $defaultVisibility);
        });

        $this->siteVisitService->record($request);

        return view('frontend.mazmoon-nigaar', compact('authors', 'search'));
    }

    public function mazmoonNigaarDetail(Request $request, int $id, string $slug): View
    {
        $defaultVisibility = $this->authorDefaultVisibility();
        $author = Author::query()
            ->where('isActive', true)
            ->with(['visibilities' => fn ($query) => $query
                ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
                ->select(['id', 'author_id', 'column_name', 'isView'])])
            ->findOrFail($id);

        $this->prepareFrontendAuthor($author, $defaultVisibility);

        $selectedCategory = $request->integer('category') ?: null;
        $selectedYear = $request->integer('year') ?: null;
        $search = Str::limit(trim((string) $request->query('q', '')), 100, '');

        $authorArticles = fn (): Builder => $this->publicArticleQuery()
            ->whereHas('authors', fn (Builder $query): Builder => $query->whereKey($author->id));

        $articles = $authorArticles()
            ->with(['categories' => fn ($query) => $query
                ->where('categories.language', config('content_language.code'))
                ->where('categories.isActive', true)
                ->orderBy('name')
                ->select(['categories.id', 'name', 'language', 'isActive'])])
            ->when($selectedCategory, fn (Builder $query, int $categoryId): Builder => $query
                ->whereHas('categories', fn (Builder $categoryQuery): Builder => $categoryQuery->whereKey($categoryId)))
            ->when($selectedYear, fn (Builder $query, int $year): Builder => $query->whereYear('publish_date', $year))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';
                $query->where(function (Builder $searchQuery) use ($searchPattern): void {
                    $searchQuery->where('title', 'like', $searchPattern)
                        ->orWhere('short_description', 'like', $searchPattern);
                });
            })
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->paginate(12, ['id', 'title', 'publish_date', 'short_description', 'image'])
            ->withQueryString();

        $articles->getCollection()->each(function (Article $article): void {
            $article->setAttribute('frontend_url', $this->mazmoonDetailUrl($article));
            $article->setAttribute('image_available', filled($article->image) && File::isFile(public_path($article->image)));
        });

        $applyAuthorArticleRules = function (Builder $query) use ($author): void {
            $this->applyPublicArticleConstraints($query)
                ->whereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery->whereKey($author->id));
        };

        $categories = Category::query()
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->whereHas('articles', $applyAuthorArticleRules)
            ->withCount(['articles as author_articles_count' => $applyAuthorArticleRules])
            ->orderBy('name')
            ->get(['id', 'name']);

        $yearExpression = $this->publicationYearExpression((new Article)->getTable());
        $years = $authorArticles()
            ->selectRaw($yearExpression.' as publication_year, COUNT(*) as article_count')
            ->groupBy(DB::raw($yearExpression))
            ->orderByDesc('publication_year')
            ->get();

        $otherAuthors = Author::query()
            ->where('isActive', true)
            ->whereKeyNot($author->id)
            ->with(['visibilities' => fn ($query) => $query
                ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
                ->select(['id', 'author_id', 'column_name', 'isView'])])
            ->orderBy('name')
            ->limit(5)
            ->get();

        $otherAuthors->each(fn (Author $otherAuthor) => $this->prepareFrontendAuthor($otherAuthor, $defaultVisibility));

        $this->siteVisitService->record($request, $author);

        return view('frontend.mazmoon-nigaar-detail', compact(
            'articles',
            'author',
            'categories',
            'otherAuthors',
            'search',
            'selectedCategory',
            'selectedYear',
            'years',
        ));
    }

    public function mozuDetail(Request $request, int $id, string $mozuName): View
    {
        $categories = $this->frontendCategoryQuery()->orderBy('name')->get(['id', 'name', 'image']);
        $this->prepareFrontendCategories($categories);
        $category = $categories->firstWhere('id', $id);
        abort_unless($category instanceof Category, 404);

        $categoryBanner = Banner::query()
            ->where('language', config('content_language.code'))
            ->where('type', 'full')
            ->where('position', 'category-detail-top-full')
            ->where('isActive', true)
            ->latest('id')
            ->first(['id', 'image']);

        if ($categoryBanner) {
            $categoryBanner->setAttribute(
                'image_available',
                filled($categoryBanner->image) && File::isFile(public_path($categoryBanner->image)),
            );
        }

        $tab = $request->query('tab') === 'articles' ? 'articles' : 'magazines';
        $search = Str::limit(trim((string) $request->query('q', '')), 100, '');
        $contentQuery = $tab === 'articles'
            ? $this->categoryArticleQuery($category, $search)
            : $this->categoryMagazineQuery($category, $search);
        $tableName = $tab === 'articles' ? (new Article)->getTable() : (new Magazine)->getTable();
        $yearExpression = $this->publicationYearExpression($tableName);
        $years = (clone $contentQuery)
            ->select(DB::raw($yearExpression.' as publication_year'))
            ->groupBy(DB::raw($yearExpression))
            ->orderByDesc('publication_year')
            ->paginate(4)
            ->withQueryString();
        $visibleYears = $years->getCollection()
            ->pluck('publication_year')
            ->values();

        $groupedContent = collect();

        if ($visibleYears->isNotEmpty()) {
            $content = $contentQuery
                ->whereIn(DB::raw($yearExpression), $visibleYears->all())
                ->orderByDesc('publish_date')
                ->orderByDesc('id')
                ->get();

            $content->each(function (Article|Magazine $item): void {
                if ($item instanceof Magazine) {
                    $item->setAttribute('frontend_url', $this->shumaraDetailUrl($item));

                    return;
                }

                $item->setAttribute('frontend_url', $this->mazmoonDetailUrl($item));
                $item->setAttribute('image_available', filled($item->image));
            });

            $groupedContent = $content->groupBy(fn (Article|Magazine $item): int => $item->publish_date->year);
        }

        $this->siteVisitService->record($request, $category);

        return view('frontend.mozu-detail', compact(
            'categories',
            'category',
            'categoryBanner',
            'groupedContent',
            'search',
            'tab',
            'years',
        ));
    }

    public function mazmoon_detail(Request $request, int $id, string $slug): View|RedirectResponse
    {
        $currentLanguage = config('content_language.code');
        $article = $this->publicArticleQuery()
            ->withCount('siteVisits')
            ->with([
                'authors' => fn ($query) => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture', 'isActive']),
                'categories' => fn ($query) => $query->where('categories.language', $currentLanguage)->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name', 'language', 'isActive']),
                'tags' => fn ($query) => $query->where('tags.language', $currentLanguage)->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name', 'language', 'isActive']),
                'relatedArticles' => fn ($query) => $query
                    ->whereKeyNot($id)
                    ->where('articles.language', $currentLanguage)
                    ->where('articles.isActive', true)
                    ->where('articles.status', Article::STATUS_PUBLISHED)
                    ->whereNotNull('articles.publish_date')
                    ->whereDate('articles.publish_date', '<=', today())
                    ->where(function (Builder $query): void {
                        $query->whereNull('articles.published_at')->orWhere('articles.published_at', '<=', now());
                    })
                    ->orderByDesc('articles.publish_date')
                    ->orderByDesc('articles.id')
                    ->limit(4)
                    ->select(['articles.id', 'title', 'publish_date', 'image']),
            ])
            ->findOrFail($id);

        if ($deniedResponse = $this->paidContentDeniedResponse(
            $article,
            SubscriptionContentAccessService::MODULE_ARTICLES,
            'اس مضمون تک رسائی کے لیے فعال سبسکرپشن درکار ہے۔',
            $request,
        )) {
            return $deniedResponse;
        }

        $frontendUser = $request->user('web');

        if ($frontendUser?->hasRole('user', 'web')) {
            $this->userContentVisitService->record($frontendUser, $article);
        }

        $this->siteVisitService->record($request, $article);

        $article->setAttribute('image_available', filled($article->image) && File::isFile(public_path($article->image)));
        $article->relatedArticles->each(function (Article $relatedArticle): void {
            $relatedArticle->setAttribute('frontend_url', $this->mazmoonDetailUrl($relatedArticle));
            $relatedArticle->setAttribute('image_available', filled($relatedArticle->image) && File::isFile(public_path($relatedArticle->image)));
        });

        $isBookmarked = $request->user('web')?->bookmarks()
            ->whereMorphedTo('bookmarkable', $article)
            ->exists() ?? false;

        return view('frontend.mazmoon-detail', compact('article', 'isBookmarked'));
    }

    public function shumara_detail(Request $request, int $id, string $slug): View|RedirectResponse
    {
        $currentLanguage = config('content_language.code');
        $magazine = $this->publicMagazineQuery($currentLanguage)
            ->withCount('siteVisits')
            ->with([
                'authors' => fn ($query) => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture', 'isActive']),
                'categories' => fn ($query) => $query->where('categories.language', $currentLanguage)->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name', 'language', 'isActive']),
                'tags' => fn ($query) => $query->where('tags.language', $currentLanguage)->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name', 'language', 'isActive']),
                'relatedMagazines' => fn ($query) => $query
                    ->where('magazines.language', $currentLanguage)
                    ->where('magazines.isActive', true)
                    ->where('magazines.status', Magazine::STATUS_PUBLISHED)
                    ->whereNotNull('magazines.publish_date')
                    ->whereDate('magazines.publish_date', '<=', today())
                    ->where(function ($query): void {
                        $query->whereNull('magazines.published_at')->orWhere('magazines.published_at', '<=', now());
                    })
                    ->orderByDesc('magazines.publish_date')
                    ->orderByDesc('magazines.id')
                    ->limit(4)
                    ->select(['magazines.id', 'title', 'issue_number', 'publish_date', 'cover_image']),
            ])
            ->findOrFail($id);

        if ($deniedResponse = $this->paidContentDeniedResponse(
            $magazine,
            SubscriptionContentAccessService::MODULE_MAGAZINES,
            'اس شمارے تک رسائی کے لیے فعال سبسکرپشن درکار ہے۔',
            $request,
        )) {
            return $deniedResponse;
        }

        $frontendUser = $request->user('web');

        if ($frontendUser?->hasRole('user', 'web')) {
            $this->userContentVisitService->record($frontendUser, $magazine);
        }

        $this->siteVisitService->record($request, $magazine);

        $magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($magazine));
        $magazine->relatedMagazines->each(function (Magazine $relatedMagazine): void {
            $relatedMagazine->setAttribute('frontend_url', $this->shumaraDetailUrl($relatedMagazine));
        });

        $pdfLocation = $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id);
        $magazineBookmark = $request->user('web')?->bookmarks()
            ->whereMorphedTo('bookmarkable', $magazine)
            ->first(['id', 'pdf_page']);
        $requestedPdfPage = filter_var($request->query('page'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $initialPdfPage = $requestedPdfPage === false ? 1 : $requestedPdfPage;

        return view('frontend.shumara-detail', compact(
            'initialPdfPage',
            'magazine',
            'magazineBookmark',
            'pdfLocation',
        ));
    }

    public function shumara_pdf(Request $request, int $id, string $slug): StreamedResponse|RedirectResponse
    {
        return $this->publicMagazinePdfResponse($request, $id, inline: true);
    }

    public function shumara_download(Request $request, int $id, string $slug): StreamedResponse|RedirectResponse
    {
        $magazine = $this->publicMagazineQuery()->findOrFail($id);
        abort_unless($magazine->is_downloadable, 404);

        if ($deniedResponse = $this->paidContentDeniedResponse(
            $magazine,
            SubscriptionContentAccessService::MODULE_MAGAZINES,
            'اس شمارے تک رسائی کے لیے فعال سبسکرپشن درکار ہے۔',
            $request,
        )) {
            return $deniedResponse;
        }

        return $this->publicMagazinePdfResponse($request, $magazine->id, inline: false);
    }

    private function shumaraDetailUrl(Magazine $magazine): string
    {
        $slug = Str::slug($magazine->title ?? '');

        return route('shumara-detail', [
            'id' => $magazine->id,
            'slug' => $slug ?: 'magazine-'.$magazine->id,
        ]);
    }

    private function mazmoonDetailUrl(Article $article): string
    {
        $slug = Str::slug($article->title ?? '');

        return route('mazmoon-detail', [
            'id' => $article->id,
            'slug' => $slug ?: 'article-'.$article->id,
        ]);
    }

    private function publicArticleQuery(?string $language = null): Builder
    {
        $language ??= config('content_language.code');

        return Article::query()
            ->where('language', $language)
            ->where('isActive', true)
            ->where('status', Article::STATUS_PUBLISHED)
            ->whereNotNull('publish_date')
            ->whereDate('publish_date', '<=', today())
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    private function publicMagazineQuery(?string $language = null): Builder
    {
        $language ??= config('content_language.code');

        return Magazine::query()
            ->where('language', $language)
            ->where('isActive', true)
            ->where('status', Magazine::STATUS_PUBLISHED)
            ->whereNotNull('publish_date')
            ->whereDate('publish_date', '<=', today())
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    private function publicMagazinePdfResponse(Request $request, int $magazineId, bool $inline): StreamedResponse|RedirectResponse
    {
        $magazine = $this->publicMagazineQuery()->findOrFail($magazineId);

        if ($deniedResponse = $this->paidContentDeniedResponse(
            $magazine,
            SubscriptionContentAccessService::MODULE_MAGAZINES,
            'اس شمارے تک رسائی کے لیے فعال سبسکرپشن درکار ہے۔',
            $request,
        )) {
            return $deniedResponse;
        }

        $location = $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id);

        abort_unless($location, 404);

        try {
            $stream = $this->mediaStorageService->openReadStream($location);
        } catch (RuntimeException) {
            abort(404, 'The requested Magazine PDF is not available.');
        }

        $fileName = filled($location->file_name) ? basename($location->file_name) : 'magazine.pdf';
        $mimeType = in_array($location->mime_type, ['application/pdf', 'application/x-pdf'], true)
            ? $location->mime_type
            : 'application/pdf';
        $disposition = HeaderUtils::makeDisposition(
            $inline ? HeaderUtils::DISPOSITION_INLINE : HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName,
            'magazine.pdf',
        );

        return response()->stream(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function paidContentDeniedResponse(
        Article|Magazine $content,
        string $module,
        string $message,
        Request $request,
    ): ?RedirectResponse {
        if ($this->subscriptionContentAccessService->isPubliclyAccessible($content)) {
            return null;
        }

        $user = auth('web')->user();

        if (! $user instanceof User) {
            $this->paidContentLoginIntentService->remember($request, $message);

            return to_route('frontend.home');
        }

        if ($this->subscriptionContentAccessService->canAccess($user, $content, $module)) {
            $this->paidContentLoginIntentService->clearReturnAfterSuccessfulAccess($request);

            return null;
        }

        $this->paidContentLoginIntentService->rememberReturnAfterPurchase($request);

        return to_route('front.subscriptions')->with('warning', $message);
    }

    public function index(Request $request): View
    {
        $currentLanguage = config('content_language.code');

        $belowSliderCards = HomeCard::query()
            ->where('is_active', true)
            ->where('position', 'below slider')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'image']);

        $aboveFooterCards = HomeCard::query()
            ->where('is_active', true)
            ->where('position', 'above footer')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'image']);

        foreach ([$belowSliderCards, $aboveFooterCards] as $homeCards) {
            $homeCards->each(function (HomeCard $homeCard): void {
                $imageAvailable = false;
                $imageFit = 'cover';

                if ($homeCard->image) {
                    $imagePath = public_path($homeCard->image);

                    if (File::isFile($imagePath)) {
                        $imageAvailable = true;
                        $dimensions = getimagesize($imagePath);

                        if ($dimensions !== false && ($dimensions[0] < 300 || $dimensions[1] < 180)) {
                            $imageFit = 'contain';
                        }
                    }
                }

                $homeCard->setAttribute('image_available', $imageAvailable);
                $homeCard->setAttribute('image_fit', $imageFit);
            });
        }

        $homeCardTitlePositions = HomeCardTitlePosition::query()
            ->where('language', config('content_language.code'))
            ->whereIn('card_position', ['below slider', 'above footer'])
            ->pluck('title_position', 'card_position');

        $homeSectionHeadings = HomePageSectionHeading::query()
            ->where('language', config('content_language.code'))
            ->whereIn('section_name', [
                'latest_articles',
                'below_slider_home_cards',
                'editorial',
                'weekly_magazine',
                'audio',
                'newsletter',
                'advertise_with_us',
                'categories',
                'above_footer_home_cards',
            ])
            ->get()
            ->keyBy('section_name');

        $categories = Category::query()
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->withCount([
                'magazines as public_magazines_count' => fn (Builder $query): Builder => $this->applyPublicMagazineConstraints($query),
                'articles as public_articles_count' => fn (Builder $query): Builder => $this->applyPublicArticleConstraints($query),
            ])
            ->orderBy('id')
            ->limit(8)
            ->get(['id', 'name', 'image']);

        $this->prepareFrontendCategories($categories);

        $latestArticles = Article::query()
            ->with('categories:id,name')
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->where('status', Article::STATUS_PUBLISHED)
            ->where('show_on_latest', true)
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->limit(3)
            ->get(['id', 'title', 'publish_date', 'image']);

        $editorialCenterArticles = Article::query()
            ->with('categories:id,name')
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->where('status', Article::STATUS_PUBLISHED)
            ->where('show_on_editorial_center', true)
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->limit(3)
            ->get(['id', 'title', 'publish_date', 'image']);

        $editorialFeaturedArticle = Article::query()
            ->with('categories:id,name')
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->where('status', Article::STATUS_PUBLISHED)
            ->where('show_on_editorial_featured', true)
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->first(['id', 'title', 'publish_date', 'short_description', 'image']);

        $topViewedMagazines = $this->publicMagazineQuery($currentLanguage)
            ->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image'])
            ->withCount('siteVisits')
            ->orderByDesc('site_visits_count')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $topViewedArticles = $this->publicArticleQuery($currentLanguage)
            ->select(['id', 'title', 'publish_date', 'image'])
            ->withCount('siteVisits')
            ->with(['categories' => fn ($query) => $query
                ->where('categories.language', $currentLanguage)
                ->where('categories.isActive', true)
                ->orderBy('name')
                ->select(['categories.id', 'name'])])
            ->orderByDesc('site_visits_count')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $topSearchedMagazines = $this->publicMagazineQuery($currentLanguage)
            ->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image'])
            ->has('searchContents')
            ->withCount('searchContents')
            ->orderByDesc('search_contents_count')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $topSearchedArticles = $this->publicArticleQuery($currentLanguage)
            ->select(['id', 'title', 'publish_date', 'image'])
            ->has('searchContents')
            ->withCount('searchContents')
            ->with(['categories' => fn ($query) => $query
                ->where('categories.language', $currentLanguage)
                ->where('categories.isActive', true)
                ->orderBy('name')
                ->select(['categories.id', 'name'])])
            ->orderByDesc('search_contents_count')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $topViewedMagazines->each(function (Magazine $magazine): void {
            $magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($magazine));
            $magazine->setAttribute('image_available', filled($magazine->cover_image) && File::isFile(public_path($magazine->cover_image)));
        });

        $topViewedArticles->each(function (Article $article): void {
            $article->setAttribute('frontend_url', $this->mazmoonDetailUrl($article));
            $article->setAttribute('image_available', filled($article->image) && File::isFile(public_path($article->image)));
        });

        $topSearchedMagazines->each(function (Magazine $magazine): void {
            $magazine->setAttribute('frontend_url', $this->shumaraDetailUrl($magazine));
            $magazine->setAttribute('image_available', filled($magazine->cover_image) && File::isFile(public_path($magazine->cover_image)));
        });

        $topSearchedArticles->each(function (Article $article): void {
            $article->setAttribute('frontend_url', $this->mazmoonDetailUrl($article));
            $article->setAttribute('image_available', filled($article->image) && File::isFile(public_path($article->image)));
        });

        $tazaShumara = TazaShumara::query()
            ->with('magazine:id,title,issue_number,publish_date,cover_image')
            ->where('language', $currentLanguage)
            ->where('is_active', true)
            ->first(['id', 'magazine_id', 'cover_image', 'show_title']);

        if ($tazaShumara?->magazine) {
            $tazaShumara->setAttribute(
                'magazine_url',
                $this->shumaraDetailUrl($tazaShumara->magazine),
            );
        }

        $sideBySideBanner = Banner::query()
            ->where('language', $currentLanguage)
            ->where('type', 'side-by-side')
            ->where('isActive', true)
            ->latest('id')
            ->first(['id', 'image', 'image_2']);

        if ($sideBySideBanner) {
            $sideBySideBanner->setAttribute(
                'image_available',
                filled($sideBySideBanner->image) && File::isFile(public_path($sideBySideBanner->image)),
            );
            $sideBySideBanner->setAttribute(
                'image_2_available',
                filled($sideBySideBanner->image_2) && File::isFile(public_path($sideBySideBanner->image_2)),
            );
        }

        $homepageArticles = $latestArticles
            ->concat($editorialCenterArticles)
            ->when($editorialFeaturedArticle, fn ($articles) => $articles->push($editorialFeaturedArticle));

        $homepageArticles->each(function (Article $article): void {
            $article->setAttribute('frontend_url', $this->mazmoonDetailUrl($article));
            $article->setAttribute(
                'image_available',
                filled($article->image) && File::isFile(public_path($article->image)),
            );
        });

        $resolveTitlePosition = static fn (mixed $position): string => in_array($position, ['center', 'left', 'right'], true)
            ? $position
            : 'right';

        $this->siteVisitService->record($request);

        return view('frontend.index', [
            'aboveFooterTitlePosition' => $resolveTitlePosition($homeCardTitlePositions->get('above footer')),
            'aboveFooterCards' => $aboveFooterCards,
            'belowSliderTitlePosition' => $resolveTitlePosition($homeCardTitlePositions->get('below slider')),
            'belowSliderCards' => $belowSliderCards,
            'categories' => $categories,
            'editorialCenterArticles' => $editorialCenterArticles,
            'editorialFeaturedArticle' => $editorialFeaturedArticle,
            'homeSectionHeadings' => $homeSectionHeadings,
            'latestArticles' => $latestArticles,
            'sideBySideBanner' => $sideBySideBanner,
            'tazaShumara' => $tazaShumara,
            'topViewedArticles' => $topViewedArticles,
            'topViewedMagazines' => $topViewedMagazines,
            'topSearchedArticles' => $topSearchedArticles,
            'topSearchedMagazines' => $topSearchedMagazines,
            'sliders' => Slider::query()
                ->where('isActive', true)
                ->orderBy('id')
                ->get(),
        ]);
    }

    private function frontendCategoryQuery(bool $withContentCounts = false): Builder
    {
        return Category::query()
            ->where('language', config('content_language.code'))
            ->where('isActive', true)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->when($withContentCounts, fn (Builder $query): Builder => $query->withCount([
                'magazines as public_magazines_count' => fn (Builder $query): Builder => $this->applyPublicMagazineConstraints($query),
                'articles as public_articles_count' => fn (Builder $query): Builder => $this->applyPublicArticleConstraints($query),
            ]));
    }

    private function prepareSubscriptionProduct(SubscriptionProduct $product): void
    {
        $durationUnit = match ($product->duration_unit) {
            'day', 'days' => 'دن',
            'week', 'weeks' => 'ہفتہ',
            'month', 'months' => 'ماہ',
            'year', 'years' => 'سال',
            default => (string) $product->duration_unit,
        };

        $product->setAttribute('frontend_currency', $product->currency?->code ?: $product->currency?->symbol);
        $price = (float) $product->price;
        $discountValue = (float) $product->discount_value;
        $discountAmount = match ($product->discount_type) {
            'percentage' => $price * min($discountValue, 100) / 100,
            'fixed' => min($discountValue, $price),
            default => 0,
        };
        $hasDiscount = $discountAmount > 0;

        $product->setAttribute('frontend_price', $this->formatSubscriptionPrice($price));
        $product->setAttribute('frontend_has_discount', $hasDiscount);
        $product->setAttribute('frontend_discounted_price', $this->formatSubscriptionPrice($product->effectivePrice()));
        $product->setAttribute('frontend_discount_label', match ($product->discount_type) {
            'percentage' => $this->formatSubscriptionPrice(min($discountValue, 100)).'% رعایت',
            'fixed' => $product->frontend_currency.' '.$this->formatSubscriptionPrice(min($discountValue, $price)).' رعایت',
            default => null,
        });
        $product->setAttribute('frontend_duration', trim($product->duration_value.' '.$durationUnit));
        $product->setAttribute('frontend_description', filled($product->duration_value) && filled($product->duration_unit)
            ? trim($product->duration_value.' '.$durationUnit.' کے لیے مکمل رسائی')
            : null);
        $product->setAttribute('frontend_membership_name', $product->getAttribute('name_ur')
            ?: $product->getAttribute('name_en')
            ?: $product->name);
        $product->setAttribute('frontend_membership_description', filled($product->duration_value) && filled($product->duration_unit)
            ? trim($product->duration_value.' '.$durationUnit.' کے لیے منتخب ماڈیولز تک رسائی')
            : 'منتخب ماڈیولز تک رسائی');
    }

    /** @param Collection<int, SubscriptionProduct> $products */
    private function prepareSubscriptionPurchaseStates(Collection $products): void
    {
        $user = auth('web')->user();

        if ($user === null) {
            return;
        }

        $states = $this->subscriptionRenewalService->statesFor($user, $products);

        $products->each(function (SubscriptionProduct $product) use ($states): void {
            $purchaseState = $states->get($product->getKey());
            $state = $purchaseState['state'];

            $product->setAttribute('frontend_purchase_state', $state);
            $product->setAttribute('frontend_purchase_allowed', $this->subscriptionPurchaseStateAllowsCheckout($state));
            $product->setAttribute('frontend_purchase_label', match ($state) {
                SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE => 'تجدید کریں',
                SubscriptionRenewalService::STATE_ACTIVE_LOCKED => 'فعال سبسکرپشن',
                SubscriptionRenewalService::STATE_RENEWAL_ALREADY_SCHEDULED => 'تجدید ہو چکی ہے',
                SubscriptionRenewalService::STATE_PAYMENT_PENDING => 'ادائیگی زیرِ جائزہ',
                default => 'ابھی سبسکرائب کریں',
            });
            $product->setAttribute('frontend_purchase_hint', match ($state) {
                SubscriptionRenewalService::STATE_ACTIVE_LOCKED => $purchaseState['renewal_window_days'] > 0
                    ? "تجدید {$purchaseState['renewal_window_days']} دن باقی ہونے پر دستیاب ہوگی"
                    : 'قبل از وقت تجدید فی الحال دستیاب نہیں ہے',
                SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE => $purchaseState['current_subscription']->end_date !== null
                    ? 'اختتام: '.$purchaseState['current_subscription']->end_date->format('d M Y')
                    : null,
                SubscriptionRenewalService::STATE_RENEWAL_ALREADY_SCHEDULED => $purchaseState['future_subscription']->start_date !== null
                    ? 'نئی مدت کا آغاز: '.$purchaseState['future_subscription']->start_date->format('d M Y')
                    : null,
                SubscriptionRenewalService::STATE_PAYMENT_PENDING => 'آپ کی ادائیگی کی درخواست پہلے ہی زیرِ جائزہ ہے',
                default => null,
            });
        });
    }

    private function subscriptionPurchaseStateAllowsCheckout(string $state): bool
    {
        return in_array($state, [
            SubscriptionRenewalService::STATE_AVAILABLE,
            SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE,
        ], true);
    }

    private function subscriptionPurchaseStateWarning(string $state): string
    {
        return $state === SubscriptionRenewalService::STATE_RENEWAL_ALREADY_SCHEDULED
            ? 'اس سبسکرپشن کی تجدید پہلے ہی ہو چکی ہے۔'
            : ($state === SubscriptionRenewalService::STATE_PAYMENT_PENDING
                ? 'Your payment request is already pending verification.'
                : 'یہ سبسکرپشن ابھی فعال ہے، تجدید مقررہ مدت میں دستیاب ہوگی۔');
    }

    private function isAvailableSubscriptionProduct(SubscriptionProduct $subscriptionProduct): bool
    {
        return SubscriptionProduct::query()
            ->availableToFrontend()
            ->whereKey($subscriptionProduct->getKey())
            ->exists();
    }

    private function frontendSubscriptionTypeLabel(SubscriptionType $type): string
    {
        return match (Str::lower(trim((string) $type->name))) {
            'magazine', 'magazines' => 'شمارہ منصوبہ',
            'article', 'articles' => 'مضمون منصوبہ',
            'audio' => 'آڈیو منصوبہ',
            default => (string) $type->name,
        };
    }

    private function formatSubscriptionPrice(float $price): string
    {
        return rtrim(rtrim(number_format($price, 2, '.', ','), '0'), '.');
    }

    private function prepareMembershipType(SubscriptionType $type): void
    {
        [$label, $icon] = match (Str::lower(trim((string) $type->name))) {
            'magazine', 'magazines' => ['ہفتہ وار میگزین', 'fa-book-open'],
            'article', 'articles' => ['مضامین', 'fa-file-lines'],
            'audio' => ['آڈیو', 'fa-headphones'],
            default => [(string) $type->name, 'fa-puzzle-piece'],
        };

        $type->setAttribute('frontend_membership_label', $label);
        $type->setAttribute('frontend_membership_icon', $icon);
    }

    /** @return array<string, bool> */
    private function authorDefaultVisibility(): array
    {
        $storedSettings = AuthorGeneralSetting::query()
            ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
            ->get(['column_name', 'isView'])
            ->keyBy('column_name');

        $visibility = [];

        foreach (AuthorGeneralSetting::DEFAULT_VISIBILITY as $field => $defaultValue) {
            $visibility[$field] = (bool) ($storedSettings->get($field)?->isView ?? $defaultValue);
        }

        return $visibility;
    }

    private function frontendSlug(?string $value): string
    {
        return Str::of($value ?? '')
            ->squish()
            ->replaceMatches('/[^\pL\pN]+/u', '-')
            ->trim('-')
            ->lower()
            ->toString();
    }

    /** @param array<string, bool> $defaultVisibility */
    private function prepareFrontendAuthor(Author $author, array $defaultVisibility): void
    {
        $visibility = $author->effectiveVisibility($defaultVisibility);
        $slug = $visibility['name'] ? $this->frontendSlug($author->name) : '';

        $author->setAttribute('frontend_visibility', $visibility);
        $author->setAttribute(
            'picture_available',
            filled($author->picture) && File::isFile(public_path($author->picture)),
        );
        $author->setAttribute('frontend_url', route('front.mazmoon-nigaar.detail', [
            'id' => $author->id,
            'slug' => $slug ?: 'author-'.$author->id,
        ]));
    }

    private function applyPublicMagazineConstraints(Builder $query): Builder
    {
        return $query
            ->where('magazines.language', config('content_language.code'))
            ->where('magazines.isActive', true)
            ->where('magazines.status', Magazine::STATUS_PUBLISHED)
            ->whereNotNull('magazines.publish_date')
            ->whereDate('magazines.publish_date', '<=', today())
            ->where(function (Builder $query): void {
                $query->whereNull('magazines.published_at')->orWhere('magazines.published_at', '<=', now());
            });
    }

    private function applyPublicArticleConstraints(Builder $query): Builder
    {
        return $query
            ->where('articles.language', config('content_language.code'))
            ->where('articles.isActive', true)
            ->where('articles.status', Article::STATUS_PUBLISHED)
            ->whereNotNull('articles.publish_date')
            ->whereDate('articles.publish_date', '<=', today())
            ->where(function (Builder $query): void {
                $query->whereNull('articles.published_at')->orWhere('articles.published_at', '<=', now());
            });
    }

    private function categoryMagazineQuery(Category $category, string $search): Builder
    {
        return $this->publicMagazineQuery()
            ->whereHas('categories', fn (Builder $query): Builder => $query->whereKey($category->id))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';
                $query->where(function (Builder $query) use ($searchPattern): void {
                    $query->where('title', 'like', $searchPattern)
                        ->orWhere('description', 'like', $searchPattern)
                        ->orWhere('issue_number', 'like', $searchPattern);
                });
            })
            ->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image', 'description']);
    }

    private function categoryArticleQuery(Category $category, string $search): Builder
    {
        return $this->publicArticleQuery()
            ->whereHas('categories', fn (Builder $query): Builder => $query->whereKey($category->id))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $searchPattern = '%'.$search.'%';
                $query->where(function (Builder $query) use ($searchPattern): void {
                    $query->where('title', 'like', $searchPattern)
                        ->orWhere('short_description', 'like', $searchPattern)
                        ->orWhere('issue_number', 'like', $searchPattern);
                });
            })
            ->select(['id', 'title', 'issue_number', 'publish_date', 'image', 'short_description']);
    }

    private function publicationYearExpression(string $tableName): string
    {
        $publishDate = $tableName.'.publish_date';

        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', {$publishDate})",
            'pgsql' => "EXTRACT(YEAR FROM {$publishDate})",
            default => "YEAR({$publishDate})",
        };
    }

    /** @param Collection<int, Category> $categories */
    private function prepareFrontendCategories(Collection $categories): void
    {
        $categorySlugs = MetaTag::query()
            ->where('table_name', (new Category)->getTable())
            ->where('language', config('content_language.code'))
            ->whereIn('table_id', $categories->modelKeys())
            ->pluck('slug_url', 'table_id');

        $categories->each(function (Category $category) use ($categorySlugs): void {
            $slug = $categorySlugs->get($category->id) ?: Str::of($category->name)
                ->squish()
                ->replaceMatches('/[^\pL\pN]+/u', '-')
                ->trim('-')
                ->lower()
                ->toString();

            $category->setAttribute('frontend_url', route('mozu-detail', [
                'id' => $category->id,
                'mozuName' => $slug ?: 'mozu-'.$category->id,
            ]));
            $category->setAttribute('image_available', filled($category->image));
            $category->setAttribute(
                'total_content_count',
                (int) ($category->public_magazines_count ?? 0) + (int) ($category->public_articles_count ?? 0),
            );
        });
    }

    private function infoPage(string $slug): View
    {
        $definition = config('info_pages.pages.'.$slug);
        abort_unless(is_array($definition), 404);
        $configuredLanguage = GeneralSetting::query()
            ->with('defaultLanguage:id,code,is_active')
            ->first()?->defaultLanguage;
        $language = $configuredLanguage?->is_active === true
            && in_array($configuredLanguage->code, config('info_pages.languages'), true)
            ? $configuredLanguage->code
            : config('content_language.code');
        $infoPage = InfoPage::query()->forPage($definition['key'], $language)->first();
        $title = $definition['titles'][$language];

        return view('frontend.info-page', compact('infoPage', 'language', 'title'))
            ->with('infoPageLanguage', $language);
    }
}
