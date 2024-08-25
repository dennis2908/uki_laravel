<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;

use App\Models\FootBallTeam;

class FootBallTeamFactory extends Factory
{
    protected $model = FootBallTeam::class;
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'language_updated_time' => $this->faker->numberBetween(1, 2000000),
            'multiple_name' => $this->faker->text,
            'coach_id' => $this->faker->text,
            'competition_id' => $this->faker->text,
            'country_id' => $this->faker->text,
            'country_logo' => $this->faker->text,
            'foreign_players' => $this->faker->numberBetween(1, 2000000),
            'foundation_time' => $this->faker->numberBetween(1, 2000000),
            'logo' => $this->faker->text,
            'market_value' => $this->faker->numberBetween(1, 2000000),
            'market_value_currency' => $this->faker->text,
            'name' => $this->faker->text,
            'national' => $this->faker->text,
            'national_players' => $this->faker->numberBetween(1, 2000000),
            'short_name' => $this->faker->text,
            'total_players' => $this->faker->numberBetween(1, 2000000),
            'updated_at' => $this->faker->numberBetween(1, 2000000),
            'venue_id' => $this->faker->text,
            'website' => $this->faker->text,
        ];
    }
}
