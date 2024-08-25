<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\TipsterUser;


class TipsterUserFactory extends Factory
{

    protected $model = TipsterUser::class;
    public function definition(): array
    {
        return [
            'user_id' => 0,
            'open_bet' => $this->faker->numberBetween(100000, 2000000),
            'balance' => $this->faker->numberBetween(100000, 2000000),
            'username' => $this->faker->name,
        ];
    }
}
