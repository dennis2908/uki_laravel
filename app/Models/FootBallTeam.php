<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootBallTeam extends Model
{
    use HasFactory;

    protected $table = 'football_team';

    public $timestamps = false;

    protected $fillable = ["id", 'language_updated_time', 'multiple_name', 'coach_id', 'competition_id', 'country_id', 'country_logo', 'foreign_players', 'foundation_time', 'logo', 'market_value', 'market_value_currency', 'name', 'national', 'national_players', 'short_name', 'total_players', 'updated_at', 'venue_id', 'website'];
}
