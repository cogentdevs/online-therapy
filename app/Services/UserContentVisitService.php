<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use App\Models\UserContentVisit;

class UserContentVisitService
{
    public function record(User $user, Article|Magazine $visitable): void
    {
        $visitedAt = now();

        UserContentVisit::query()->upsert(
            [[
                'user_id' => $user->getKey(),
                'visitable_type' => $visitable->getMorphClass(),
                'visitable_id' => $visitable->getKey(),
                'last_visited_at' => $visitedAt,
                'created_at' => $visitedAt,
                'updated_at' => $visitedAt,
            ]],
            ['user_id', 'visitable_type', 'visitable_id'],
            ['last_visited_at', 'updated_at'],
        );
    }
}
