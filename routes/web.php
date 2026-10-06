<?php

use App\Http\Controllers\AdClickController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\AdvertisingRequestController as AdminAdvertisingRequestController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AskQuestionController as AdminAskQuestionController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\AuthorSettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConsultancyController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\HomeArticleController;
use App\Http\Controllers\Admin\HomeCardController;
use App\Http\Controllers\Admin\HomePageSectionHeadingController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\InfoPageController;
use App\Http\Controllers\Admin\MagazineController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\MetaTagController;
use App\Http\Controllers\Admin\NewsletterCampaignController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\PaymentAccountsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteAnalyticsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubscriptionNotificationSettingController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\TazaShumaraController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSubscriptionController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Frontend\AccountAdvertisingRequestController;
use App\Http\Controllers\Frontend\AccountAskQuestionController;
use App\Http\Controllers\Frontend\AdvertisingRequestController;
use App\Http\Controllers\Frontend\AskQuestionController;
use App\Http\Controllers\Frontend\BookmarkController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\NewsletterUnsubscribeController;
use App\Http\Controllers\Frontend\SearchContentController;
use App\Http\Controllers\Frontend\SiteVisitController;
use App\Http\Controllers\Frontend\SubscriptionInvoiceController;
use App\Http\Controllers\Frontend\UserController as FrontendUserController;
use App\Http\Controllers\Webhooks\BrevoNewsletterWebhookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Front website routes
Route::get('/', [FrontController::class, 'index'])->name('frontend.home');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->middleware('throttle:10,1')->name('front.newsletter.subscribe');
Route::get('/newsletter/unsubscribe/success', [NewsletterUnsubscribeController::class, 'success'])->name('front.newsletter.unsubscribe.success');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterUnsubscribeController::class, 'show'])->where('token', '[A-Za-z0-9]{64}')->name('front.newsletter.unsubscribe.show');
Route::post('/newsletter/unsubscribe/{token}', [NewsletterUnsubscribeController::class, 'store'])->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:10,1')->name('front.newsletter.unsubscribe.store');
Route::post('/webhooks/brevo/newsletter', BrevoNewsletterWebhookController::class)->middleware('throttle:120,1')->name('webhooks.brevo.newsletter');
Route::get('/search-content/{type}/{id}', SearchContentController::class)
    ->whereIn('type', ['article', 'magazine'])
    ->whereNumber('id')
    ->middleware('signed')
    ->name('front.search-content.track');
Route::get('/subscriptions', [FrontController::class, 'subscriptions'])->name('front.subscriptions');
Route::get('/consultancies', [FrontController::class, 'consultancies'])->name('front.consultancies.index');
Route::get('/consultancies/{consultancy}', [FrontController::class, 'consultancy'])->whereNumber('consultancy')->name('front.consultancies.show');
Route::get('/courses', [FrontController::class, 'courses'])->name('front.courses.index');
Route::get('/courses/{course}', [FrontController::class, 'course'])->whereNumber('course')->name('front.courses.show');
Route::post('/subscriptions/{subscriptionProduct}/intent', [FrontController::class, 'storeSubscriptionIntent'])->name('front.subscriptions.intent');
Route::delete('/subscriptions/intent', [FrontController::class, 'clearSubscriptionIntent'])->name('front.subscriptions.intent.clear');
Route::delete('/paid-content/intent', [FrontController::class, 'clearPaidContentIntent'])->name('front.paid-content.intent.clear');
Route::get('/subscriptions/{subscriptionProduct}/checkout', [FrontController::class, 'subscriptionCheckout'])->middleware('front-account')->name('front.subscriptions.checkout');
Route::post('/subscriptions/{subscriptionProduct}/checkout', [FrontController::class, 'storeSubscriptionCheckout'])->middleware('front-account')->name('front.subscriptions.checkout.store');
Route::get('/register', [FrontendUserController::class, 'register'])->middleware('guest:web')->name('front.register');
Route::post('/register', [FrontendUserController::class, 'store'])->middleware(['guest', 'throttle:6,1'])->name('front.register.store');
Route::get('/account/activate', [FrontendUserController::class, 'activation'])->middleware('throttle:30,1')->name('front.account.activation');
Route::post('/account/activate', [FrontendUserController::class, 'activate'])->middleware('throttle:10,1')->name('front.account.activation.store');
Route::get('/login', [FrontendUserController::class, 'login'])->middleware('guest:web')->name('front.login');
Route::post('/login', [FrontendUserController::class, 'authenticate'])->middleware('guest:web')->name('front.login.store');
Route::get('/two-factor-challenge', [FrontendUserController::class, 'loginTwoFactorChallenge'])->middleware('guest:web')->name('front.two-factor.challenge');
Route::post('/two-factor-challenge', [FrontendUserController::class, 'verifyLoginTwoFactorChallenge'])->middleware('guest:web')->name('front.two-factor.verify');
Route::post('/two-factor-challenge/resend', [FrontendUserController::class, 'resendLoginTwoFactorChallenge'])->middleware('guest:web')->name('front.two-factor.resend');
Route::get('/forgot-password', [FrontendUserController::class, 'forgotPassword'])->middleware('guest:web')->name('front.password.request');
Route::post('/forgot-password', [FrontendUserController::class, 'emailPasswordResetLink'])->middleware('guest:web')->name('front.password.email');
Route::get('/reset-password/{token}', [FrontendUserController::class, 'resetPassword'])->middleware('guest:web')->name('front.password.reset');
Route::post('/reset-password', [FrontendUserController::class, 'updatePassword'])->middleware('guest:web')->name('front.password.update');
Route::post('/logout', [FrontendUserController::class, 'logout'])->middleware('auth:web')->name('front.logout');
Route::get('/account', [FrontendUserController::class, 'account'])->middleware('front-account')->name('front.account');
Route::get('/account/bookmarks', [FrontendUserController::class, 'bookmarks'])->middleware('front-account')->name('front.account.bookmarks');
Route::get('/account/recent-activities', [FrontendUserController::class, 'recentActivities'])->middleware('front-account')->name('front.account.recent-activities');
Route::delete('/account/bookmarks/{bookmark}', [BookmarkController::class, 'destroyAccountBookmark'])
    ->whereNumber('bookmark')
    ->middleware('front-account')
    ->name('front.account.bookmarks.destroy');
Route::get('/account/profile', [FrontendUserController::class, 'profile'])->middleware('front-account')->name('front.account.profile');
Route::patch('/account/profile', [FrontendUserController::class, 'updateProfile'])->middleware('front-account')->name('front.account.profile.update');
Route::get('/account/subscriptions', [FrontendUserController::class, 'subscriptions'])->middleware('front-account')->name('front.account.subscriptions');
Route::get('/account/subscriptions/{subscription}/videos', [FrontendUserController::class, 'planVideos'])
    ->whereNumber('subscription')
    ->middleware('front-account')
    ->name('front.account.subscriptions.videos');
Route::get('/account/subscriptions/{subscription}/videos/{video}/watch', [FrontendUserController::class, 'watchPlanVideo'])
    ->whereNumber('subscription')
    ->whereNumber('video')
    ->middleware('front-account')
    ->name('front.account.subscriptions.videos.watch');
Route::get('/account/subscriptions/{subscription}/resubmit', [FrontController::class, 'resubmitSubscriptionPayment'])
    ->whereNumber('subscription')
    ->middleware('front-account')
    ->name('front.account.subscriptions.resubmit');
Route::post('/account/subscriptions/{subscription}/resubmit', [FrontController::class, 'storeResubmittedSubscriptionPayment'])
    ->whereNumber('subscription')
    ->middleware('front-account')
    ->name('front.account.subscriptions.resubmit.store');
Route::get('/account/advertising-requests', [AccountAdvertisingRequestController::class, 'index'])->middleware('front-account')->name('front.account.advertising-requests.index');
Route::get('/account/advertising-requests/{adRequest}', [AccountAdvertisingRequestController::class, 'show'])->whereNumber('adRequest')->middleware('front-account')->name('front.account.advertising-requests.show');
Route::get('/account/questions', [AccountAskQuestionController::class, 'index'])->middleware('front-account')->name('front.account.questions.index');
Route::get('/account/questions/{askQuestion}', [AccountAskQuestionController::class, 'show'])->whereNumber('askQuestion')->middleware('front-account')->name('front.account.questions.show');
Route::get('/account/subscriptions/{subscription}/invoice', [SubscriptionInvoiceController::class, 'view'])
    ->whereNumber('subscription')
    ->middleware('front-account')
    ->name('front.account.subscriptions.invoice.view');
Route::get('/account/subscriptions/{subscription}/invoice/download', [SubscriptionInvoiceController::class, 'download'])
    ->whereNumber('subscription')
    ->middleware('front-account')
    ->name('front.account.subscriptions.invoice.download');
Route::get('/account/two-factor-authentication', [FrontendUserController::class, 'twoFactorAuthentication'])->middleware('front-account')->name('front.account.two-factor-authentication');
Route::post('/account/two-factor-authentication/send-otp', [FrontendUserController::class, 'sendTwoFactorOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.send');
Route::get('/account/two-factor-authentication/verify', [FrontendUserController::class, 'verifyTwoFactorAuthentication'])->middleware('front-account')->name('front.account.two-factor-authentication.verify');
Route::post('/account/two-factor-authentication/verify', [FrontendUserController::class, 'storeTwoFactorVerification'])->middleware('front-account')->name('front.account.two-factor-authentication.verify.store');
Route::post('/account/two-factor-authentication/disable/send-otp', [FrontendUserController::class, 'sendDisableTwoFactorOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.disable.send');
Route::get('/account/two-factor-authentication/disable/verify', [FrontendUserController::class, 'disableTwoFactorVerification'])->middleware('front-account')->name('front.account.two-factor-authentication.disable.challenge');
Route::post('/account/two-factor-authentication/disable/verify', [FrontendUserController::class, 'verifyDisableTwoFactorOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.disable.verify');
Route::post('/account/two-factor-authentication/disable/resend', [FrontendUserController::class, 'resendDisableTwoFactorOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.disable.resend');
Route::post('/account/two-factor-authentication/change-method/send-otp', [FrontendUserController::class, 'sendChangeTwoFactorMethodOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.change-method.send');
Route::get('/account/two-factor-authentication/change-method/verify', [FrontendUserController::class, 'changeTwoFactorMethodVerification'])->middleware('front-account')->name('front.account.two-factor-authentication.change-method.challenge');
Route::post('/account/two-factor-authentication/change-method/verify', [FrontendUserController::class, 'verifyChangeTwoFactorMethodOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.change-method.verify');
Route::post('/account/two-factor-authentication/change-method/resend', [FrontendUserController::class, 'resendChangeTwoFactorMethodOtp'])->middleware('front-account')->name('front.account.two-factor-authentication.change-method.resend');
Route::get('/account/change-password', [FrontendUserController::class, 'changePassword'])->middleware('front-account')->name('front.account.change-password');
Route::post('/account/change-password', [FrontendUserController::class, 'updateAccountPassword'])->middleware('front-account')->name('front.account.change-password.update');
Route::get('/taza-shumara', [FrontController::class, 'tazaShumara'])->name('taza.shumara');
Route::get('/sabqa-shumare', [FrontController::class, 'sabqa_shumare'])->name('sabqa-shumare');
Route::get('/mazameen', [FrontController::class, 'mazameen'])->name('mazameen');
Route::get('/taaruf', [FrontController::class, 'taaruf'])->name('front.taaruf');
Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
Route::post('/contact', [FrontController::class, 'storeContact'])->name('front.contact.store');
Route::get('/advertise', [AdvertisingRequestController::class, 'index'])->middleware('front-account')->name('front.advertise');
Route::get('/advertise/availability', [AdvertisingRequestController::class, 'availability'])->middleware('front-account')->name('front.advertise.availability');
Route::post('/advertise', [AdvertisingRequestController::class, 'store'])->middleware('front-account')->name('front.advertise.store');
Route::get('/advertise/success/{adRequest}', [AdvertisingRequestController::class, 'success'])->whereNumber('adRequest')->middleware('front-account')->name('front.advertise.success');
Route::get('/ask-question', [AskQuestionController::class, 'index'])->middleware('front-account')->name('front.ask-question');
Route::post('/ask-question', [AskQuestionController::class, 'store'])->middleware('front-account')->name('front.ask-question.store');
Route::get('/ask-question/success/{askQuestion}', [AskQuestionController::class, 'success'])->whereNumber('askQuestion')->middleware('front-account')->name('front.ask-question.success');
Route::get('/faqs', [FrontController::class, 'faqs'])->name('front.faqs');
Route::get('/privacy-policy', [FrontController::class, 'privacyPolicy'])->name('front.privacy-policy');
Route::get('/terms-and-conditions', [FrontController::class, 'termsAndConditions'])->name('front.terms-and-conditions');
Route::get('/disclaimer', [FrontController::class, 'disclaimer'])->name('front.disclaimer');
Route::get('/mozoaat', [FrontController::class, 'mozoaat'])->name('front.mozoaat');
Route::get('/mazmoon-nigaar', [FrontController::class, 'mazmoonNigaar'])->name('front.mazmoon-nigaar');
Route::get('/mazmoon-nigaar/{id}/{slug}', [FrontController::class, 'mazmoonNigaarDetail'])->whereNumber('id')->name('front.mazmoon-nigaar.detail');
Route::get('/mozu/{id}/{mozuName}', [FrontController::class, 'mozuDetail'])->whereNumber('id')->name('mozu-detail');
Route::get('/mazmoon/{id}/{slug}', [FrontController::class, 'mazmoon_detail'])->whereNumber('id')->name('mazmoon-detail');
Route::post('/mazmoon/{article}/bookmark', [BookmarkController::class, 'storeArticle'])
    ->whereNumber('article')
    ->middleware('front-account')
    ->name('front.article-bookmarks.store');
Route::delete('/mazmoon/{article}/bookmark', [BookmarkController::class, 'destroyArticle'])
    ->whereNumber('article')
    ->middleware('front-account')
    ->name('front.article-bookmarks.destroy');
Route::get('/shumara-detail/{id}/{slug}', [FrontController::class, 'shumara_detail'])->whereNumber('id')->name('shumara-detail');
Route::post('/shumara-detail/{magazine}/bookmark', [BookmarkController::class, 'storeMagazine'])
    ->whereNumber('magazine')
    ->middleware('front-account')
    ->name('front.magazine-bookmarks.store');
Route::delete('/shumara-detail/{magazine}/bookmark', [BookmarkController::class, 'destroyMagazine'])
    ->whereNumber('magazine')
    ->middleware('front-account')
    ->name('front.magazine-bookmarks.destroy');
Route::get('/shumara-detail/{id}/{slug}/pdf', [FrontController::class, 'shumara_pdf'])->whereNumber('id')->name('shumara-detail.pdf');
Route::get('/shumara-detail/{id}/{slug}/download', [FrontController::class, 'shumara_download'])->whereNumber('id')->name('shumara-detail.download');
Route::get('/ads/{ad}/click', AdClickController::class)->whereNumber('ad')->name('ads.click');
Route::post('/site-visits/heartbeat', [SiteVisitController::class, 'update'])
    ->middleware('throttle:120,1')
    ->name('front.site-visits.heartbeat');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//  Admin Routes
Route::get('/admin', [AdminController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('admin.login.submit');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin-access', 'fixed-content-language'])
    ->group(function (): void {

        // admin dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        Route::get('/site-analytics', [SiteAnalyticsController::class, 'index'])
            ->middleware('permission:site-analytics.view')
            ->name('site-analytics.index');
        Route::get('/site-analytics/data', [SiteAnalyticsController::class, 'data'])
            ->middleware('permission:site-analytics.view')
            ->name('site-analytics.data');
        Route::get('/site-analytics/export', [SiteAnalyticsController::class, 'export'])
            ->middleware('permission:site-analytics.export')
            ->name('site-analytics.export');
        Route::get('/site-analytics/{type}/{identifier}', [SiteAnalyticsController::class, 'detail'])
            ->middleware('permission:site-analytics.view')
            ->name('site-analytics.detail');
        Route::get('/site-analytics/{type}/{identifier}/data', [SiteAnalyticsController::class, 'detailData'])
            ->middleware('permission:site-analytics.view')
            ->name('site-analytics.detail.data');
        Route::get('/site-analytics/{type}/{identifier}/export', [SiteAnalyticsController::class, 'detailExport'])
            ->middleware('permission:site-analytics.export')
            ->name('site-analytics.detail.export');

        Route::get('/contacts', [ContactController::class, 'index'])
            ->middleware('permission:contacts.view')
            ->name('contacts.index');
        Route::get('/contacts/data', [ContactController::class, 'data'])
            ->middleware('permission:contacts.view')
            ->name('contacts.data');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])
            ->whereNumber('contact')
            ->middleware('permission:contacts.view')
            ->name('contacts.show');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])
            ->whereNumber('contact')
            ->middleware('permission:contacts.delete')
            ->name('contacts.destroy');

        Route::get('/newsletter-subscribers', [AdminNewsletterSubscriberController::class, 'index'])
            ->middleware('permission:newsletter-subscribers.view')
            ->name('newsletter-subscribers.index');
        Route::post('/newsletter-subscribers/{newsletterSubscriber}/resync', [AdminNewsletterSubscriberController::class, 'resync'])
            ->whereNumber('newsletterSubscriber')
            ->middleware('permission:newsletter-subscribers.edit')
            ->name('newsletter-subscribers.resync');
        Route::get('/newsletter-campaigns', [NewsletterCampaignController::class, 'index'])->middleware('permission:newsletter-campaigns.view')->name('newsletter-campaigns.index');
        Route::get('/newsletter-campaigns/create', [NewsletterCampaignController::class, 'create'])->middleware('permission:newsletter-campaigns.create')->name('newsletter-campaigns.create');
        Route::post('/newsletter-campaigns', [NewsletterCampaignController::class, 'store'])->middleware('permission:newsletter-campaigns.create')->name('newsletter-campaigns.store');
        Route::get('/newsletter-campaigns/{newsletterCampaign}', [NewsletterCampaignController::class, 'show'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.view')->name('newsletter-campaigns.show');
        Route::get('/newsletter-campaigns/{newsletterCampaign}/edit', [NewsletterCampaignController::class, 'edit'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.edit')->name('newsletter-campaigns.edit');
        Route::put('/newsletter-campaigns/{newsletterCampaign}', [NewsletterCampaignController::class, 'update'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.edit')->name('newsletter-campaigns.update');
        Route::delete('/newsletter-campaigns/{newsletterCampaign}', [NewsletterCampaignController::class, 'destroy'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.delete')->name('newsletter-campaigns.destroy');
        Route::get('/newsletter-campaigns/{newsletterCampaign}/preview', [NewsletterCampaignController::class, 'preview'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.view')->name('newsletter-campaigns.preview');
        Route::post('/newsletter-campaigns/{newsletterCampaign}/test', [NewsletterCampaignController::class, 'sendTest'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.edit')->name('newsletter-campaigns.test');
        Route::post('/newsletter-campaigns/{newsletterCampaign}/send', [NewsletterCampaignController::class, 'sendNow'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.send')->name('newsletter-campaigns.send');
        Route::post('/newsletter-campaigns/{newsletterCampaign}/retry', [NewsletterCampaignController::class, 'retry'])->whereNumber('newsletterCampaign')->middleware('permission:newsletter-campaigns.send')->name('newsletter-campaigns.retry');

        Route::get('/ask-questions', [AdminAskQuestionController::class, 'index'])
            ->middleware('permission:ask-questions.view')
            ->name('ask-questions.index');
        Route::get('/ask-questions/{askQuestion}', [AdminAskQuestionController::class, 'show'])
            ->whereNumber('askQuestion')
            ->middleware('permission:ask-questions.view')
            ->name('ask-questions.show');
        Route::patch('/ask-questions/{askQuestion}/response', [AdminAskQuestionController::class, 'updateResponse'])
            ->whereNumber('askQuestion')
            ->middleware('permission:ask-questions.edit')
            ->name('ask-questions.update-response');

        Route::get('/info-pages/{page}/edit', [InfoPageController::class, 'edit'])
            ->whereIn('page', array_keys(config('info_pages.pages')))
            ->middleware('permission:privacy-policy.view|terms-conditions.view|disclaimer.view')
            ->name('info-pages.edit');
        Route::put('/info-pages/{page}', [InfoPageController::class, 'update'])
            ->whereIn('page', array_keys(config('info_pages.pages')))
            ->middleware('permission:privacy-policy.edit|terms-conditions.edit|disclaimer.edit')
            ->name('info-pages.update');

        // Super Admin-only immutable activity history
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('super-admin')
            ->name('activity-logs.index');
        Route::get('/activity-logs/detail/{id}', [ActivityLogController::class, 'show'])
            ->whereNumber('id')
            ->middleware('super-admin')
            ->name('activity-logs.show');
        Route::get('/activity-logs/export/excel', [ActivityLogController::class, 'exportExcel'])
            ->middleware('super-admin')
            ->name('activity-logs.export.excel');
        Route::get('/activity-logs/export/pdf', [ActivityLogController::class, 'exportPdf'])
            ->middleware('super-admin')
            ->name('activity-logs.export.pdf');

        // admin change password
        Route::get('/change-password', [AdminController::class, 'changePassword'])
            ->middleware('permission:change-password.view')
            ->name('change-password');
        Route::put('/change-password', [AdminController::class, 'updatePassword'])
            ->middleware('permission:change-password.edit')
            ->name('change-password.update');

        // admin logout
        Route::post('/logout', [AdminController::class, 'logout'])
            ->name('logout');

        // general setting
        Route::get('/general-setting', [GeneralSettingController::class, 'edit'])
            ->middleware('permission:general-settings.view')
            ->name('general-setting.edit');
        Route::put('/general-setting', [GeneralSettingController::class, 'update'])
            ->middleware('permission:general-settings.edit')
            ->name('general-setting.update');

        Route::get('/subscription-reminder-settings', [SubscriptionNotificationSettingController::class, 'edit'])
            ->middleware('permission:subscription-reminder-settings.view')
            ->name('subscription-notification-settings.edit');
        Route::put('/subscription-reminder-settings', [SubscriptionNotificationSettingController::class, 'update'])
            ->middleware('permission:subscription-reminder-settings.edit')
            ->name('subscription-notification-settings.update');

        // slider routes
        Route::get('/slider', [SliderController::class, 'index'])
            ->middleware('permission:sliders.view')
            ->name('slider.index');
        Route::get('/slider/add', [SliderController::class, 'create'])
            ->middleware('permission:sliders.create')
            ->name('slider.create');
        Route::post('/slider/store', [SliderController::class, 'store'])
            ->middleware('permission:sliders.create')
            ->name('slider.store');
        Route::get('/slider/edit/{id}', [SliderController::class, 'edit'])
            ->whereNumber('id')
            ->middleware('permission:sliders.edit')
            ->name('slider.edit');
        Route::post('/slider/update/{id}', [SliderController::class, 'update'])
            ->whereNumber('id')
            ->middleware('permission:sliders.edit')
            ->name('slider.update');
        Route::delete('/slider/delete/{id}', [SliderController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware('permission:sliders.delete')
            ->name('slider.destroy');

        // banner routes
        Route::get('/banner', [BannerController::class, 'index'])
            ->middleware('permission:banners.view')
            ->name('banner.index');
        Route::get('/banner/add', [BannerController::class, 'create'])
            ->middleware('permission:banners.create')
            ->name('banner.create');
        Route::post('/banner/store', [BannerController::class, 'store'])
            ->middleware('permission:banners.create')
            ->name('banner.store');
        Route::get('/banner/edit/{id}', [BannerController::class, 'edit'])
            ->whereNumber('id')
            ->middleware('permission:banners.edit')
            ->name('banner.edit');
        Route::post('/banner/update/{id}', [BannerController::class, 'update'])
            ->whereNumber('id')
            ->middleware('permission:banners.edit')
            ->name('banner.update');
        Route::delete('/banner/delete/{id}', [BannerController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware('permission:banners.delete')
            ->name('banner.destroy');

        Route::get('/ads', [AdsController::class, 'index'])->middleware('permission:ads.view')->name('ads.index');
        Route::get('/ads/add', [AdsController::class, 'create'])->middleware('permission:ads.create')->name('ads.create');
        Route::post('/ads', [AdsController::class, 'store'])->middleware('permission:ads.create')->name('ads.store');
        Route::get('/ads/{id}', [AdsController::class, 'show'])->whereNumber('id')->middleware('permission:ads.view')->name('ads.show');
        Route::get('/ads/{id}/edit', [AdsController::class, 'edit'])->whereNumber('id')->middleware('permission:ads.edit')->name('ads.edit');
        Route::put('/ads/{id}', [AdsController::class, 'update'])->whereNumber('id')->middleware('permission:ads.edit')->name('ads.update');
        Route::delete('/ads/{id}', [AdsController::class, 'destroy'])->whereNumber('id')->middleware('permission:ads.delete')->name('ads.destroy');

        Route::get('/advertising-requests', [AdminAdvertisingRequestController::class, 'index'])->middleware('permission:ad-requests.view')->name('advertising-requests.index');
        Route::get('/advertising-requests/{adRequest}', [AdminAdvertisingRequestController::class, 'show'])->whereNumber('adRequest')->middleware('permission:ad-requests.view')->name('advertising-requests.show');
        Route::post('/advertising-requests/{adRequest}/status', [AdminAdvertisingRequestController::class, 'changeStatus'])->whereNumber('adRequest')->middleware('permission:ad-requests.edit')->name('advertising-requests.status');
        Route::post('/advertising-requests/{adRequest}/placements/{placement}/confirm', [AdminAdvertisingRequestController::class, 'confirmPlacement'])->whereNumber('adRequest')->whereNumber('placement')->middleware('permission:ad-requests.edit')->name('advertising-requests.placements.confirm');

        // home heading routes
        Route::get('/home-headings', [HomePageSectionHeadingController::class, 'index'])
            ->middleware('permission:home-headings.view')
            ->name('home-headings.index');
        Route::put('/home-headings', [HomePageSectionHeadingController::class, 'update'])
            ->middleware('permission:home-headings.edit')
            ->name('home-headings.update');

        // home article routes
        Route::get('/home-article', [HomeArticleController::class, 'index'])
            ->middleware('permission:home-article.view')
            ->name('home-article.index');
        Route::put('/home-article', [HomeArticleController::class, 'update'])
            ->middleware('permission:home-article.edit')
            ->name('home-article.update');

        // Taza Shumara routes
        Route::get('/taza-shumara', [TazaShumaraController::class, 'index'])->middleware('permission:taza-shumara.view')->name('taza-shumara.index');
        Route::get('/taza-shumara/add', [TazaShumaraController::class, 'create'])->middleware('permission:taza-shumara.create')->name('taza-shumara.create');
        Route::post('/taza-shumara/store', [TazaShumaraController::class, 'store'])->middleware('permission:taza-shumara.create')->name('taza-shumara.store');
        Route::get('/taza-shumara/{tazaShumara}/manage', [TazaShumaraController::class, 'manage'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.view')->name('taza-shumara.manage');
        Route::get('/taza-shumara/{tazaShumara}/edit', [TazaShumaraController::class, 'edit'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.edit')->name('taza-shumara.edit');
        Route::post('/taza-shumara/{tazaShumara}/update', [TazaShumaraController::class, 'update'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.edit')->name('taza-shumara.update');
        Route::delete('/taza-shumara/{tazaShumara}/cover', [TazaShumaraController::class, 'destroyCover'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.edit')->name('taza-shumara.cover.destroy');
        Route::delete('/taza-shumara/{tazaShumara}', [TazaShumaraController::class, 'destroy'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.delete')->name('taza-shumara.destroy');
        Route::post('/taza-shumara/{tazaShumara}/articles', [TazaShumaraController::class, 'storeArticle'])->whereNumber('tazaShumara')->middleware('permission:taza-shumara.edit')->name('taza-shumara.articles.store');
        Route::get('/taza-shumara/{tazaShumara}/articles/{placement}/edit', [TazaShumaraController::class, 'editArticle'])->whereNumber(['tazaShumara', 'placement'])->middleware('permission:taza-shumara.edit')->name('taza-shumara.articles.edit');
        Route::post('/taza-shumara/{tazaShumara}/articles/{placement}', [TazaShumaraController::class, 'updateArticle'])->whereNumber(['tazaShumara', 'placement'])->middleware('permission:taza-shumara.edit')->name('taza-shumara.articles.update');
        Route::delete('/taza-shumara/{tazaShumara}/articles/{placement}', [TazaShumaraController::class, 'destroyArticle'])->whereNumber(['tazaShumara', 'placement'])->middleware('permission:taza-shumara.edit')->name('taza-shumara.articles.destroy');

        // home card routes
        Route::get('/home-cards', [HomeCardController::class, 'index'])->middleware('permission:home-cards.view')->name('home-cards.index');
        Route::get('/home-cards/card-title-position', [HomeCardController::class, 'cardTitlePosition'])->middleware('permission:home-cards.view')->name('home-cards.card-title-position');
        Route::put('/home-cards/card-title-position', [HomeCardController::class, 'updateCardTitlePosition'])->middleware('permission:home-cards.edit')->name('home-cards.card-title-position.update');
        Route::get('/home-cards/add', [HomeCardController::class, 'create'])->middleware('permission:home-cards.create')->name('home-cards.create');
        Route::post('/home-cards/store', [HomeCardController::class, 'store'])->middleware('permission:home-cards.create')->name('home-cards.store');
        Route::get('/home-cards/detail/{id}', [HomeCardController::class, 'show'])->whereNumber('id')->middleware('permission:home-cards.view')->name('home-cards.show');
        Route::get('/home-cards/edit/{id}', [HomeCardController::class, 'edit'])->whereNumber('id')->middleware('permission:home-cards.edit')->name('home-cards.edit');
        Route::post('/home-cards/update/{id}', [HomeCardController::class, 'update'])->whereNumber('id')->middleware('permission:home-cards.edit')->name('home-cards.update');
        Route::delete('/home-cards/delete/{id}', [HomeCardController::class, 'destroy'])->whereNumber('id')->middleware('permission:home-cards.delete')->name('home-cards.destroy');

        // services routes
        Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:services.view')->name('services.index');
        Route::get('/services/add', [ServiceController::class, 'create'])->middleware('permission:services.create')->name('services.create');
        Route::post('/services/store', [ServiceController::class, 'store'])->middleware('permission:services.create')->name('services.store');
        Route::get('/services/edit/{id}', [ServiceController::class, 'edit'])->whereNumber('id')->middleware('permission:services.edit')->name('services.edit');
        Route::post('/services/update/{id}', [ServiceController::class, 'update'])->whereNumber('id')->middleware('permission:services.edit')->name('services.update');
        Route::delete('/services/delete/{id}', [ServiceController::class, 'destroy'])->whereNumber('id')->middleware('permission:services.delete')->name('services.destroy');

        // home section routes
        Route::get('/home-sections', [HomeSectionController::class, 'index'])->middleware('permission:home-sections.view')->name('home-sections.index');
        Route::get('/home-sections/add', [HomeSectionController::class, 'create'])->middleware('permission:home-sections.create')->name('home-sections.create');
        Route::post('/home-sections/store', [HomeSectionController::class, 'store'])->middleware('permission:home-sections.create')->name('home-sections.store');
        Route::get('/home-sections/detail/{id}', [HomeSectionController::class, 'show'])->whereNumber('id')->middleware('permission:home-sections.view')->name('home-sections.show');
        Route::get('/home-sections/edit/{id}', [HomeSectionController::class, 'edit'])->whereNumber('id')->middleware('permission:home-sections.edit')->name('home-sections.edit');
        Route::post('/home-sections/update/{id}', [HomeSectionController::class, 'update'])->whereNumber('id')->middleware('permission:home-sections.edit')->name('home-sections.update');
        Route::delete('/home-sections/delete/{id}', [HomeSectionController::class, 'destroy'])->whereNumber('id')->middleware('permission:home-sections.delete')->name('home-sections.destroy');

        // about routes
        Route::get('/about', [AboutController::class, 'index'])->middleware('permission:abouts.view')->name('about.index');
        Route::get('/about/add', [AboutController::class, 'create'])->middleware('permission:abouts.create')->name('about.create');
        Route::post('/about/store', [AboutController::class, 'store'])->middleware('permission:abouts.create')->name('about.store');
        Route::get('/about/detail/{id}', [AboutController::class, 'show'])->whereNumber('id')->middleware('permission:abouts.view')->name('about.show');
        Route::get('/about/edit/{id}', [AboutController::class, 'edit'])->whereNumber('id')->middleware('permission:abouts.edit')->name('about.edit');
        Route::post('/about/update/{id}', [AboutController::class, 'update'])->whereNumber('id')->middleware('permission:abouts.edit')->name('about.update');
        Route::delete('/about/delete/{id}', [AboutController::class, 'destroy'])->whereNumber('id')->middleware('permission:abouts.delete')->name('about.destroy');

        // faq routes
        Route::get('/faq-categories', [FaqCategoryController::class, 'index'])->middleware('permission:faq-categories.view')->name('faq-categories.index');
        Route::get('/faq-categories/add', [FaqCategoryController::class, 'create'])->middleware('permission:faq-categories.create')->name('faq-categories.create');
        Route::post('/faq-categories/store', [FaqCategoryController::class, 'store'])->middleware('permission:faq-categories.create')->name('faq-categories.store');
        Route::get('/faq-categories/edit/{id}', [FaqCategoryController::class, 'edit'])->whereNumber('id')->middleware('permission:faq-categories.edit')->name('faq-categories.edit');
        Route::post('/faq-categories/update/{id}', [FaqCategoryController::class, 'update'])->whereNumber('id')->middleware('permission:faq-categories.edit')->name('faq-categories.update');
        Route::delete('/faq-categories/delete/{id}', [FaqCategoryController::class, 'destroy'])->whereNumber('id')->middleware('permission:faq-categories.delete')->name('faq-categories.destroy');

        Route::get('/faq', [FAQController::class, 'index'])
            ->middleware('permission:faqs.view')
            ->name('faq.index');
        Route::get('/faq/add', [FAQController::class, 'create'])
            ->middleware('permission:faqs.create')
            ->name('faq.create');
        Route::post('/faq/store', [FAQController::class, 'store'])
            ->middleware('permission:faqs.create')
            ->name('faq.store');
        Route::get('/faq/edit/{id}', [FAQController::class, 'edit'])
            ->whereNumber('id')
            ->middleware('permission:faqs.edit')
            ->name('faq.edit');
        Route::post('/faq/update/{id}', [FAQController::class, 'update'])
            ->whereNumber('id')
            ->middleware('permission:faqs.edit')
            ->name('faq.update');
        Route::delete('/faq/delete/{id}', [FAQController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware('permission:faqs.delete')
            ->name('faq.destroy');

        // author settings routes must stay before dynamic author routes
        Route::get('/author/settings', [AuthorSettingController::class, 'edit'])
            ->middleware('permission:author-settings.view')
            ->name('author.settings');
        Route::post('/author/settings', [AuthorSettingController::class, 'update'])
            ->middleware('permission:author-settings.edit')
            ->name('author.settings.update');

        // role management routes
        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
        Route::get('/roles/add', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
        Route::post('/roles/store', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
        Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->whereNumber('id')->middleware('permission:roles.edit')->name('roles.edit');
        Route::post('/roles/update/{id}', [RoleController::class, 'update'])->whereNumber('id')->middleware('permission:roles.edit')->name('roles.update');
        Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy'])->whereNumber('id')->middleware('permission:roles.delete')->name('roles.destroy');

        // admin user management routes
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
        Route::get('/users/add', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
        Route::get('/users/{id}/transfer-ownership', [UserController::class, 'transferOwnership'])->whereNumber('id')->middleware('permission:users.edit')->name('users.transfer-ownership');
        Route::post('/users/{id}/transfer-ownership', [UserController::class, 'storeOwnershipTransfer'])->whereNumber('id')->middleware('permission:users.edit')->name('users.transfer-ownership.store');
        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->whereNumber('id')->middleware('permission:users.edit')->name('users.edit');
        Route::post('/users/update/{id}', [UserController::class, 'update'])->whereNumber('id')->middleware('permission:users.edit')->name('users.update');
        Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->whereNumber('id')->middleware('permission:users.delete')->name('users.destroy');
        Route::get('/users/role-permissions/{role}', [UserController::class, 'rolePermissions'])->whereNumber('role')->middleware('permission:users.view|users.create|users.edit')->name('users.role-permissions');

        // author routes
        Route::get('/author', [AuthorController::class, 'index'])
            ->middleware('permission:authors.view')
            ->name('author.index');
        Route::get('/author/add', [AuthorController::class, 'create'])
            ->middleware('permission:authors.create')
            ->name('author.create');
        Route::post('/author/store', [AuthorController::class, 'store'])
            ->middleware('permission:authors.create')
            ->name('author.store');
        Route::get('/author/edit/{id}', [AuthorController::class, 'edit'])
            ->whereNumber('id')
            ->middleware('permission:authors.edit')
            ->name('author.edit');
        Route::post('/author/update/{id}', [AuthorController::class, 'update'])
            ->whereNumber('id')
            ->middleware('permission:authors.edit')
            ->name('author.update');
        Route::delete('/author/delete/{id}', [AuthorController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware('permission:authors.delete')
            ->name('author.destroy');

        // tags routes
        Route::get('/tags', [TagsController::class, 'index'])->middleware('permission:tags.view')->name('tags.index');
        Route::get('/tags/add', [TagsController::class, 'create'])->middleware('permission:tags.create')->name('tags.create');
        Route::post('/tags/store', [TagsController::class, 'store'])->middleware('permission:tags.create')->name('tags.store');
        Route::get('/tags/edit/{id}', [TagsController::class, 'edit'])->whereNumber('id')->middleware('permission:tags.edit')->name('tags.edit');
        Route::post('/tags/update/{id}', [TagsController::class, 'update'])->whereNumber('id')->middleware('permission:tags.edit')->name('tags.update');
        Route::delete('/tags/delete/{id}', [TagsController::class, 'destroy'])->whereNumber('id')->middleware('permission:tags.delete')->name('tags.destroy');

        // categories routes
        Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:categories.view')->name('categories.index');
        Route::get('/categories/add', [CategoryController::class, 'create'])->middleware('permission:categories.create')->name('categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->middleware('permission:categories.create')->name('categories.store');
        Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->whereNumber('id')->middleware('permission:categories.edit')->name('categories.edit');
        Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->whereNumber('id')->middleware('permission:categories.edit')->name('categories.update');
        Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy'])->whereNumber('id')->middleware('permission:categories.delete')->name('categories.destroy');

        // currency routes
        Route::get('/currency', [CurrencyController::class, 'index'])->middleware('permission:currency.view')->name('currency.index');
        Route::get('/currency/add', [CurrencyController::class, 'create'])->middleware('permission:currency.create')->name('currency.create');
        Route::post('/currency/store', [CurrencyController::class, 'store'])->middleware('permission:currency.create')->name('currency.store');
        Route::get('/currency/edit/{id}', [CurrencyController::class, 'edit'])->whereNumber('id')->middleware('permission:currency.edit')->name('currency.edit');
        Route::post('/currency/update/{id}', [CurrencyController::class, 'update'])->whereNumber('id')->middleware('permission:currency.edit')->name('currency.update');
        Route::delete('/currency/delete/{id}', [CurrencyController::class, 'destroy'])->whereNumber('id')->middleware('permission:currency.delete')->name('currency.destroy');

        // subscription plan routes
        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->middleware('permission:subscription-plans.view')->name('subscription-plan.index');
        Route::get('/subscription-plans/add', [SubscriptionPlanController::class, 'create'])->middleware('permission:subscription-plans.create')->name('subscription-plan.create');
        Route::post('/subscription-plans/store', [SubscriptionPlanController::class, 'store'])->middleware('permission:subscription-plans.create')->name('subscription-plan.store');
        Route::get('/subscription-plans/edit/{id}', [SubscriptionPlanController::class, 'edit'])->whereNumber('id')->middleware('permission:subscription-plans.edit')->name('subscription-plan.edit');
        Route::post('/subscription-plans/update/{id}', [SubscriptionPlanController::class, 'update'])->whereNumber('id')->middleware('permission:subscription-plans.edit')->name('subscription-plan.update');
        Route::delete('/subscription-plans/delete/{id}', [SubscriptionPlanController::class, 'destroy'])->whereNumber('id')->middleware('permission:subscription-plans.delete')->name('subscription-plan.destroy');

        // payment account routes
        Route::get('/payment-accounts', [PaymentAccountsController::class, 'index'])->middleware('permission:payment-accounts.view')->name('payment-account.index');
        Route::get('/payment-accounts/add', [PaymentAccountsController::class, 'create'])->middleware('permission:payment-accounts.create')->name('payment-account.create');
        Route::post('/payment-accounts/store', [PaymentAccountsController::class, 'store'])->middleware('permission:payment-accounts.create')->name('payment-account.store');
        Route::get('/payment-accounts/edit/{id}', [PaymentAccountsController::class, 'edit'])->whereNumber('id')->middleware('permission:payment-accounts.edit')->name('payment-account.edit');
        Route::post('/payment-accounts/update/{id}', [PaymentAccountsController::class, 'update'])->whereNumber('id')->middleware('permission:payment-accounts.edit')->name('payment-account.update');
        Route::delete('/payment-accounts/delete/{id}', [PaymentAccountsController::class, 'destroy'])->whereNumber('id')->middleware('permission:payment-accounts.delete')->name('payment-account.destroy');

        // membership routes
        Route::get('/memberships', [MembershipController::class, 'index'])->middleware('permission:memberships.view')->name('membership.index');
        Route::get('/memberships/add', [MembershipController::class, 'create'])->middleware('permission:memberships.create')->name('membership.create');
        Route::post('/memberships/store', [MembershipController::class, 'store'])->middleware('permission:memberships.create')->name('membership.store');
        Route::get('/memberships/edit/{id}', [MembershipController::class, 'edit'])->whereNumber('id')->middleware('permission:memberships.edit')->name('membership.edit');
        Route::post('/memberships/update/{id}', [MembershipController::class, 'update'])->whereNumber('id')->middleware('permission:memberships.edit')->name('membership.update');
        Route::delete('/memberships/delete/{id}', [MembershipController::class, 'destroy'])->whereNumber('id')->middleware('permission:memberships.delete')->name('membership.destroy');

        Route::get('/user-subscriptions', [UserSubscriptionController::class, 'index'])
            ->middleware('permission:user-subscriptions.view')
            ->name('user-subscriptions.index');
        Route::get('/user-subscriptions/{userSubscription}', [UserSubscriptionController::class, 'show'])
            ->whereNumber('userSubscription')
            ->middleware('permission:user-subscriptions.view')
            ->name('user-subscriptions.show');
        Route::get('/user-subscriptions/{userSubscription}/payment-slip/view', [UserSubscriptionController::class, 'viewSlip'])
            ->whereNumber('userSubscription')
            ->middleware('permission:user-subscriptions.view')
            ->name('user-subscriptions.payment-slip.view');
        Route::get('/user-subscriptions/{userSubscription}/payment-slip/download', [UserSubscriptionController::class, 'downloadSlip'])
            ->whereNumber('userSubscription')
            ->middleware('permission:user-subscriptions.view')
            ->name('user-subscriptions.payment-slip.download');
        Route::post('/user-subscriptions/{userSubscription}/approve', [UserSubscriptionController::class, 'approve'])
            ->whereNumber('userSubscription')
            ->middleware('permission:user-subscriptions.approve')
            ->name('user-subscriptions.approve');
        Route::post('/user-subscriptions/{userSubscription}/reject', [UserSubscriptionController::class, 'reject'])
            ->whereNumber('userSubscription')
            ->middleware('permission:user-subscriptions.reject')
            ->name('user-subscriptions.reject');

        // magazine routes
        Route::get('/magazine', [MagazineController::class, 'index'])->middleware('permission:magazines.view')->name('magazine.index');
        Route::get('/magazine/add', [MagazineController::class, 'create'])->middleware('permission:magazines.create')->name('magazine.create');
        Route::post('/magazine/store', [MagazineController::class, 'store'])->middleware('permission:magazines.create')->name('magazine.store');
        Route::get('/magazine/detail/{id}', [MagazineController::class, 'show'])->whereNumber('id')->middleware('permission:magazines.view')->name('magazine.show');
        Route::get('/magazine/edit/{id}', [MagazineController::class, 'edit'])->whereNumber('id')->middleware('permission:magazines.edit')->name('magazine.edit');
        Route::get('/magazine/{magazine}/pdf/{storageLocation}/view', [MagazineController::class, 'viewPdf'])->whereNumber(['magazine', 'storageLocation'])->middleware('permission:magazines.pdf.view')->name('magazine.pdf.view');
        Route::get('/magazine/{magazine}/pdf/{storageLocation}/download', [MagazineController::class, 'downloadPdf'])->whereNumber(['magazine', 'storageLocation'])->middleware('permission:magazines.pdf.download')->name('magazine.pdf.download');
        Route::delete('/magazine/{magazine}/related/{relatedMagazine}', [MagazineController::class, 'removeRelatedMagazine'])->whereNumber(['magazine', 'relatedMagazine'])->middleware('permission:magazines.related.remove')->name('magazine.related.remove');
        Route::post('/magazine/update/{id}', [MagazineController::class, 'update'])->whereNumber('id')->middleware('permission:magazines.edit')->name('magazine.update');
        Route::post('/magazine/publish/{id}', [MagazineController::class, 'publish'])->whereNumber('id')->middleware('permission:magazines.publish')->name('magazine.publish');
        Route::delete('/magazine/delete/{id}', [MagazineController::class, 'destroy'])->whereNumber('id')->middleware('permission:magazines.delete')->name('magazine.destroy');

        // article routes
        Route::get('/article', [ArticleController::class, 'index'])->middleware('permission:articles.view')->name('article.index');
        Route::get('/article/add', [ArticleController::class, 'create'])->middleware('permission:articles.create')->name('article.create');
        Route::post('/article/store', [ArticleController::class, 'store'])->middleware('permission:articles.create')->name('article.store');
        Route::get('/article/detail/{id}', [ArticleController::class, 'show'])->whereNumber('id')->middleware('permission:articles.view')->name('article.show');
        Route::get('/article/edit/{id}', [ArticleController::class, 'edit'])->whereNumber('id')->middleware('permission:articles.edit')->name('article.edit');
        Route::post('/article/update/{id}', [ArticleController::class, 'update'])->whereNumber('id')->middleware('permission:articles.edit')->name('article.update');
        Route::post('/article/publish/{id}', [ArticleController::class, 'publish'])->whereNumber('id')->middleware('permission:articles.publish')->name('article.publish');
        Route::delete('/article/{article}/related/{relatedArticle}', [ArticleController::class, 'removeRelatedArticle'])->whereNumber(['article', 'relatedArticle'])->middleware('permission:articles.related.remove')->name('article.related.remove');
        Route::delete('/article/delete/{id}', [ArticleController::class, 'destroy'])->whereNumber('id')->middleware('permission:articles.delete')->name('article.destroy');

        // consultancy routes
        Route::get('/consultancy', [ConsultancyController::class, 'index'])->middleware('permission:consultancies.view')->name('consultancy.index');
        Route::get('/consultancy/add', [ConsultancyController::class, 'create'])->middleware('permission:consultancies.create')->name('consultancy.create');
        Route::post('/consultancy/store', [ConsultancyController::class, 'store'])->middleware('permission:consultancies.create')->name('consultancy.store');
        Route::get('/consultancy/detail/{id}', [ConsultancyController::class, 'show'])->whereNumber('id')->middleware('permission:consultancies.view')->name('consultancy.show');
        Route::get('/consultancy/edit/{id}', [ConsultancyController::class, 'edit'])->whereNumber('id')->middleware('permission:consultancies.edit')->name('consultancy.edit');
        Route::post('/consultancy/update/{id}', [ConsultancyController::class, 'update'])->whereNumber('id')->middleware('permission:consultancies.edit')->name('consultancy.update');
        Route::post('/consultancy/publish/{id}', [ConsultancyController::class, 'publish'])->whereNumber('id')->middleware('permission:consultancies.publish')->name('consultancy.publish');
        Route::delete('/consultancy/{consultancy}/related/{relatedConsultancy}', [ConsultancyController::class, 'removeRelatedConsultancy'])->whereNumber(['consultancy', 'relatedConsultancy'])->middleware('permission:consultancies.related.remove')->name('consultancy.related.remove');
        Route::delete('/consultancy/delete/{id}', [ConsultancyController::class, 'destroy'])->whereNumber('id')->middleware('permission:consultancies.delete')->name('consultancy.destroy');

        Route::get('/course', [CourseController::class, 'index'])->middleware('permission:courses.view')->name('course.index');
        Route::get('/course/add', [CourseController::class, 'create'])->middleware('permission:courses.create')->name('course.create');
        Route::post('/course/store', [CourseController::class, 'store'])->middleware('permission:courses.create')->name('course.store');
        Route::get('/course/detail/{id}', [CourseController::class, 'show'])->whereNumber('id')->middleware('permission:courses.view')->name('course.show');
        Route::get('/course/edit/{id}', [CourseController::class, 'edit'])->whereNumber('id')->middleware('permission:courses.edit')->name('course.edit');
        Route::post('/course/update/{id}', [CourseController::class, 'update'])->whereNumber('id')->middleware('permission:courses.edit')->name('course.update');
        Route::post('/course/publish/{id}', [CourseController::class, 'publish'])->whereNumber('id')->middleware('permission:courses.publish')->name('course.publish');
        Route::delete('/course/{course}/related/{relatedCourse}', [CourseController::class, 'removeRelatedCourse'])->whereNumber(['course', 'relatedCourse'])->middleware('permission:courses.related.remove')->name('course.related.remove');
        Route::delete('/course/delete/{id}', [CourseController::class, 'destroy'])->whereNumber('id')->middleware('permission:courses.delete')->name('course.destroy');

        // video routes
        Route::get('/video', [VideoController::class, 'index'])->middleware('permission:videos.view')->name('video.index');
        Route::get('/video/add', [VideoController::class, 'create'])->middleware('permission:videos.create')->name('video.create');
        Route::post('/video/store', [VideoController::class, 'store'])->middleware('permission:videos.create')->name('video.store');
        Route::get('/video/edit/{id}', [VideoController::class, 'edit'])->whereNumber('id')->middleware('permission:videos.edit')->name('video.edit');
        Route::post('/video/update/{id}', [VideoController::class, 'update'])->whereNumber('id')->middleware('permission:videos.edit')->name('video.update');
        Route::post('/video/publish/{id}', [VideoController::class, 'publish'])->whereNumber('id')->middleware('permission:videos.publish')->name('video.publish');
        Route::delete('/video/delete/{id}', [VideoController::class, 'destroy'])->whereNumber('id')->middleware('permission:videos.delete')->name('video.destroy');

        // meta tags routes
        Route::get('/meta-tags', [MetaTagController::class, 'index'])->middleware('permission:meta-tags.view')->name('meta-tags.index');
        Route::get('/meta-tags/add', [MetaTagController::class, 'create'])->middleware('permission:meta-tags.create')->name('meta-tags.create');
        Route::post('/meta-tags/store', [MetaTagController::class, 'store'])->middleware('permission:meta-tags.create')->name('meta-tags.store');
        Route::get('/meta-tags/edit/{id}', [MetaTagController::class, 'edit'])->whereNumber('id')->middleware('permission:meta-tags.edit')->name('meta-tags.edit');
        Route::post('/meta-tags/update/{id}', [MetaTagController::class, 'update'])->whereNumber('id')->middleware('permission:meta-tags.edit')->name('meta-tags.update');
        Route::delete('/meta-tags/delete/{id}', [MetaTagController::class, 'destroy'])->whereNumber('id')->middleware('permission:meta-tags.delete')->name('meta-tags.destroy');
    });
