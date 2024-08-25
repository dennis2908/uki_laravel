<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchBetTeam extends Model
{
    use HasFactory;

    protected $table = 'tipster_match_bet';

    protected $fillable = ['tipster_season_id', 'odds_over', 'odds_under', 'bet_price', 'big_bet_price', 'handicap', 'football_match_id'];

    function matchBetEdit($Id)
    {

        return $this::join('tipster_away_team', 'tipster_away_team.tsr_match_bet_id', '=', 'tipster_match_bet.id')
            ->join('tipster_home_team', 'tipster_home_team.tsr_match_bet_id', '=', 'tipster_match_bet.id')
            ->select([
                DB::RAW('tipster_match_bet.*'),
                DB::RAW('tipster_away_team.id as away_team_id'),
                DB::RAW('tipster_away_team.odds as away_team_odds'),
                DB::RAW('tipster_away_team.handicap as away_team_handicap'),
                DB::RAW('tipster_away_team.football_team_id as away_team_football_team_id'),
                DB::RAW('tipster_home_team.id as home_team_id'),
                DB::RAW('tipster_home_team.odds as home_team_odds'),
                DB::RAW('tipster_home_team.handicap as home_team_handicap'),
                DB::RAW('tipster_home_team.football_team_id as home_team_football_team_id'),
            ])
            ->find($Id);
    }

    function transactionEdit($Data)
    {
        return $this::select([
            'tipster_match_bet.id',
            DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_over,3,'en_US')) as odds_over"),
            DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_under,3,'en_US')) as odds_under"),
            DB::RAW("CONCAT(FORMAT(bet_price,3,'en_US')) as bet_price"),
            DB::RAW("CONCAT(FORMAT(big_bet_price,3,'en_US')) as big_bet_price"),
        ])->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->whereRaw('tipster_match_bet.id NOT IN(select tipster_match_bet_id from tipster_transaction where user_id = ' . $Data->user_id . ')')
            ->orWhere('tipster_match_bet.id', $Data->tipster_match_bet_id)
            ->latest('tipster_match_bet.created_at')->get();
    }

    function tipsterMatchBetAPIIndex()
    {
        return $this::select([
            'tipster_match_bet.*',
            DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
            DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
            DB::RAW('format(tipster_match_bet.big_bet_price,2) as big_bet_price'),
            DB::RAW('format(bet_price,2) as bet_price'),
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
            DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),
        ])
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))->latest()->get();
    }

    function getDataByIdNoRedun($Request)
    {
        return $this::select([
            'tipster_match_bet.id',
            DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_over,3,'en_US')) as odds_over"),
            DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_under,3,'en_US')) as odds_under"),
            DB::RAW("CONCAT(FORMAT(bet_price,3,'en_US')) as bet_price"),
            DB::RAW("CONCAT(FORMAT(big_bet_price,3,'en_US')) as big_bet_price"),
        ])->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->whereRaw('tipster_match_bet.id NOT IN(select tipster_match_bet_id from tipster_transaction where user_id = ' . $Request->id . ')')
            ->latest('tipster_match_bet.created_at')->get();
    }

    public function tipsterMatchBetAPIGetDataById($Request)
    {
        return $this::select([
            'tipster_match_bet.*',
            DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
            DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
            DB::RAW('format(big_bet_price,2) as big_bet_price'),
            DB::RAW('format(bet_price,2) as bet_price'),
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
            DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),

        ])->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))->where('tipster_match_bet.id', $Request->id)->first();;
    }

    public function tipsterMatchBetAPIGetHomeAway($Request)
    {
        return $this::join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_season", "tipster_season.id", "=", 'tipster_match_bet.tipster_season_id')
            ->select([
                'tipster_match_bet.id',
                DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
                DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
                DB::RAW('format(big_bet_price,3) as big_bet_price'),
                DB::RAW('format(bet_price,3) as bet_price'),
                DB::RAW('football_team_home.name as football_team_home_name'),
                DB::RAW('football_team_home.logo as football_team_home_logo'),
                DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
                DB::RAW('football_team_away.name as football_team_away_name'),
                DB::RAW('football_team_away.logo as football_team_away_logo'),
                DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
                DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
                DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),
            ])
            ->where('tipster_match_bet.id', $Request->id)->first();
    }

    public function tipsterUpcomingMatchBetAPIIndex()
    {
        return $this::select([
            'tipster_match_bet.*',
            DB::RAW('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") as match_datetime'),
            DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
            DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
            DB::RAW('format(tipster_match_bet.big_bet_price,2) as big_bet_price'),
            DB::RAW('format(tipster_match_bet.bet_price,2) as bet_price'),
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
            DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),
        ])
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)')
            ->latest()->get();
    }

    public function tipsterUpcomingMatchBetGetDataById($Request)
    {
        return $this::select([
            'tipster_match_bet.*',
            DB::RAW('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") as match_datetime'),
            DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
            DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
            DB::RAW('format(big_bet_price,2) as big_bet_price'),
            DB::RAW('format(bet_price,2) as bet_price'),
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
            DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
            DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),
        ])
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)')
            ->where('tipster_match_bet.id', $Request->id)->first();
    }

    public function tipsterUpcomingMatchBetGetHomeAway($Request)
    {

        return $this::join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
            ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
            ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_season", "tipster_season.id", "=", 'tipster_match_bet.tipster_season_id')
            ->select([
                'tipster_match_bet.id',
                DB::RAW('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") as match_datetime'),
                DB::RAW('format(tipster_match_bet.odds_over,3) as odds_over'),
                DB::RAW('format(tipster_match_bet.odds_under,3) as odds_under'),
                DB::RAW('format(big_bet_price,3) as big_bet_price'),
                DB::RAW('format(bet_price,3) as bet_price'),
                DB::RAW('football_team_home.name as football_team_home_name'),
                DB::RAW('football_team_home.logo as football_team_home_logo'),
                DB::RAW('format(tipster_away_team.odds,3) as tipster_away_odds'),
                DB::RAW('football_team_away.name as football_team_away_name'),
                DB::RAW('football_team_away.logo as football_team_away_logo'),
                DB::RAW('format(tipster_home_team.odds,3) as tipster_home_odds'),
                DB::RAW('format(tipster_away_team.handicap,3) as tipster_away_handicap'),
                DB::RAW('format(tipster_home_team.handicap,3) as tipster_home_handicap'),
            ])
            ->where('tipster_match_bet.id', $Request->id)->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)')->first();
    }
}
