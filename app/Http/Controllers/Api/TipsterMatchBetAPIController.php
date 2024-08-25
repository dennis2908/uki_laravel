<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\MatchBetTeam;
use Illuminate\Support\Facades\DB;

class TipsterMatchBetAPIController extends Controller
{
    public function index()
    {
        $Datas = new MatchBetTeam();
        $Datas = $Datas->tipsterMatchBetAPIIndex();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }


    public function getDataByIdNoRedun(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas =  new MatchBetTeam();
        $Datas =  $Datas->getDataByIdNoRedun($Request);
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getDataById(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas =  new MatchBetTeam();
        $Datas =  $Datas->tipsterMatchBetAPIGetHomeAway($Request);
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getHomeAway(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas =  new MatchBetTeam();
        $Datas =  $Datas->tipsterUpcomingMatchBetGetHomeAway($Request);
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
            "under" => $Datas['under'],
        ];

        return $this->returnJson($Res);
    }
}
