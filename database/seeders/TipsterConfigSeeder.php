<?php

namespace Database\Seeders;

use App\Models\TipsterConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipsterConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipsterConfig::factory()->count(550)->create();
    }
}
