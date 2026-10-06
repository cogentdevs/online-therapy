<?php

namespace App\Providers;

use App\Models\About;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use App\Models\Article;
use App\Models\AskQuestion;
use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Consultancy;
use App\Models\Contact;
use App\Models\Course;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\GeneralSetting;
use App\Models\HomeCard;
use App\Models\HomeSection;
use App\Models\InfoPage;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MetaTag;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\PaymentAccount;
use App\Models\Service;
use App\Models\Slider;
use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\Tags;
use App\Models\TazaShumara;
use App\Models\TazaShumaraArticle;
use App\Models\User;
use App\Models\Video;
use App\Observers\AdminActivityObserver;
use App\Services\Authorization\AdminUserPermissionService;
use App\Services\FrontendSharedDataService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as BladeView;
use Throwable;
use WeakMap;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->activityLogModels() as $modelClass) {
            $modelClass::observe(AdminActivityObserver::class);
        }

        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasRole('super-admin')) {
                return true;
            }

            $permissions = app(AdminUserPermissionService::class);
            $customModeDecision = $permissions->customModeDecision($user, $ability);

            if ($customModeDecision !== null) {
                return $customModeDecision;
            }

            return $user->checkPermissionTo($ability) ?: null;
        });

        $sharedDataByRequest = new WeakMap;

        View::composer('*', function (BladeView $view) use ($sharedDataByRequest): void {
            $request = request();

            if (! isset($sharedDataByRequest[$request])) {
                $sharedDataByRequest[$request] = $this->globalViewData();
            }

            foreach ($sharedDataByRequest[$request] as $key => $value) {
                if (! array_key_exists($key, $view->getData())) {
                    $view->with($key, $value);
                }
            }
        });
    }

    /** @return array<int, class-string> */
    private function activityLogModels(): array
    {
        return [
            About::class,
            Ad::class,
            AdRequest::class,
            AdRequestPlacement::class,
            AskQuestion::class,
            HomeCard::class,
            HomeSection::class,
            InfoPage::class,
            GeneralSetting::class,
            Slider::class,
            Banner::class,
            Faq::class,
            FaqCategory::class,
            Author::class,
            AuthorGeneralSetting::class,
            Tags::class,
            Category::class,
            MetaTag::class,
            NewsletterSubscriber::class,
            NewsletterCampaign::class,
            PaymentAccount::class,
            Service::class,
            Currency::class,
            Contact::class,
            Consultancy::class,
            Course::class,
            SubscriptionProduct::class,
            SubscriptionNotificationSetting::class,
            Magazine::class,
            Article::class,
            TazaShumara::class,
            TazaShumaraArticle::class,
            Video::class,
        ];
    }

    /**
     * @return array{generalSetting: GeneralSetting|null, activeLanguages: Collection<int, Language>, navigationCategories: Collection<int, Category>, footerCategories: Collection<int, Category>, frontendAds: Collection<string, Collection<string, Ad>>}
     */
    private function globalViewData(): array
    {
        $sharedData = app(FrontendSharedDataService::class);
        $generalSetting = $sharedData->generalSetting();
        $activeLanguages = $sharedData->activeLanguages();
        $navigationCategories = $sharedData->navigationCategories();
        $footerCategories = $navigationCategories->take(5);
        $frontendAds = collect();

        try {
            if (Schema::hasTable((new Ad)->getTable())) {
                $frontendAds = $this->frontendAds();
            }
        } catch (Throwable) {
            $frontendAds = collect();
        }

        return compact('generalSetting', 'activeLanguages', 'navigationCategories', 'footerCategories', 'frontendAds');
    }

    /** @return Collection<string, Collection<string, Ad>> */
    private function frontendAds(): Collection
    {
        $routeName = request()->route()?->getName();

        if (! is_string($routeName) || Str::startsWith($routeName, 'admin.')) {
            return collect();
        }

        $placements = ['header' => ['header_ad']];

        if ($routeName === 'frontend.home') {
            $placements['home'] = ['home_horizontal_large', 'home_horizontal_small'];
        } elseif ($routeName === 'taza.shumara') {
            $placements['taza_shumara'] = [
                'taza_sidebar_1_normal',
                'taza_sidebar_1_tall',
                'taza_sidebar_2_normal',
                'taza_sidebar_2_tall',
            ];
        }

        $currentLanguage = config('content_language.code');

        return Ad::query()
            ->currentlyEligible()
            ->where(function ($query) use ($placements): void {
                foreach ($placements as $page => $places) {
                    $query->orWhere(function ($query) use ($page, $places): void {
                        $query->where('page_name', $page)->whereIn('place', $places);
                    });
                }
            })
            ->where(function ($query) use ($currentLanguage): void {
                $query->where('language', $currentLanguage)->orWhereNull('language');
            })
            ->where(function ($query): void {
                $query->where(function ($query): void {
                    $query->whereNotNull('google_ad_code')->where('google_ad_code', '!=', '');
                })->orWhere(function ($query): void {
                    $query->whereNotNull('ad_image')->where('ad_image', '!=', '');
                });
            })
            ->orderByRaw('CASE WHEN language = ? THEN 0 ELSE 1 END', [$currentLanguage])
            ->orderByDesc('id')
            ->get(['id', 'language', 'title', 'page_name', 'place', 'ad_image', 'ad_url', 'google_ad_code', 'start_date', 'expiry_date', 'isActive'])
            ->unique(fn (Ad $ad): string => $ad->page_name.':'.$ad->place)
            ->groupBy('page_name')
            ->map(fn (Collection $ads): Collection => $ads->keyBy('place'));
    }
}
