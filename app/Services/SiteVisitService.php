<?php

namespace App\Services;

use App\Models\SiteVisit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Throwable;

class SiteVisitService
{
    public const VISITOR_COOKIE = 'site_visitor_id';

    /** @var array<string, string> */
    private const TRACKED_ROUTES = [
        'frontend.home' => 'home',
        'front.subscriptions' => 'subscriptions',
        'taza.shumara' => 'taza-shumara',
        'sabqa-shumare' => 'sabqa-shumare',
        'mazameen' => 'mazameen',
        'front.taaruf' => 'about',
        'front.mozoaat' => 'categories',
        'front.mazmoon-nigaar' => 'authors',
        'front.mazmoon-nigaar.detail' => 'author-detail',
        'mozu-detail' => 'category-detail',
        'mazmoon-detail' => 'mazmoon-detail',
        'shumara-detail' => 'shumara-detail',
        'api.articles.show' => 'mazmoon-detail',
        'api.magazines.show' => 'shumara-detail',
    ];

    public function record(Request $request, ?Model $visitable = null): void
    {
        $routeName = $request->route()?->getName();

        if (! $request->isMethod('GET') || ! is_string($routeName) || ! isset(self::TRACKED_ROUTES[$routeName])) {
            return;
        }

        try {
            $visitorId = $this->visitorId($request);
            $plainToken = Str::random(64);
            $location = $this->location($request->ip());
            $agent = $this->parseUserAgent($request->userAgent());

            SiteVisit::query()->create([
                'user_id' => $request->user('web')?->getKey(),
                'visitor_id' => $visitorId,
                'public_token_hash' => hash('sha256', $plainToken),
                'page_key' => self::TRACKED_ROUTES[$routeName],
                'route_name' => $routeName,
                'url' => Str::limit($request->url(), 2048, ''),
                'referrer' => $this->sanitizeUrl($request->headers->get('referer')),
                'visitable_type' => $visitable?->getMorphClass(),
                'visitable_id' => $visitable?->getKey(),
                'ip_address' => $request->ip(),
                ...$location,
                ...$agent,
                'started_at' => now(),
            ]);

            $request->attributes->set('site_visit_token', $plainToken);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /** @return array<string, mixed> */
    private function location(?string $ip): array
    {
        $empty = ['country' => null, 'country_code' => null, 'city' => null, 'region' => null, 'latitude' => null, 'longitude' => null];

        if ($ip === null || ! app()->bound('geoip')) {
            return $empty;
        }

        try {
            $location = app('geoip')->getLocation($ip);

            return [
                'country' => data_get($location, 'country'),
                'country_code' => data_get($location, 'iso_code', data_get($location, 'isoCode')),
                'city' => data_get($location, 'city'),
                'region' => data_get($location, 'state_name', data_get($location, 'state')),
                'latitude' => data_get($location, 'lat'),
                'longitude' => data_get($location, 'lon'),
            ];
        } catch (Throwable $exception) {
            report($exception);

            return $empty;
        }
    }

    /** @return array<string, string|null> */
    private function parseUserAgent(?string $userAgent): array
    {
        $value = Str::limit((string) $userAgent, 2000, '');
        $deviceType = preg_match('/bot|crawler|spider/i', $value) ? 'bot' : (preg_match('/mobile|android|iphone/i', $value) ? 'mobile' : (preg_match('/tablet|ipad/i', $value) ? 'tablet' : 'desktop'));
        preg_match('/(Edg|Chrome|Firefox|Version)\/([\d.]+)/i', $value, $browserMatch);
        preg_match('/(Windows NT|Android|iPhone OS|CPU OS|Mac OS X)\s?([\d._]*)/i', $value, $osMatch);

        return [
            'device' => $deviceType === 'mobile' ? 'Mobile' : ($deviceType === 'tablet' ? 'Tablet' : 'Computer'),
            'device_type' => $deviceType,
            'browser' => $browserMatch[1] ?? null,
            'browser_version' => $browserMatch[2] ?? null,
            'os' => $osMatch[1] ?? null,
            'os_version' => isset($osMatch[2]) ? str_replace('_', '.', $osMatch[2]) : null,
            'user_agent' => $value ?: null,
        ];
    }

    private function visitorId(Request $request): string
    {
        $existing = $request->cookie(self::VISITOR_COOKIE);

        if (is_string($existing) && Str::isUuid($existing)) {
            return $existing;
        }

        $visitorId = (string) Str::uuid();
        Cookie::queue(cookie(self::VISITOR_COOKIE, $visitorId, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'lax'));

        return $visitorId;
    }

    private function sanitizeUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['host'])) {
            return null;
        }

        return Str::limit(($parts['scheme'] ?? 'https').'://'.$parts['host'].($parts['path'] ?? '/'), 2048, '');
    }
}
