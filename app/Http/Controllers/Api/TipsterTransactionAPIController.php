<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\TipsterTransaction;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class TipsterTransactionAPIController extends Controller
{
    public function index()
    {
        $Datas = TipsterTransaction::latest()->get();


        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getDataById(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = TipsterTransaction::where('id', $Request->id)->first();

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function getDataByUserId(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = new TipsterTransaction();
        $Datas = $Datas->getDataByUserId($Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }

    public function store(Request $Request)
    {
        $Validator = Validator::make($Request->all(), [
            'user_id' => ['required', 'numeric'],
            'tipster_match_bet_id' => ['required', 'numeric'],
            'type' => ['required', 'in:home,away'],
            'bet_type' => ['required', 'in:big,normal'],
            'win_prize' => ['required', 'numeric'],
        ]);


        if ($Validator->passes()) {

            $TipsterTransaction = new TipsterTransaction();
            $TipsterTransaction->user_id  = $Request->user_id;
            $TipsterTransaction->tipster_match_bet_id  = $Request->tipster_match_bet_id;
            $TipsterTransaction->type  = $Request->type;
            $TipsterTransaction->bet_type  = $Request->bet_type;
            date_default_timezone_set('GMT');
            $TipsterTransaction->place_bet_time  =  date('Y-m-d H:i:s', strtotime(Carbon::now()->timezone("GMT")));
            $TipsterTransaction->win_prize = $Request->win_prize;
            $TipsterTransaction->save();

            if (Auth::user() == null)
                $ActorId = 0;
            else
                $ActorId =  Auth::user()->id;

            $InsertLog = DB::table('tipster_balance_log');
            date_default_timezone_set('Asia/Jakarta');
            $DataLog = [
                'user_id' => $Request->user_id,
                'type' => 'debit',
                'action_type' => 'bet',
                'tipster_transaction_id' => $TipsterTransaction->id,
                'actor_id' => $ActorId,
                'created_at' => date('Y-m-d H:i:s', strtotime(Carbon::now())),
                'tipster_transaction_cancel_id' => 0
            ];
            $InsertLog->insert($DataLog);

            return $this->returnJson(['success' => 'Added new record.']);
        }
        return $this->returnJson(['error' => $Validator->errors()->all()], 422, false);
    }
}
