<?php

namespace App\Observers;

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
use App\Models\Video;
use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminActivityObserver
{
    public function __construct(private ActivityLogService $activityLogService) {}

    public function created(Model $model): void
    {
        $newValues = $model->getAttributes();

        $this->activityLogService->log(
            $this->module($model),
            'created',
            $model,
            'Created '.$this->subjectDescription($model).'.',
            null,
            $newValues,
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $oldValues = collect(array_keys($changes))
            ->mapWithKeys(fn (string $key): array => [$key => $model->getRawOriginal($key)])
            ->all();
        $action = $this->updateAction($model, $changes);

        $this->activityLogService->log(
            $this->module($model),
            $action,
            $model,
            $this->updateDescription($model, $action, $changes),
            $oldValues,
            $changes,
        );
    }

    public function deleted(Model $model): void
    {
        $oldValues = $model->getAttributes();

        $this->activityLogService->log(
            $this->module($model),
            'deleted',
            $model,
            'Deleted '.$this->subjectDescription($model).'.',
            $oldValues,
            null,
        );
    }

    /** @param array<string, mixed> $changes */
    private function updateAction(Model $model, array $changes): string
    {
        if (($model instanceof Magazine || $model instanceof Article || $model instanceof Consultancy || $model instanceof Course || $model instanceof Video)
            && ($changes['status'] ?? null) === 'published') {
            return 'published';
        }

        if (array_key_exists('isActive', $changes) || array_key_exists('is_active', $changes) || array_key_exists('isactive', $changes)) {
            return 'status_changed';
        }

        return 'updated';
    }

    /** @param array<string, mixed> $changes */
    private function updateDescription(Model $model, string $action, array $changes): string
    {
        if ($action === 'published') {
            return 'Published '.$this->subjectDescription($model).'.';
        }

        if ($action === 'status_changed') {
            $isActive = (bool) ($changes['isActive'] ?? $changes['is_active'] ?? $changes['isactive'] ?? false);

            return ($isActive ? 'Activated ' : 'Deactivated ').$this->subjectDescription($model).'.';
        }

        return 'Updated '.$this->subjectDescription($model).'.';
    }

    private function module(Model $model): string
    {
        return match ($model::class) {
            About::class => 'abouts',
            Ad::class => 'ads',
            AdRequest::class, AdRequestPlacement::class => 'ad_requests',
            AskQuestion::class => 'ask_questions',
            HomeCard::class => 'home_cards',
            HomeSection::class => 'home_sections',
            InfoPage::class => 'info_pages',
            GeneralSetting::class => 'general_settings',
            Slider::class => 'sliders',
            Banner::class => 'banners',
            Faq::class => 'faqs',
            FaqCategory::class => 'faq_categories',
            Author::class => 'authors',
            AuthorGeneralSetting::class => 'author_settings',
            Tags::class => 'tags',
            Category::class => 'categories',
            MetaTag::class => 'meta_tags',
            NewsletterSubscriber::class => 'newsletter_subscribers',
            NewsletterCampaign::class => 'newsletter_campaigns',
            PaymentAccount::class => 'payment_accounts',
            Service::class => 'services',
            Currency::class => 'currencies',
            Contact::class => 'contacts',
            Consultancy::class => 'consultancies',
            Course::class => 'courses',
            SubscriptionProduct::class => $model->product_for === SubscriptionProduct::FOR_MEMBERSHIP
                ? 'memberships'
                : 'subscription_plans',
            SubscriptionNotificationSetting::class => 'subscription_reminder_settings',
            Magazine::class => 'magazines',
            Article::class => 'articles',
            TazaShumara::class => 'taza_shumara',
            TazaShumaraArticle::class => 'taza_shumara_articles',
            Video::class => 'videos',
            default => Str::snake(class_basename($model)),
        };
    }

    private function subjectDescription(Model $model): string
    {
        $type = Str::of($this->module($model))->replace('_', ' ')->singular()->title();
        $identifier = collect(['request_no', 'question_no', 'name', 'title', 'account_title', 'main_heading', 'question', 'code', 'column_name'])
            ->map(fn (string $attribute): mixed => $model->getAttribute($attribute))
            ->first(fn (mixed $value): bool => filled($value));

        return $identifier === null
            ? $type.' #'.$model->getKey()
            : $type.' "'.Str::limit((string) $identifier, 150).'"';
    }
}
