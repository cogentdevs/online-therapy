<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'first_reminder_days',
    'second_reminder_days',
    'third_reminder_days',
    'isActive',
])]
class SubscriptionNotificationSetting extends Model
{
    protected $attributes = [
        'isActive' => true,
    ];

    public static function current(): self
    {
        $setting = self::query()->find(1);

        if ($setting !== null) {
            return $setting;
        }

        $setting = new self;
        $setting->setAttribute($setting->getKeyName(), 1);

        return $setting;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'first_reminder_days' => 'integer',
            'second_reminder_days' => 'integer',
            'third_reminder_days' => 'integer',
            'isActive' => 'boolean',
        ];
    }
}
