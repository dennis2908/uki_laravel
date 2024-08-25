<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\MatchBetTeam;
use Illuminate\Support\Facades\DB;

class TipsterUpcomingMatchBetAPIController extends Controller
{
    public function index()
    {
        $Datas = new MatchBetTeam();

        $Datas = $Datas->tipsterUpcomingMatchBetAPIIndex();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        $Datas = $Datas->toArray();

        foreach ($Datas as $key => $val) {
            $Datas[$key]['tipster_away_handicap'] = $this->decToFraction((int)$val['tipster_away_handicap']);
            $Datas[$key]['tipster_home_handicap'] = $this->decToFraction((int)$val['tipster_home_handicap']);
        }

        return $this->returnJson($Datas);
    }

    public function getDataById(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = new MatchBetTeam();
        $Datas = $Datas->tipsterUpcomingMatchBetGetDataById($Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getHomeAway(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = new MatchBetTeam();
        $Datas = $Datas->tipsterUpcomingMatchBetGetHomeAway($Request);
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        $Res = [
            'id' => $Datas->id,
            'home_team' => [
                "name" => ucfirst(strtolower($Datas['football_team_home_name'])),
                "odds" => $Datas['tipster_home_odds'],
                "logo" => $Datas['football_team_home_logo'],
                "handicap" => $Datas['tipster_home_handicap']
            ],
            'away_team' => [
                "name" => ucfirst(strtolower($Datas['football_team_away_name'])),
                "odds" => $Datas['tipster_away_odds'],
                "logo" => $Datas['football_team_away_logo'],
                "handicap" => $Datas['tipster_away_handicap']
            ],
            "over" => $Datas['overs'],
            "over_under_handicap" => $Datas['over_under_handicap'],
            "under" => $Datas['under'],
        ];

        return $this->returnJson($Res);
    }
}
