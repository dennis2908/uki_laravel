<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\MatchBetTeam;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipsterAwayTeam>
 */
class TipsterHomeTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tsr_match_bet_id' => MatchBetTeam::all()->unique()->random()->id,
            'odds' => $this->faker->numberBetween(100000, 2000000),
            'handicap1' => $this->faker->numberBetween(1, 5),
            'handicap2' => $this->faker->numberBetween(6, 10),
            'football_team_id' => "1f0ac68e-570d-405f-8e75-a8feb2a37205" //change with id from table football_team
        ];
    }
}
