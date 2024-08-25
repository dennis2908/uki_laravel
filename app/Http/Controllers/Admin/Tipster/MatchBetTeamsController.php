<?php

namespace App\Http\Controllers\Admin\Tipster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\MatchBetTeam;
use App\Models\TipsterSeason;
use App\Models\TipsterAwayTeam;
use App\Models\TipsterHomeTeam;
use App\Models\FootBallMatch;
use Yajra\DataTables\DataTables;
use Exception;

class MatchBetTeamsController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('tipster_match_bet')->join("tipster_season", "tipster_season.id", "=", "tipster_match_bet.tipster_season_id")
                ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
                ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
                ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")->select([
                    'tipster_match_bet.*',
                    DB::RAW('FROM_UNIXTIME(match_time, "%d-%M-%Y %H:%i:%s") as unixdatetime'),
                    DB::RAW('tipster_season.name as tipster_season_name'),
                    DB::RAW('tipster_away_team.id as away_team_id'),
                    DB::RAW('tipster_away_team.odds as away_team_odds'),
                    DB::RAW('tipster_away_team.handicap as away_team_handicap'),
                    DB::RAW('tipster_away_team.football_team_id as away_team_football_team_id'),
                    DB::RAW('tipster_home_team.id as home_team_id'),
                    DB::RAW('tipster_home_team.odds as home_team_odds'),
                    DB::RAW('tipster_home_team.handicap as home_team_handicap'),
                    DB::RAW('tipster_home_team.football_team_id as home_team_football_team_id'),
                ])->where('tipster_match_bet.tipster_season_id', $Request['idSession']);

            if ($ReqDatatable['orderable']) {
                foreach ($ReqDatatable['orderable'] as $Order) {
                    if ($Order['column'] == 'rownum') {
                        $Datas = $Datas->orderBy('id', $Order['dir']);
                    } else {
                        if (!empty($val['column'])) {
                            $Datas = $Datas->orderBy($Order['column'], $Order['dir']);
                        }
                    }
                }
            } else {
                $Datas = $Datas->orderBy('id', 'desc');
            }

            $Datatables = DataTables::of($Datas);

            if (isset($ReqDatatable['orderable']['rownum'])) {
                if ($ReqDatatable['orderable']['rownum']['dir'] == 'desc') {
                    $RowNum      = abs($Datas->count() - ($ReqDatatable['start'] * $ReqDatatable['length']));
                    $IsIncrease = false;
                } else {
                    $RowNum = ($ReqDatatable['start'] * $ReqDatatable['length']) + 1;
                    $IsIncrease = true;
                }
            } else {
                $RowNum      = ($ReqDatatable['start'] * $ReqDatatable['length']) + 1;
                $IsIncrease = true;
            }

            return $Datatables
                ->addColumn('rownum', function () use (&$RowNum, $IsIncrease) {
                    if ($IsIncrease == true) {
                        return $RowNum++;
                    } else {
                        return $RowNum--;
                    }
                })
                ->editColumn('created_at', function ($Data) {
                    return date('d F Y H:i:s', strtotime($Data->created_at));
                })
                ->editColumn('updated_at', function ($Data) {
                    return date('d F Y H:i:s', strtotime($Data->updated_at));
                })
                ->addColumn('unixdatetime', function ($Data) {
                    return $Data->unixdatetime;
                })
                ->filterColumn('unixdatetime', function ($query, $keyword) {
                    $sql = 'LOWER(FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s") LIKE ?)';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('unixdatetime', function ($query, $order) {
                    $query->orderBy(DB::RAW('FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s")'), $order);
                })
                ->addColumn('tipster_season_name', function ($Data) {
                    return $Data->tipster_season_name;
                })
                ->filterColumn('tipster_season_name', function ($query, $keyword) {
                    $sql = "LOWER(tipster_season.name LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('tipster_season_name', function ($query, $order) {
                    $query->orderBy('tipster_season.name', $order);
                })
                ->addColumn('away_team_odds', function ($Data) {
                    return $Data->away_team_odds;
                })
                ->filterColumn('away_team_odds', function ($query, $keyword) {
                    $sql = "LOWER(tipster_away_team.odds LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('away_team_odds', function ($query, $order) {
                    $query->orderBy('tipster_away_team.odds', $order);
                })
                ->addColumn('away_team_handicap', function ($Data) {
                    return $Data->away_team_handicap;
                })
                ->filterColumn('away_team_handicap', function ($query, $keyword) {
                    $sql = "LOWER(tipster_away_team.handicap LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('away_team_handicap', function ($query, $order) {
                    $query->orderBy('tipster_away_team.handicap', $order);
                })
                ->addColumn('away_team_football_team_id', function ($Data) {
                    return $Data->away_team_football_team_id;
                })
                ->filterColumn('away_team_football_team_id', function ($query, $keyword) {
                    $sql = "LOWER(tipster_away_team.football_team_id LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('away_team_football_team_id', function ($query, $order) {
                    $query->orderBy('tipster_away_team.football_team_id', $order);
                })
                ->addColumn('home_team_odds', function ($Data) {
                    return $Data->home_team_odds;
                })
                ->filterColumn('home_team_odds', function ($query, $keyword) {
                    $sql = "LOWER(tipster_home_team.odds LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('home_team_odds', function ($query, $order) {
                    $query->orderBy('tipster_home_team.odds', $order);
                })
                ->addColumn('home_team_handicap', function ($Data) {
                    return $Data->home_team_handicap;
                })
                ->filterColumn('home_team_handicap', function ($query, $keyword) {
                    $sql = "LOWER(tipster_home_team.handicap LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('home_team_handicap', function ($query, $order) {
                    $query->orderBy('tipster_home_team.handicap', $order);
                })
                ->addColumn('home_team_football_team_id', function ($Data) {
                    return $Data->home_team_football_team_id;
                })
                ->filterColumn('home_team_football_team_id', function ($query, $keyword) {
                    $sql = "LOWER(tipster_home_team.football_team_id LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('home_team_football_team_id', function ($query, $order) {
                    $query->orderBy('tipster_home_team.football_team_id', $order);
                })
                ->addColumn('action', function ($Data) {
                    $Html = '<div class="dropdown dropdown-inline mr-1"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                    //* EDIT
                    $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/tipster/match-bet/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                    //* DELETE
                    $Html .= '<li class="nav-item"><a class="nav-link btn-delete" href="' . url('admin/tipster/match-bet/delete/' . $Data->id . "/" . $Data->away_team_id . "/" . $Data->home_team_id) . '"><i class="flaticon2-delete nav-icon"></i><span class="nav-text">Delete</span></a></li>';
                    $Html .= '</ul></div></div>';

                    return $Html;
                })
                ->rawColumns(['action'])
                ->toJson(true);
        }

        return view('pages.tipster.match-bet.index');
    }

    public function create()
    {
        $DataSeason = new TipsterSeason();

        $DataSeason = $DataSeason->matchBet();


        return view('pages.tipster.match-bet.create', compact('DataSeason'));
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'odds_over' => ['required', 'numeric', 'between:0,999999999999.999'],
            'away_team_odds' => ['required', 'numeric', 'between:0,999999999999.999'],
            'home_team_odds' => ['required', 'numeric', 'between:0,999999999999.999'],
            'odds_under' => ['required', 'numeric', 'between:0,999999999999.999'],
            'bet_price' => ['required', 'numeric', 'between:0,999999999999.999'],
            'big_bet_price' => ['required', 'numeric', 'between:0,999999999999.999'],
            'handicap' => ['required', 'numeric', 'between:0,99999999.9999'],
            'away_team_handicap' => ['required', 'numeric', 'between:0,99999999.9999'],
            'home_team_handicap' => ['required', 'numeric', 'between:0,99999999.9999'],
            'tipster_season_id' => ['required', 'numeric'],
            'football_match_id' => ['required'],
            'away_team_football_team_id' => ['required'],
            'home_team_football_team_id' => ['required'],
        ];

        $Messages = [];

        $Attributes = [
            'odds_over' => 'Odds Over',
            'away_team_odds' => 'Away Team Odds',
            'home_team_odds' => 'Home Team Odds',
            'odds_under' => 'Odds Under',
            'handicap' => 'Handicap Match Bet',
            'bet_price' => 'Bet Price',
            'big_bet_price' => 'Big Bet Price',
            'away_team_handicap' => 'Away Team Handicap',
            'home_team_handicap' => 'Home Team Handicap',
            'tipster_season_id' => 'Tipster Season',
            'football_match_id' => 'Football Match',
            'away_team_football_team_id' => 'Away Team Football Team Id',
            'home_team_football_team_id' => 'Home Team Football Team Id',
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

        $IsError = false;

        try {
            DB::beginTransaction();

            $MatchBetTeam = new MatchBetTeam();

            $MatchBetTeam->fill([
                'odds_over' => $Data['odds_over'],
                'odds_under' => $Data['odds_under'],
                'bet_price' => $Data['bet_price'],
                'big_bet_price' => $Data['big_bet_price'],
                'tipster_season_id' => $Data['tipster_season_id'],
                'football_match_id' => $Data['football_match_id'],
                'handicap' => $Data['handicap']
            ])->save();

            $TipsterAwayTeam = new TipsterAwayTeam();

            $TipsterAwayTeam->fill([
                'tsr_match_bet_id' => $MatchBetTeam->id,
                'odds' => $Data['away_team_odds'],
                'handicap' => $Data['away_team_handicap'],
                'football_team_id' => $Data['away_team_football_team_id'],
            ])->save();

            $TipsterHomeTeam = new TipsterHomeTeam();

            $TipsterHomeTeam->fill([
                'tsr_match_bet_id' => $MatchBetTeam->id,
                'odds' => $Data['home_team_odds'],
                'handicap' => $Data['home_team_handicap'],
                'football_team_id' => $Data['home_team_football_team_id'],
            ])->save();

            DB::commit();

            $Message = 'Macth Bet created successfully';
        } catch (\Illuminate\Database\QueryException $E) {
            DB::rollBack();

            $IsError = true;

            $Err     = $E->errorInfo;

            $Message =  $Err[2];
        }

        if ($IsError == true) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $Message
            ], 500)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ]);
        } else {
            session()->flash('success', $Message);

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => $Message,
                'redirect' => url('admin/tipster/match-bet')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function dataSeason(Request $Request)
    {
        $ReqDatatable  = $this->requestDatatables($Request->input());

        $Datas = DB::table('tipster_season');

        if ($ReqDatatable['orderable']) {
            foreach ($ReqDatatable['orderable'] as $Order) {
                if ($Order['column'] == 'rownum') {
                    $Datas = $Datas->orderBy('id', $Order['dir']);
                } else {
                    if (!empty($val['column'])) {
                        $Datas = $Datas->orderBy($Order['column'], $Order['dir']);
                    }
                }
            }
        } else {
            $Datas = $Datas->orderBy('id', 'desc');
        }

        $Datatables = DataTables::of($Datas);

        if (isset($ReqDatatable['orderable']['rownum'])) {
            if ($ReqDatatable['orderable']['rownum']['dir'] == 'desc') {
                $RowNum      = abs($Datas->count() - ($ReqDatatable['start'] * $ReqDatatable['length']));
                $IsIncrease = false;
            } else {
                $RowNum = ($ReqDatatable['start'] * $ReqDatatable['length']) + 1;
                $IsIncrease = true;
            }
        } else {
            $RowNum      = ($ReqDatatable['start'] * $ReqDatatable['length']) + 1;
            $IsIncrease = true;
        }

        return $Datatables
            ->addColumn('rownum', function () use (&$RowNum, $IsIncrease) {
                if ($IsIncrease == true) {
                    return $RowNum++;
                } else {
                    return $RowNum--;
                }
            })
            ->editColumn('created_at', function ($Data) {
                return date('d F Y H:i:s', strtotime($Data->created_at));
            })
            ->editColumn('updated_at', function ($Data) {
                return date('d F Y H:i:s', strtotime($Data->updated_at));
            })
            ->addColumn('action', function ($Data) {
                $Html = '<div class="dropdown dropdown-inline mw-100"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                //* EDIT
                $Html .= '<li class="nav-item"><a class="nav-link" onclick="openTableMatchBet(' . $Data->id . ')"><i class="flaticon2-open-box nav-icon"></i><span class="nav-text">Match Bet</span></a></li>';


                //* DELETE
                $Html .= '</ul></div></div>';

                return $Html;
            })
            ->rawColumns(['action'])
            ->toJson(true);
    }

    public function edit($Id)
    {
        $Data = new MatchBetTeam();
        $Data = $Data->matchBetEdit($Id);

        if (empty($Data))
            return redirect('admin/tipster/match-bet')->with(['error' => 'Data not exists']);

        $DataSeason = new TipsterSeason();

        $DataSeason = $DataSeason->matchBet();

        $DataFootball = new FootBallMatch();

        $DataFootball = $DataFootball->matchBetEdit($Data);

        return view('pages.tipster.match-bet.edit', compact('Data', 'DataSeason', 'DataFootball'));
    }

    public function update($Id, $AwayTeamId, $HomeTeamId, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'odds_over' => ['required', 'numeric', 'between:0,999999999999.999'],
            'away_team_odds' => ['required', 'numeric', 'between:0,999999999999.999'],
            'home_team_odds' => ['required', 'numeric', 'between:0,999999999999.999'],
            'odds_under' => ['required', 'numeric', 'between:0,999999999999.999'],
            'bet_price' => ['required', 'numeric', 'between:0,999999999999.999'],
            'big_bet_price' => ['required', 'numeric', 'between:0,999999999999.999'],
            'handicap' => ['required', 'numeric', 'between:0,99999999.9999'],
            'away_team_handicap' => ['required', 'numeric', 'between:0,999999999999.9999'],
            'home_team_handicap' => ['required', 'numeric', 'between:0,999999999999.9999'],
            'football_match_id' => ['required'],
            'away_team_football_team_id' => ['required'],
            'home_team_football_team_id' => ['required'],
        ];

        $Messages = [];

        $Attributes = [
            'odds_over' => 'Odds Over',
            'away_team_odds' => 'Away Team Odds',
            'home_team_odds' => 'Home Team Odds',
            'odds_under' => 'Odds Under',
            'handicap' => 'Handicap',
            'bet_price' => 'Bet Price',
            'big_bet_price' => 'Big Bet Price',
            'away_team_handicap' => 'Away Team Handicap',
            'home_team_handicap' => 'Home Team Handicap',
            'football_match_id' => 'Football Match',
            'away_team_football_team_id' => 'Away Team Football Team Id',
            'home_team_football_team_id' => 'Home Team Football Team Id',
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

        $IsError = false;

        try {
            DB::beginTransaction();

            $MatchBet = MatchBetTeam::find($Id);

            $DataMatchBet = [
                'odds_over' => $Data['odds_over'],
                'odds_under' => $Data['odds_under'],
                'bet_price' => $Data['bet_price'],
                'big_bet_price' => $Data['big_bet_price'],
                'football_match_id' => $Data['football_match_id'],
                'handicap' => $Data['handicap']
            ];

            $MatchBet->fill($DataMatchBet)->save();

            $TipsterAwayTeam = TipsterAwayTeam::find($AwayTeamId);

            $DataTipsterAwayTeam = [
                'odds' => $Data['away_team_odds'],
                'handicap' => $Data['away_team_handicap'],
                'football_team_id' => $Data['away_team_football_team_id'],
            ];

            $TipsterAwayTeam->fill($DataTipsterAwayTeam)->save();

            $TipsterHomeTeam = TipsterHomeTeam::find($HomeTeamId);

            $DataTipsterHomeTeam = [
                'odds' => $Data['home_team_odds'],
                'handicap' => $Data['home_team_handicap'],
                'football_team_id' => $Data['home_team_football_team_id'],
            ];

            $TipsterHomeTeam->fill($DataTipsterHomeTeam)->save();

            DB::commit();

            $Message = 'Match Bet updated successfully';
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            $IsError = true;

            $Err     = $e->errorInfo;

            $Message =  $Err[2];
        }

        if ($IsError == true) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => $Message
            ], 500)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ]);
        } else {
            session()->flash('success', $Message);

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => $Message,
                'redirect' => url('admin/tipster/match-bet')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id, $IdTipsterAwayTeam, $IdTipsterHomeTeam)
    {
        try {
            DB::beginTransaction();

            MatchBetTeam::where('id', $Id)->delete();
            TipsterAwayTeam::where('id', $IdTipsterAwayTeam)->delete();
            TipsterHomeTeam::where('id', $IdTipsterHomeTeam)->delete();

            DB::commit();

            return redirect('admin/tipster/match-bet')->with(['success' => 'Match Bet has been deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
