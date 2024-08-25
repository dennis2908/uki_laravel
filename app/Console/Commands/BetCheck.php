<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BetCheck extends Command
{
    protected $signature = 'bet:check';

    protected $description = 'Bet status check every 5 minutes';

    public function handle()
    {
        $dateNow = strtotime(date('Y-m-d H:i:s', strtotime('2024-07-09 15:00:00')));

        $matches = DB::table('tipster_match_bet')
        ->select([
            'tipster_match_bet.id',
            'tipster_match_bet.odds_over',
            'tipster_match_bet.odds_under',
            'tipster_match_bet.handicap',
            'football_match.home_scores',
            'football_match.away_scores',
            'football_match.match_time',
            'football_match.status_id',
            'tipster_away_team.odds as away_odds',
            'tipster_home_team.odds as home_odds',
            'tipster_match_bet.bet_price'
        ])
        ->join('football_match', 'football_match.id', DB::raw('`tipster_match_bet`.`football_match_id` COLLATE utf8mb4_0900_ai_ci'))
        ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", 'tipster_match_bet.id')
        ->whereIn('football_match.status_id', [0, 8, 9, 10, 11, 12, 13])
        ->where('football_match.match_time', '<', $dateNow)
        ->where('tipster_match_bet.status', 1)
        ->get();

        foreach($matches as $key => $val){
            DB::table('tipster_match_bet')
                ->where('id', $val->id)
                ->update(['status' => 2]);

            $homeScore = json_decode($val->home_scores);
            $awayScore = json_decode($val->away_scores);

            $homeScore = ($homeScore[5] != 0) ? $homeScore[5] : ($homeScore[4] != 0 ? $homeScore[4] : ($homeScore[0]));
            $awayScore = ($awayScore[5] != 0) ? $awayScore[5] : ($awayScore[4] != 0 ? $awayScore[4] : ($awayScore[0]));

            $summaryScore = $homeScore + $awayScore;

            $transactions = DB::table('tipster_transaction')
            ->where('tipster_match_bet_id', $val->id)
            ->where('status', 1)
            ->get();

            foreach($transactions as $val2){
                if($val2->type == 'over'){
                    $oddsOver = $val->odds_over;

                    $getUser = DB::table('tipster_user')
                        ->where('user_id', $val2->user_id)
                        ->first();

                    try{
                        DB::beginTransaction();

                        if($summaryScore > $oddsOver){
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                    'balance' => $getUser->balance + $val2->win_prize,
                                ]);

                            DB::table('tipster_balance_log')
                                ->insert([
                                    'user_id' => $getUser->id,
                                    'type' => 'debit',
                                    'action_type' => 'win',
                                    'tipster_transaction_id' => $val2->id,
                                    'actor_id' => 0,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'tipster_transaction_cancel_id' => 0,
                                    'balance' => $val2->win_prize
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 3
                                ]);
                        }else{
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 4
                                ]);
                        }

                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }

                if($val2->type == 'under'){
                    $oddsUnder = $val->odds_under;

                    $getUser = DB::table('tipster_user')
                        ->where('user_id', $val2->user_id)
                        ->first();

                    try{
                        DB::beginTransaction();

                        if($summaryScore < $oddsUnder){
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                    'balance' => $getUser->balance + $val2->win_prize,
                                ]);

                            DB::table('tipster_balance_log')
                                ->insert([
                                    'user_id' => $getUser->id,
                                    'type' => 'debit',
                                    'action_type' => 'win',
                                    'tipster_transaction_id' => $val2->id,
                                    'actor_id' => 0,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'tipster_transaction_cancel_id' => 0,
                                    'balance' => $val2->win_prize
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 3
                                ]);
                        }else{
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 4
                                ]);
                        }

                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }

                if($val2->type == 'home'){
                    $oddsHome = $val->home_odds;

                    $getUser = DB::table('tipster_user')
                        ->where('user_id', $val2->user_id)
                        ->first();

                    try{
                        DB::beginTransaction();

                        if($homeScore > $oddsHome){
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                    'balance' => $getUser->balance + $val2->win_prize,
                                ]);

                            DB::table('tipster_balance_log')
                                ->insert([
                                    'user_id' => $getUser->id,
                                    'type' => 'debit',
                                    'action_type' => 'win',
                                    'tipster_transaction_id' => $val2->id,
                                    'actor_id' => 0,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'tipster_transaction_cancel_id' => 0,
                                    'balance' => $val2->win_prize
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 3
                                ]);
                        }else{
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 4
                                ]);
                        }

                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }

                if($val2->type == 'away'){
                    $oddsAway = $val->away_odds;

                    $getUser = DB::table('tipster_user')
                        ->where('user_id', $val2->user_id)
                        ->first();

                    try{
                        DB::beginTransaction();

                        if($homeScore > $oddsAway){
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                    'balance' => $getUser->balance + $val2->win_prize,
                                ]);

                            DB::table('tipster_balance_log')
                                ->insert([
                                    'user_id' => $getUser->id,
                                    'type' => 'debit',
                                    'action_type' => 'win',
                                    'tipster_transaction_id' => $val2->id,
                                    'actor_id' => 0,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'tipster_transaction_cancel_id' => 0,
                                    'balance' => $val2->win_prize
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 3
                                ]);
                        }else{
                            DB::table('tipster_user')
                                ->where('id', $getUser->id)
                                ->update([
                                    'open_bet' => $getUser->open_bet - $val->bet_price,
                                ]);

                            DB::table('tipster_transaction')
                                ->where('id', $val2->id)
                                ->update([
                                    'status' => 4
                                ]);
                        }

                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }
            }
        }
    }
}
