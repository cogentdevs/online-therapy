<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<NewsletterSubscriber> */
class NewsletterSubscriberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'status' => NewsletterSubscriber::STATUS_SUBSCRIBED,
            'subscribed_at' => now(),
            'brevo_sync_status' => NewsletterSubscriber::SYNC_PENDING,
        ];
    }
}
