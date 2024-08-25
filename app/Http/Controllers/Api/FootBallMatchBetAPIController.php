<?php

namespace App\Http\Controllers\Api;

use App\Models\FootBallMatch;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\TipsterSeason;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FootBallMatchBetAPIController extends Controller
{
    public function getDataByMatchTime(Request $Request)
    {
        $Data = $Request->all();
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);


        if ($Request->start_date_match === null)
            return $this->returnJson(["error" => "start_date_match field is required"], 422, false);

        if ($Request->end_date_match === null)
            return $this->returnJson(["error" => "end_date_match field is required"], 422, false);

        $Rules = [
            'id' => ['required', 'numeric'],
            'start_date_match' => ['required', 'date'],
            'end_date_match' => ['required', 'date'],
        ];

        $Messages = [];

        $Attributes = [
            'id' => 'Id',
            'start_date_match' => 'Start Date Match',
            'end_date_match' => 'End Date Match'
        ];

        $Validator = Validator::make($Data, $Rules, $Messages, $Attributes);

        if ($Validator->fails()) {
            return response()->json([
                'code' => 422,
                'success' => false,
                'message' => 'Validation error!',
                'data' => $Validator->errors()
            ], 422)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ]);
        }

        $DatasMatchBet = TipsterSeason::find($Request['id']);

        if (empty($DatasMatchBet)) {
            return $this->returnJson($DatasMatchBet, 404, false);
        }

        $Datas = new FootBallMatch();

        $Datas = $Datas->getDataByMatchTime($DatasMatchBet, $Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getDataById(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = new FootBallMatch();

        $Datas = $Datas->getDataById($Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getDataByFS(Request $Request)
    {
        if ($Request->football_match_id === null)
            return $this->returnJson(["error" => "football match id field is required"], 422, false);


        if ($Request->tipster_season_id === null)
            return $this->returnJson(["error" => "tipster season id field is required"], 422, false);

        $Datas = new FootBallMatch();

        $Datas = $Datas->getDataByFS($Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }
}
