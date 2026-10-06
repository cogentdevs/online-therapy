<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Services\FrontendSharedDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class AppConfigController extends Controller
{
    public function __construct(
        private readonly FrontendSharedDataService $frontendSharedDataService,
    ) {}

    public function index(): JsonResponse
    {
        $generalSetting = $this->frontendSharedDataService->generalSetting();
        $activeLanguages = $this->frontendSharedDataService->activeLanguages();

        return response()->json([
            'success' => true,
            'data' => [
                'app' => [
                    'name' => $generalSetting?->app_name,
                    'website_url' => $generalSetting?->url,
                ],
                'branding' => [
                    'header_logo_url' => $this->assetUrl($generalSetting?->logo),
                    'footer_logo_url' => $this->assetUrl($generalSetting?->footer_logo),
                    'favicon_url' => $this->assetUrl($generalSetting?->favicon),
                    'footer_text' => $generalSetting?->footer_text,
                ],
                'contact' => [
                    'email' => $generalSetting?->email,
                    'primary_phone' => $generalSetting?->contact_1,
                    'secondary_phone' => $generalSetting?->contact_2,
                    'address' => $generalSetting?->address,
                ],
                'social_links' => [
                    'facebook' => $generalSetting?->facebook,
                    'x' => $generalSetting?->x,
                    'instagram' => $generalSetting?->instagram,
                    'youtube' => $generalSetting?->youtube,
                    'linkedin' => $generalSetting?->linkedin,
                    'tiktok' => $generalSetting?->tiktok,
                ],
                'app_stores' => [
                    'heading' => $generalSetting?->app_section_heading,
                    'text' => $generalSetting?->app_section_text,
                    'play_store' => [
                        'icon_url' => $this->assetUrl($generalSetting?->play_store_icon),
                        'link' => $generalSetting?->play_store_link,
                    ],
                    'app_store' => [
                        'icon_url' => $this->assetUrl($generalSetting?->app_store_icon),
                        'link' => $generalSetting?->app_store_link,
                    ],
                ],
                'languages' => [
                    'default' => $this->defaultLanguage($generalSetting, $activeLanguages),
                    'active' => $activeLanguages->map(fn (Language $language): array => $this->languageData($language))->values(),
                ],
            ],
        ]);
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }

    /** @param Collection<int, Language> $activeLanguages */
    private function defaultLanguage(?GeneralSetting $generalSetting, Collection $activeLanguages): ?array
    {
        $defaultLanguage = $generalSetting?->defaultLanguage;

        return $defaultLanguage !== null && $activeLanguages->contains('id', $defaultLanguage->id)
            ? $this->languageData($defaultLanguage)
            : null;
    }

    /** @return array{id: int, name: ?string, code: ?string} */
    private function languageData(Language $language): array
    {
        return [
            'id' => $language->id,
            'name' => $language->name,
            'code' => $language->code,
        ];
    }
}
