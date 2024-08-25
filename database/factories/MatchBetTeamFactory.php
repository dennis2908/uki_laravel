<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\MatchBetTeam;

use App\Models\TipsterSeason;

class MatchBetTeamFactory extends Factory
{
    protected $model = MatchBetTeam::class;
    public function definition(): array
    {
        return [
            'tipster_season_id' => TipsterSeason::all()->unique()->random()->id,
            'odds' => $this->faker->numberBetween(100000, 2000000),
            'overs' => $this->faker->numberBetween(100000, 2000000),
            'under' => $this->faker->numberBetween(100000, 2000000),
            'bet_price' => $this->faker->numberBetween(100000, 200000),
            'big_bet_price' => $this->faker->numberBetween(1000000, 2000000),
            'over_under_handicap1' => $this->faker->numberBetween(1, 5),
            'over_under_handicap2' => $this->faker->numberBetween(6, 10),
            'football_match_id' => "01733975-f868-42de-829a-b2cfb5edb52e" //change with id from table football_match

        ];
    }
}
