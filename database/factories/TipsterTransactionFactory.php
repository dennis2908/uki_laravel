<?php

namespace Database\Factories;

use App\Models\TipsterUser;

use App\Models\TipsterTransaction;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\MatchBetTeam;

class TipsterTransactionFactory extends Factory
{
    protected $model = TipsterTransaction::class;
    public function definition(): array
    {
        $TypeValues = ['home', 'away'];
        $BetValues = ['big', 'normal'];
        return [
            'user_id' => TipsterUser::all()->unique()->random()->id,
            'tipster_match_bet_id'  => MatchBetTeam::all()->random()->id,
            'type' => $TypeValues[rand(0, 1)],
            'bet_type' => $BetValues[rand(0, 1)],
            'place_bet_time' => $this->faker->unique()->dateTimeBetween($startDate = '-17 days', $endDate = 'now', $timezone = "Asia/Jakarta"),
            'win_prize' => $this->faker->numberBetween(100000, 2000000),
        ];
    }
}
