<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class FootBallMatch extends Model
{
    use HasFactory;

    protected $table = 'football_match';

    public $timestamps = false;

    protected $fillable = ["id", 'updated_at', 'agg_score', 'away_position', 'away_scores', 'away_team_id', 'competition_id', 'environment', 'first_half_kick_off_time', 'group_num', 'home_position', 'home_scores', 'home_team_id', 'lineup', 'match_time', 'mlive', 'neutral', 'note', 'overtime_kick_off_time', 'referee_id', 'related_id', 'round_num', 'season_id', 'second_half_kick_off_time', 'stage_id', 'status_id', 'venue_id', 'vlive'];

    function matchBetEdit($Data)
    {
        return DB::select(
            'select `football_match`.`id`, `football_match`.`home_team_id`,
        `football_match`.`away_team_id`, FROM_UNIXTIME(match_time) as match_date, home_team.name AS home_team_name,
        away_team.name AS away_team_name
        from `football_match` inner
        join football_team home_team on `home_team`.`id` = `football_match`.`home_team_id`
        inner join football_team away_team on `away_team`.`id` = `football_match`.`away_team_id`
        inner join tipster_match_bet on `tipster_match_bet`.`football_match_id` COLLATE utf8mb4_0900_ai_ci  = `football_match`.`id`
        inner join tipster_season on `tipster_season`.`id` = `tipster_match_bet`.`tipster_season_id`
            where
             ( FROM_UNIXTIME( match_time, "%Y-%m-%d %H:%i:%s" )) >= (NOW() + INTERVAL 1 DAY)
            and tipster_season.start_date <= (FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s"))
            and tipster_season.end_date >= (FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s"))
            and football_match.id = "' . $Data->football_match_id . '"
            and tipster_season.id = "' . $Data->tipster_season_id . '"
            AND (football_match.id COLLATE utf8mb4_0900_ai_ci not in (select football_match_id COLLATE utf8mb4_0900_ai_ci from tipster_match_bet
            where tipster_season_id = ' . $Data->tipster_season_id . '
            )
            or football_match_id COLLATE utf8mb4_0900_ai_ci = ' . "'" . $Data->football_match_id .  "'" . ')
'
        );
    }

    function getDataByMatchTime($DatasMatchBet, $Request)
    {
        return DB::select(
            'select `football_match`.`id`, `football_match`.`home_team_id`,
        `football_match`.`away_team_id`, FROM_UNIXTIME(match_time) as match_date, home_team.name AS home_team_name,
        away_team.name AS away_team_name
        from `football_match` inner
        join football_team home_team on `home_team`.`id` = `football_match`.`home_team_id`
        inner join football_team away_team on `away_team`.`id` = `football_match`.`away_team_id`
            where
            ( FROM_UNIXTIME( match_time, "%Y-%m-%d %H:%i:%s" )) >= (NOW() + INTERVAL 1 DAY)
            AND ( FROM_UNIXTIME( match_time, "%Y-%m-%d %H:%i:%s" )) >= "' . $DatasMatchBet->start_date . '"
            AND "' . $DatasMatchBet->end_date . '" >= ( FROM_UNIXTIME( match_time, "%Y-%m-%d %H:%i:%s" ))
            AND football_match.id COLLATE utf8mb4_0900_ai_ci not in (select football_match_id COLLATE utf8mb4_0900_ai_ci from tipster_match_bet
            where tipster_season_id = ' . $Request['id'] . '
            )
            and ( FROM_UNIXTIME( match_time, "%Y-%m-%d" )) >= ' . "'" . $Request['start_date_match'] . "'"  . '
                and ( FROM_UNIXTIME( match_time, "%Y-%m-%d" )) <= ' . "'"  . $Request['end_date_match'] . "'" . '
            '
        );
    }

    public function getDataById($Request)
    {
        return  DB::select('select `football_match`.`id`, `football_match`.`home_team_id`,
        `football_match`.`away_team_id`, FROM_UNIXTIME(match_time) as match_date, home_team.name AS home_team_name,
        away_team.name AS away_team_name
        from `football_match` inner
        join football_team home_team on `home_team`.`id` = `football_match`.`home_team_id`
        inner join football_team away_team on `away_team`.`id` = `football_match`.`away_team_id`
            where `football_match`.`id` = "' . $Request['id'] . '"
            ');
    }

    public function getDataByFS($Request)
    {


        return DB::select(
            'select `football_match`.`id`, `football_match`.`home_team_id`,
        `football_match`.`away_team_id`, FROM_UNIXTIME(match_time) as match_date, home_team.name AS home_team_name,
        away_team.name AS away_team_name
        from `football_match` inner
        join football_team home_team on `home_team`.`id` = `football_match`.`home_team_id`
        inner join football_team away_team on `away_team`.`id` = `football_match`.`away_team_id`
        inner join tipster_match_bet on `tipster_match_bet`.`football_match_id` COLLATE utf8mb4_0900_ai_ci  = `football_match`.`id`
        inner join tipster_season on `tipster_season`.`id` = `tipster_match_bet`.`tipster_season_id`
            where
            tipster_season.start_date <= FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s")
            and tipster_season.end_date >= FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s")
            and football_match.id = "' . $Request->football_match_id . '"
            and tipster_season.id = "' . $Request->tipster_season_id . '"
            '
        );
    }
}
