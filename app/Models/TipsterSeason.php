<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TipsterSeason extends Model
{
    use HasFactory;

    protected $table = 'tipster_season';

    protected $fillable = ['name', 'start_date', 'end_date', 'initialize_balance'];

    function matchBet()
    {
        return $this::select(['*'])->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addDays(1))))->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addDays(1))))
            ->orderBy('name')->get();
    }

    function tipsterSeasonAPIIndex()
    {
        return $this::select(['*', DB::RAW('DATEDIFF(now(), start_date) as dayCounter')])->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now())))->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now())))->latest()->get();
    }

    public function AllUpComingSession()
    {
        return $this::select(['tipster_season.name', 'tipster_season.id'])->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addHours(1))))->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addHours(1))))
            ->orderBy('name')->get();
    }

    public function getDataById($id)
    {
        return $this::select(['name', 'start_date', 'end_date', DB::RAW('DATEDIFF(now(), start_date) as dayCounter')])
            ->find($id);
    }

    public function getActiveSeason()
    {
        return $this::select(['name', 'start_date', 'end_date', DB::RAW('DATEDIFF(now(), start_date) as dayCounter')])
            ->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now())))
            ->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now())))
            ->first();
    }

    public function getDataByUserId($Request)
    {

        return $this::select([
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
            ->whereRaw('tipster_transaction.id NOT IN(select tipster_transaction_id from tipster_transaction_cancel)')
            ->where('tipster_transaction.user_id', $Request->id)
            ->get();
    }
}
