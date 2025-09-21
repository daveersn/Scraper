<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemPrice>
 */
class ItemPriceFactory extends Factory
{
    protected $model = ItemPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'price' => fake()->numberBetween(100, 100000),
            'currency' => fake()->currencyCode(),
        ];
    }
}
