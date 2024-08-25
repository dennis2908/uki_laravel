<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class MatchController extends Controller
{
    public function upcoming(Request $request)
    {
        $token = $request->token;

        if(empty($token)){
            return $this->returnJson([], 401, false, 'Token not provided');
        }

        $getUserId = DB::table('user')->select(['id'])->where('token', $token)->first();
        $getUserId = $getUserId->id;

        $datas = DB::table('tipster_match_bet')
        ->select([
            'tipster_match_bet.id',
            'tipster_match_bet.odds_over',
            'tipster_match_bet.odds_under',
            'tipster_match_bet.handicap',
            'tipster_match_bet.bet_price',
            'tipster_match_bet.big_bet_price',
            DB::RAW('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") as match_datetime'),
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
            'tipster_transaction.type as bet_on',
        ])
        ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
        ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
        ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
        ->leftJoin('tipster_transaction', function($query) use($getUserId){
            $query->on('tipster_transaction.tipster_match_bet_id', '=', 'tipster_match_bet.id')
            ->where('tipster_transaction.user_id', $getUserId)
            ->where('tipster_transaction.status', '=', 1);
        })
        ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
        ->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)')
        ->orderBy('football_match.match_time', 'ASC')
        ->get();

        if (empty($datas)) {
            return $this->returnJson($datas, 404, false);
        }

        $datas = $datas->toArray();

        foreach($datas as $key => $val){
            $datas[$key] = (array) $val;
            $val = (array) $val;

            $datas[$key]['id'] = Crypt::encrypt($val['id']);
            $datas[$key]['handicap'] = $this->decToFraction($val['handicap']);
            $datas[$key]['tipster_away_handicap'] = $this->decToFraction($val['tipster_away_handicap']);
            $datas[$key]['tipster_home_handicap'] = $this->decToFraction($val['tipster_home_handicap']);
            $datas[$key]['bet_price'] = number_format($val['bet_price'], 0, ',', '.');
            $datas[$key]['big_bet_price'] = number_format($val['big_bet_price'], 0, ',', '.');
            $datas[$key]['odds_over'] = $val['odds_over'] * 1;
            $datas[$key]['odds_under'] = $val['odds_under'] * 1;
            $datas[$key]['tipster_home_odds'] = $val['tipster_home_odds'] * 1;
            $datas[$key]['tipster_away_odds'] = $val['tipster_away_odds'] * 1;
        }

        return $this->returnJson($datas);
    }

    public function history(Request $request)
    {
        $token = $request->token;

        if(empty($token)){
            return $this->returnJson([], 401, false, 'Token not provided');
        }

        $getUserId = DB::table('user')->select(['id'])->where('token', $token)->first();
        $getUserId = $getUserId->id;

        $datas = DB::table('tipster_match_bet')
        ->select([
            'tipster_match_bet.id',
            'tipster_match_bet.odds_over',
            'tipster_match_bet.odds_under',
            'tipster_match_bet.handicap',
            'tipster_match_bet.bet_price',
            'tipster_match_bet.big_bet_price',
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            'tipster_transaction.place_bet_time',
            'tipster_transaction.win_prize',
        ])
        ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
        ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
        ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
        ->join('tipster_transaction', function($query) use($getUserId){
            $query->on('tipster_transaction.tipster_match_bet_id', '=', 'tipster_match_bet.id')
            ->where('tipster_transaction.user_id', $getUserId)
            ->where('tipster_transaction.status', 'IN', '(2, 3, 4)');
        })
        ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
        ->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)')
        ->orderBy('football_match.match_time', 'DESC')
        ->get();

        if (empty($datas)) {
            return $this->returnJson($datas, 404, false);
        }

        $datas = $datas->toArray();

        foreach($datas as $key => $val){
            $datas[$key] = (array) $val;
            $val = (array) $val;

            $datas[$key]['id'] = Crypt::encrypt($val['id']);
            $datas[$key]['place_bet_time'] = Carbon::parse($val['place_bet_time'])->diffForHumans();
            $datas[$key]['win_prize'] = '$'.number_format($val['win_prize'], 0, ',', '.');
        }

        return $this->returnJson($datas);
    }

    public function openBet(Request $request)
    {
        $token = $request->token;

        if(empty($token)){
            return $this->returnJson([], 401, false, 'Token not provided');
        }

        $getUserId = DB::table('user')->select(['id'])->where('token', $token)->first();
        $getUserId = $getUserId->id;

        $datas = DB::table('tipster_match_bet')
        ->select([
            'tipster_match_bet.id',
            'tipster_match_bet.odds_over',
            'tipster_match_bet.odds_under',
            'tipster_match_bet.handicap',
            'tipster_match_bet.bet_price',
            'tipster_match_bet.big_bet_price',
            DB::RAW('football_team_home.name as football_team_home_name'),
            DB::RAW('football_team_away.name as football_team_away_name'),
            DB::RAW('football_team_home.logo as football_team_home_logo'),
            DB::RAW('football_team_away.logo as football_team_away_logo'),
            'tipster_transaction.place_bet_time',
            'tipster_transaction.win_prize',
        ])
        ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join(DB::RAW("football_team as football_team_home"), "football_team_home.id", "=", 'tipster_home_team.football_team_id')
        ->join(DB::RAW("football_team as football_team_away"), "football_team_away.id", "=", 'tipster_away_team.football_team_id')
        ->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
        ->join('tipster_transaction', function($query) use($getUserId){
            $query->on('tipster_transaction.tipster_match_bet_id', '=', 'tipster_match_bet.id')
            ->where('tipster_transaction.user_id', $getUserId)
            ->where('tipster_transaction.status', '=', 1);
        })
        ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
        ->whereRaw('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") >= NOW()')
        ->orderBy('football_match.match_time', 'DESC')
        ->get();

        if (empty($datas)) {
            return $this->returnJson($datas, 404, false);
        }

        $datas = $datas->toArray();

        foreach($datas as $key => $val){
            $datas[$key] = (array) $val;
            $val = (array) $val;

            $datas[$key]['id'] = Crypt::encrypt($val['id']);
            $datas[$key]['place_bet_time'] = Carbon::parse($val['place_bet_time'])->diffForHumans();
            $datas[$key]['win_prize'] = '$'.number_format($val['win_prize'], 0, ',', '.');
        }

        return $this->returnJson($datas);
    }
}
