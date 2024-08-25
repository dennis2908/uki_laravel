<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;


class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        $rules = [
            'type' => ['required', 'in:home,away,over,under'],
            'token' => ['required'],
            'bet_type' => ['required', 'in:big,normal'],
            'tipster_match_id' => ['required'],
        ];

        $messages = [];

        $attributes = [
            'type' => 'Type',
            'token' => 'Token',
            'tipster_match_id' => 'ID',
            'bet_type' => 'Bet Type',
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

            $user = DB::table('user')
                ->select(['id'])
                ->where('token', $data['token'])
                ->first();

            $tipsterUser = DB::table('tipster_user')->where('user_id', $user->id)->first();

            if(empty($user)){
                return $this->returnJson([], 404, false, 'User not found');
            }

            $data['tipster_match_id'] = Crypt::decrypt($data['tipster_match_id']);

            $matchTipster = DB::table('tipster_match_bet')
                ->select([
                    'tipster_match_bet.id',
                    'home.odds as odds_home',
                    'away.odds as odds_away',
                    'tipster_match_bet.odds_over',
                    'tipster_match_bet.odds_under',
                    'tipster_match_bet.bet_price',
                    'tipster_match_bet.big_bet_price'

                ])
                ->join('tipster_home_team as home', 'home.tsr_match_bet_id', '=', 'tipster_match_bet.id')
                ->join('tipster_away_team as away', 'away.tsr_match_bet_id', '=', 'tipster_match_bet.id')
                ->where('tipster_match_bet.id', $data['tipster_match_id'])
                ->first();

            $betPrice = $data['bet_type'] == 'big' ? $matchTipster->big_bet_price : $matchTipster->bet_price;

            if(empty($matchTipster)){
                return $this->returnJson([], 404, false, 'Match not found');
            }

            if($betPrice > $tipsterUser->balance){
                return $this->returnJson([], 422, false, 'Insufficient balance.');
            }

            if($data['type'] == 'home'){
                $winPrize = $betPrice * $matchTipster->odds_home;
            }else if($data['type'] == 'away'){
                $winPrize = $betPrice * $matchTipster->odds_away;
            }else if($data['type'] == 'over'){
                $winPrize = $betPrice * $matchTipster->odds_over;
            }else if($data['type'] == 'under'){
                $winPrize = $betPrice * $matchTipster->odds_under;
            }

            $createData = DB::table('tipster_transaction')->insertGetId([
                'user_id' => $user->id,
                'tipster_match_bet_id' => $data['tipster_match_id'],
                'type' => $data['type'],
                'bet_type' => $data['bet_type'],
                'win_prize' => $winPrize,
                'status' => 1,
                'place_bet_time' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $getTipsterUser = DB::table('tipster_user')->where('user_id', $user->id)->first();

            DB::table('tipster_user')->where('id', $getTipsterUser->id)->update([
                'balance' => $getTipsterUser->balance - $betPrice,
                'open_bet' => $getTipsterUser->open_bet + $betPrice,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            DB::table('tipster_balance_log')
            ->insert([
                'user_id' => $user->id,
                'type' => 'credit',
                'action_type' => 'bet',
                'tipster_transaction_id' => $createData,
                'actor_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'tipster_transaction_cancel_id' => 0,
                'balance' => $betPrice
            ]);

            DB::commit();

            $message = 'Tipster transaction created successfully';
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

    public function cancel(Request $request)
    {
        $data = $request->all();

        $rules = [
            'token' => ['required'],
            'tipster_match_id' => ['required'],
        ];

        $messages = [];

        $attributes = [
            'token' => 'Token',
            'tipster_match_id' => 'ID'
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

            $user = DB::table('user')
                ->select(['id'])
                ->where('token', $data['token'])
                ->first();

            if(empty($user)){
                return $this->returnJson([], 404, false, 'User not found');
            }

            $data['tipster_match_id'] = Crypt::decrypt($data['tipster_match_id']);

            $getTipsterUser = DB::table('tipster_user')->where('user_id', $user->id)->first();

            $getTransaction = DB::table('tipster_transaction')
                ->select(['id', 'bet_type'])
                ->where('tipster_match_bet_id', $data['tipster_match_id'])
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->first();

            $getMatch = DB::table('tipster_match_bet')
                ->select(['*'])
                ->where('id', $data['tipster_match_id'])
                ->first();

            $betPrice = $getTransaction->bet_type == 'big' ? $getMatch->big_bet_price : $getMatch->bet_price;

            DB::table('tipster_transaction')->where('id', $getTransaction->id)->update([
                'status' => 5
            ]);

            $tipsterCancel = DB::table('tipster_transaction_cancel')->insertGetId([
                'user_id' => $user->id,
                'tipster_transaction_id' => $getTransaction->id,
                'cancel_time' => date('Y-m-d H:i:s')
            ]);

            DB::table('tipster_user')->where('id', $getTipsterUser->id)->update([
                'balance' => $getTipsterUser->balance + $betPrice,
                'open_bet' => $getTipsterUser->open_bet - $betPrice,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            DB::table('tipster_balance_log')
            ->insert([
                'user_id' => $user->id,
                'type' => 'debit',
                'action_type' => 'bet',
                'tipster_transaction_id' => $getTransaction->id,
                'actor_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'tipster_transaction_cancel_id' => $tipsterCancel,
                'balance' => $betPrice
            ]);

            DB::commit();

            $message = 'Tipster transaction canceled successfully';
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
}
