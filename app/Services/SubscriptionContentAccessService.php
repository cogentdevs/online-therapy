<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;

class SubscriptionContentAccessService
{
    public const MODULE_ARTICLES = 'Articles';

    public const MODULE_MAGAZINES = 'Magazine';

    public const MODULE_AUDIO = 'Audio';

    public function __construct(private readonly SubscriptionEntitlementService $entitlementService) {}

    public function isPubliclyAccessible(Article|Magazine $content): bool
    {
        return $content->isFree
            && ($content->free_until === null || ! $content->free_until->isBefore(today()));
    }

    public function canAccess(User $user, Article|Magazine $content, string $module): bool
    {
        return $this->isPubliclyAccessible($content)
            || ($user->is_active
                && $user->hasRole('user', 'web')
                && $this->entitlementService->hasEntitlement($user, $module));
    }
}
