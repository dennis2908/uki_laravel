<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipsterHomeTeam extends Model
{
    use HasFactory;

    protected $table = 'tipster_home_team';

    protected $fillable = ['tsr_match_bet_id', 'odds', 'handicap', 'football_team_id'];
}
