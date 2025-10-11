<?php

namespace Database\Factories;

use App\Enums\ItemStatus;
use App\Models\Item;
use App\Support\UrlNormalizer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $siteDomain = fake()->unique()->domainName();
        $url = 'https://'.$siteDomain.'/'.Str::slug(fake()->unique()->sentence(3));

        return [
            'url' => $url,
            'url_hash' => UrlNormalizer::hash($url),
            'external_id' => fake()->optional()->bothify('?????-#####'),
            'title' => fake()->sentence(),
            'current_price' => fake()->optional()->numberBetween(100, 100000),
            'currency' => fake()->optional()->currencyCode(),
            'status' => ItemStatus::ACTIVE,
            'first_seen_at' => now(),
            'last_seen_at' => fake()->boolean() ? now() : null,
        ];
    }
}
