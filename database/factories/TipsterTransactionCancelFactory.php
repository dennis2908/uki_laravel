<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\TipsterTransactionCancel;

use App\Models\TipsterTransaction;

use App\Models\TipsterUser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipsterTransactionCancel>
 */
class TipsterTransactionCancelFactory extends Factory
{
    protected $model = TipsterTransactionCancel::class;
    public function definition(): array
    {
        return  [
            'user_id' => TipsterUser::all()->unique()->random()->id,
            'tipster_transaction_id' => TipsterTransaction::all()->unique()->random()->id,
            'cancel_time' => $this->faker->unique()->dateTimeBetween($startDate = '-17 days', $endDate = 'now', $timezone = "Asia/Jakarta"),
        ];
    }
}
