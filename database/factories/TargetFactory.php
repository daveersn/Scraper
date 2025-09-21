<?php

namespace Database\Factories;

use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Target>
 */
class TargetFactory extends Factory
{
    protected $model = Target::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->unique()->sentence(),
            'url' => fake()->url(),
            'driver' => null,
            'blueprint' => null,
            'schedule_cron' => fake()->optional()->randomElement(['* * * * *', '*/10 * * * *', '0 * * * *']),
            'active' => true,
            'last_run_at' => fake()->optional()->dateTimeThisMonth(),
            'next_run_at' => fake()->optional()->dateTimeBetween('now', '+2 days'),
        ];
    }
}
