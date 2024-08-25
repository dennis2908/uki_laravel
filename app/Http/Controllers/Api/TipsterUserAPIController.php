<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use App\Models\TipsterUser;

use App\Models\TipsterSeason;

use Carbon\Carbon;

class TipsterUserAPIController extends Controller
{
    public function index()
    {
        $Datas = new TipsterUser();
        $Datas = $Datas->TipsterUserIndex();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }
    public function getUser(Request $Request)
    {
        if ($Request->token === null)
            return $this->returnJson(["error" => "token field is required"], 422, false);

        $Datas =  new TipsterUser();
        $Datas = $Datas->getUser($Request);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        $Datas->balance = number_format($Datas->balance, 0, ',', '.');
        $Datas->open_bet = number_format($Datas->open_bet, 0, ',', '.');

        return $this->returnJson($Datas);
    }

    public function store(Request $Request)
    {
        $Validator = Validator::make($Request->all(), [
            'token' => ['required'],
            'username' => ['required', 'unique:tipster_user,username']
        ]);

        if ($Validator->passes()) {
            $existSeason = TipsterSeason::select(['initialize_balance'])
                ->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now())))
                ->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now())))
                ->first();

            $getUser = DB::table('user')->where('token', $Request->token)->first();

            if (empty($getUser)) {
                return $this->returnJson(['error' => ['User not found']], 404, false);
            }

            $initializeBalance = $existSeason->initialize_balance ?? 0;

            $TipsterUserBalance = new TipsterUser();
            $TipsterUserBalance->user_id  = $getUser->id;
            $TipsterUserBalance->balance  = $initializeBalance;
            $TipsterUserBalance->open_bet  = 0;
            $TipsterUserBalance->username  = $Request->username;
            $TipsterUserBalance->save();

            $InsertLog = DB::table('tipster_balance_log');
            date_default_timezone_set('Asia/Jakarta');

            $DataLog = [
                'user_id' => $getUser->id,
                'type' => 'debit',
                'action_type' => 'adjustment',
                'tipster_transaction_id' => 0,
                'actor_id' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime(Carbon::now())),
                'tipster_transaction_cancel_id' => 0
            ];
            $InsertLog->insert($DataLog);

            return $this->returnJson(['success' => 'Added new record.']);
        } else {
            return $this->returnJson(['error' => $Validator->errors()->all()], 422, false);
        }
    }
}
