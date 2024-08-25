<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipsterTransaction extends Model
{
    use HasFactory;

    protected $table = 'tipster_transaction';

    protected $fillable = ['user_id', 'tipster_match_bet_id', 'type', 'bet_type', 'place_bet_time', 'win_prize', 'status'];

    function transactionCancelCreate()
    {
        return $this::select([
            'tipster_user.*'
        ])
            ->join('tipster_user', 'tipster_transaction.user_id', '=', 'tipster_user.id')
            ->join('tipster_match_bet', 'tipster_match_bet.id', '=', 'tipster_transaction.tipster_match_bet_id')
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->whereRaw('tipster_transaction.id NOT IN(select tipster_transaction_id from tipster_transaction_cancel)')
            ->groupBy('tipster_user.id')
            ->get();
    }

    function transactionCancelEdit($Data)
    {
        return $this->select([
            'tipster_transaction.id', 'tipster_user.username',
            DB::RAW("case tipster_transaction.status
                        when '1' then 'Waiting for match'
                        when '2' then 'Match in progress'
                        when '3' then 'Win the prize'
                        when '4' then 'Lose the prize'
                        when '5' then 'Canceled bet'
                        ELSE '-'
                    end as status"),
            DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_over,3,'en_US'),' - ',FORMAT(odds_under,3,'en_US'),
                ' - ',FORMAT(bet_price,3,'en_US'),' - ',FORMAT(big_bet_price,3,'en_US')) as tipster_match_bet"),
        ])
            ->join('tipster_user', 'tipster_transaction.user_id', '=', 'tipster_user.id')
            ->join('tipster_match_bet', 'tipster_match_bet.id', '=', 'tipster_transaction.tipster_match_bet_id')
            ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
            ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
            ->whereRaw('(tipster_transaction.id NOT IN(select tipster_transaction_id from tipster_transaction_cancel) or tipster_transaction.id = ' . $Data->tipster_transaction_id . ')')
            ->where('tipster_user.id', $Data->user_id)
            ->get();
    }
}
