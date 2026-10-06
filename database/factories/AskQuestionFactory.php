<?php

namespace Database\Factories;

use App\Models\AskQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AskQuestion>
 */
class AskQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => '03001234567',
            'subject' => fake()->sentence(),
            'sawal' => fake()->paragraph(),
        ];
    }
}
