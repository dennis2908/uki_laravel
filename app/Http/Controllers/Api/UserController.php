<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        $rules = [
            'token' => ['required'],
            'username' => ['required', 'unique:tipster_user,username'],
        ];

        $messages = [];

        $attributes = [
            'token' => 'Token',
            'username' => 'Username'
        ];

        $validator = Validator::make($data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            return $this->returnJson([
                $validator->errors()
            ], 422, false, 'Validation error!');
        }

        $IsError = false;

        try {
            DB::beginTransaction();

            $existSeason = DB::table('tipster_season')->select(['initialize_balance'])
                ->where('start_date', '<=', date('Y-m-d H:i:s'))
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->first();

            $getUser = DB::table('user')->where('token', $data['token'])->first();

            if (empty($getUser)) {
                return $this->returnJson(['error' => ['User not found']], 404, false);
            }

            $initializeBalance = $existSeason->initialize_balance ?? 0;

            DB::table('tipster_user')
            ->insert([
                'user_id' => $getUser->id,
                'balance' => $initializeBalance,
                'open_bet' => 0,
                'username' => $data['username'],
            ]);

            DB::table('tipster_balance_log')
            ->insert([
                'user_id' => $getUser->id,
                'type' => 'debit',
                'action_type' => 'adjustment',
                'tipster_transaction_id' => 0,
                'actor_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'tipster_transaction_cancel_id' => 0,
                'balance' => $initializeBalance
            ]);

            DB::commit();

            $message = 'User created successfully';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $IsError = true;

            $err     = $e->errorInfo;

            $message =  $err[2];
        }

        if ($IsError == true) {
            return $this->returnJson([], 500, false, $message);
        } else {
            return $this->returnJson([], 200, true, $message);
        }
    }

    public function mostTipsters()
    {
        $datas = DB::select("
            SELECT
                tipster_user.username,
                SUM(IF(tipster_transaction.bet_type = 'normal', tipster_match_bet.bet_price, tipster_match_bet.big_bet_price)) AS tipsting_bet
            FROM
                tipster_season
            JOIN
                tipster_match_bet
                ON tipster_match_bet.tipster_season_id = tipster_season.id
            JOIN
                tipster_transaction
                ON tipster_transaction.tipster_match_bet_id = tipster_match_bet.id
                AND tipster_transaction.status != 5
            JOIN
                tipster_user
                ON tipster_user.user_id = tipster_transaction.user_id
            WHERE
                tipster_season.start_date <= NOW()
                AND tipster_season.end_date >= NOW()
            GROUP BY
                tipster_user.id
            ORDER BY
                SUM(IF(tipster_transaction.bet_type = 'normal', tipster_match_bet.bet_price, tipster_match_bet.big_bet_price)) DESC
            LIMIT
                100
        ");

        foreach($datas as $key => $val){
            $datas[$key]->tipsting_bet = number_format($val->tipsting_bet, 0, ',', '.');
        }

        return $this->returnJson($datas, 200, true);
    }
}
