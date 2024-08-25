<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\FootBallMatch;

use Illuminate\Support\Str;

class FootBallMatchFactory extends Factory
{
    protected $model = FootBallMatch::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'updated_at' => $this->faker->numberBetween(1, 200000000),
            'agg_score' => $this->faker->text,
            'away_position' => $this->faker->text,
            'away_scores' => $this->faker->text,
            'away_team_id' => $this->faker->text,
            'competition_id' => $this->faker->text,
            'environment' => $this->faker->text,
            'first_half_kick_off_time' => $this->faker->numberBetween(1, 200000000),
            'group_num' => $this->faker->numberBetween(1, 200000000),
            'home_position' => $this->faker->text,
            'home_scores' => $this->faker->text,
            'home_team_id' => $this->faker->text,
            'lineup' => $this->faker->numberBetween(1, 200000000),
            'match_time' => 1718772046,
            'mlive' => $this->faker->numberBetween(1, 200000000),
            'neutral' => $this->faker->numberBetween(1, 200000000),
            'note' => $this->faker->text,
            'overtime_kick_off_time' => $this->faker->numberBetween(1, 200000000),
            'referee_id' => $this->faker->text,
            'related_id' => $this->faker->text,
            'round_num' => $this->faker->numberBetween(1, 200000000),
            'season_id' => $this->faker->text,
            'second_half_kick_off_time' => $this->faker->numberBetween(1, 200000000),
            'stage_id' => $this->faker->text,
            'status_id' => $this->faker->numberBetween(1, 200000000),
            'venue_id' => $this->faker->text,
            'vlive' => $this->faker->numberBetween(1, 200000000)
        ];
    }
}
