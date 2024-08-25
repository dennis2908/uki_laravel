<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\TipsterSeason;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipsterSeason>
 */
class TipsterSeasonFactory extends Factory
{
    protected $model = TipsterSeason::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'start_date' => $this->faker->unique()->dateTimeBetween($startDate = '-17 days', $endDate = 'now', $timezone = "Asia/Jakarta"),
            'end_date' => $this->faker->unique()->dateTimeBetween($startDate = 'now', $endDate = '57 days', $timezone = "Asia/Jakarta"),
            'initialize_balance' => $this->faker->numberBetween(100000, 2000000),
        ];
    }
}
