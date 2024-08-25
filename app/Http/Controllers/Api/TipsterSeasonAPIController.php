<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\TipsterSeason;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TipsterSeasonAPIController extends Controller
{
    public function index()
    {
        $Datas = new TipsterSeason();

        $Datas  = $Datas->TipsterSeasonAPIIndex();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function AllUpComingSession()
    {

        $Datas = new TipsterSeason();

        $Datas  = $Datas->AllUpComingSession();

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }
    public function getDataById(Request $request)
    {
        $id = $request->id;

        if (empty($id)) {
            return $this->returnJson(['error' => ['ID is Required']], 422, false);
        }

        $Datas = new TipsterSeason();
        $Datas = $Datas->getDataById($id);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        $Datas['start_date'] = date('d F Y', strtotime($Datas['start_date']));
        $Datas['end_date'] = date('d F Y', strtotime($Datas['end_date']));

        return $this->returnJson($Datas);
    }

    public function getActiveSeason()
    {
        $Datas = new TipsterSeason();
        $Datas = $Datas->getActiveSeason();

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        $Datas['start_date'] = date('d F Y', strtotime($Datas['start_date']));
        $Datas['end_date'] = date('d F Y', strtotime($Datas['end_date']));

        return $this->returnJson($Datas);
    }
}
