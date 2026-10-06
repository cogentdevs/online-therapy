<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\Account\AdvertisingRequestController;
use App\Http\Controllers\Api\Account\BookmarkController;
use App\Http\Controllers\Api\Account\ProfileController;
use App\Http\Controllers\Api\Account\QuestionController;
use App\Http\Controllers\Api\Account\RecentActivityController;
use App\Http\Controllers\Api\Account\SecurityController;
use App\Http\Controllers\Api\Account\SubscriptionController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\TwoFactorController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MagazineController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\SubscriptionProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/app/config', [AppConfigController::class, 'index'])->name('api.app.config');
Route::get('/navigation', [NavigationController::class, 'index'])->name('api.navigation');
Route::get('/about', [AboutController::class, 'index'])->name('api.about.index');
Route::get('/faq-categories', [FaqController::class, 'categories'])->name('api.faq-categories.index');
Route::get('/faqs', [FaqController::class, 'index'])->name('api.faqs.index');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('api.pages.show');
Route::get('/subscription-products', [SubscriptionProductController::class, 'index'])->name('api.subscription-products.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('api.contact.store');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->middleware('throttle:10,1')->name('api.newsletter.subscribe');
Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('api.auth.register');
    Route::get('/activation', [AuthController::class, 'inspectActivation'])->middleware('throttle:30,1')->name('api.auth.activation.show');
    Route::post('/activation', [AuthController::class, 'activate'])->middleware('throttle:10,1')->name('api.auth.activation.store');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/login/2fa/verify', [TwoFactorController::class, 'verify'])->middleware('throttle:10,1')->name('api.auth.login.2fa.verify');
    Route::post('/login/2fa/resend', [TwoFactorController::class, 'resend'])->middleware('throttle:10,1')->name('api.auth.login.2fa.resend');
    Route::post('/password/forgot', [PasswordController::class, 'forgot'])->name('api.auth.password.forgot');
    Route::post('/password/reset', [PasswordController::class, 'reset'])->name('api.auth.password.reset');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.auth.logout');
});
Route::middleware('auth:sanctum')->prefix('me')->group(function (): void {
    Route::get('/', [ProfileController::class, 'show'])->name('api.me.show');
    Route::patch('/', [ProfileController::class, 'update'])->name('api.me.update');
    Route::delete('/profile-image', [ProfileController::class, 'destroyProfileImage'])->name('api.me.profile-image.destroy');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('api.me.password.update');
    Route::get('/2fa', [SecurityController::class, 'show'])->name('api.me.2fa.show');
    Route::post('/2fa/challenges', [SecurityController::class, 'storeChallenge'])->name('api.me.2fa.challenges.store');
    Route::post('/2fa/challenges/verify', [SecurityController::class, 'verifyChallenge'])->name('api.me.2fa.challenges.verify');
    Route::post('/2fa/challenges/resend', [SecurityController::class, 'resendChallenge'])->name('api.me.2fa.challenges.resend');
    Route::get('/subscriptions/active', [SubscriptionController::class, 'active'])->name('api.me.subscriptions.active');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('api.me.subscriptions.index');
    Route::post('/subscriptions/purchase', [SubscriptionController::class, 'purchase'])->name('api.me.subscriptions.purchase');
    Route::get('/entitlements', [SubscriptionController::class, 'entitlements'])->name('api.me.entitlements.index');
    Route::get('/subscriptions/{subscription}/invoice/download', [SubscriptionController::class, 'downloadInvoice'])->whereNumber('subscription')->name('api.me.subscriptions.invoice.download');
    Route::get('/subscriptions/{subscription}/invoice', [SubscriptionController::class, 'invoice'])->whereNumber('subscription')->name('api.me.subscriptions.invoice.show');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('api.me.bookmarks.index');
    Route::get('/recent-activities', [RecentActivityController::class, 'index'])->name('api.me.recent-activities.index');
    Route::post('/questions', [QuestionController::class, 'store'])->name('api.me.questions.store');
    Route::get('/questions', [QuestionController::class, 'index'])->name('api.me.questions.index');
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->whereNumber('question')->name('api.me.questions.show');
    Route::post('/advertising-requests', [AdvertisingRequestController::class, 'store'])->name('api.me.advertising-requests.store');
    Route::get('/advertising-requests', [AdvertisingRequestController::class, 'index'])->name('api.me.advertising-requests.index');
    Route::get('/advertising-requests/{adRequest}', [AdvertisingRequestController::class, 'show'])->whereNumber('adRequest')->name('api.me.advertising-requests.show');
});
Route::middleware('auth:sanctum')->group(function (): void {
    Route::put('/articles/{article}/bookmark', [BookmarkController::class, 'storeArticle'])
        ->whereNumber('article')
        ->name('api.articles.bookmark.store');
    Route::delete('/articles/{article}/bookmark', [BookmarkController::class, 'destroyArticle'])
        ->whereNumber('article')
        ->name('api.articles.bookmark.destroy');
    Route::put('/magazines/{magazine}/pdf/bookmark', [BookmarkController::class, 'storeMagazinePdf'])
        ->whereNumber('magazine')
        ->name('api.magazines.pdf.bookmark.store');
    Route::delete('/magazines/{magazine}/pdf/bookmark', [BookmarkController::class, 'destroyMagazinePdf'])
        ->whereNumber('magazine')
        ->name('api.magazines.pdf.bookmark.destroy');
});
Route::get('/articles', [ArticleController::class, 'index'])->name('api.articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->whereNumber('article')->name('api.articles.show');
Route::get('/magazines', [MagazineController::class, 'index'])->name('api.magazines.index');
Route::get('/magazines/current', [MagazineController::class, 'current'])->name('api.magazines.current');
Route::get('/magazines/{magazine}/pdf', [MagazineController::class, 'pdf'])->whereNumber('magazine')->name('api.magazines.pdf');
Route::get('/magazines/{magazine}/download', [MagazineController::class, 'download'])->whereNumber('magazine')->name('api.magazines.download');
Route::get('/magazines/{magazine}', [MagazineController::class, 'show'])->whereNumber('magazine')->name('api.magazines.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->whereNumber('category')->name('api.categories.show');
Route::get('/authors', [AuthorController::class, 'index'])->name('api.authors.index');
Route::get('/authors/{author}', [AuthorController::class, 'show'])->whereNumber('author')->name('api.authors.show');
Route::prefix('home')->group(function (): void {
    Route::get('/sliders', [HomeController::class, 'sliders'])->name('api.home.sliders');
    Route::get('/section-headings', [HomeController::class, 'sectionHeadings'])->name('api.home.section-headings');
    Route::get('/cards', [HomeController::class, 'cards'])->name('api.home.cards');
    Route::get('/latest-articles', [HomeController::class, 'latestArticles'])->name('api.home.latest-articles');
    Route::get('/editorial', [HomeController::class, 'editorial'])->name('api.home.editorial');
    Route::get('/banners', [HomeController::class, 'banners'])->name('api.home.banners');
});

Route::get('/ads', [AdController::class, 'index'])->name('api.ads.index');
Route::post('/ads/{ad}/click', [AdController::class, 'click'])->whereNumber('ad')->middleware('throttle:60,1')->name('api.ads.click');
Route::get('/rankings', [RankingController::class, 'index'])->name('api.rankings.index');
Route::post('/analytics/search-clicks', [AnalyticsController::class, 'searchClick'])->middleware('throttle:60,1')->name('api.analytics.search-clicks.store');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
