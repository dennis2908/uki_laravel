<?php

namespace App\Http\Controllers\Admin\Tipster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\TipsterTransaction;
use App\Models\TipsterUser;
use App\Models\MatchBetTeam;
use App\Models\TipsterTransactionCancel;
use Yajra\DataTables\DataTables;
use Exception;
use Carbon\Carbon;

class TransactionCancelController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('tipster_transaction_cancel')
                ->select([
                    'tipster_transaction.*',
                    'tipster_user.username',
                    'tipster_transaction_cancel.id',
                    'tipster_transaction.win_prize',
                    DB::RAW("case tipster_transaction.status
                            when '1' then 'Waiting for match'
                            when '2' then 'Match in progress'
                            when '3' then 'Win the prize'
                            when '4' then 'Lose the prize'
                            when '5' then 'Canceled bet'
                            ELSE '-'
                        end as status"),
                    DB::RAW('DATE_FORMAT(place_bet_time, "%d %M %Y %h:%i:%s ") as place_bet_time'),
                    DB::RAW("CONCAT(FORMAT(tipster_match_bet.odds_over,3,'en_US'),' - ',FORMAT(odds_under,3,'en_US'),
                    ' - ',FORMAT(bet_price,3,'en_US'),' - ',FORMAT(big_bet_price,3,'en_US')) as tipster_match_bet"),
                ])
                ->join('tipster_transaction', 'tipster_transaction_cancel.tipster_transaction_id', '=', 'tipster_transaction.id')
                ->join('tipster_user', 'tipster_transaction.user_id', '=', 'tipster_user.id')
                ->join('tipster_match_bet', 'tipster_match_bet.id', '=', 'tipster_transaction.tipster_match_bet_id')
                ->join("football_match", "football_match.id", "=", DB::raw('tipster_match_bet.football_match_id COLLATE utf8mb4_0900_ai_ci'))
                ->join("tipster_away_team", "tipster_away_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
                ->join("tipster_home_team", "tipster_home_team.tsr_match_bet_id", "=", "tipster_match_bet.id")
                ->where('tipster_match_bet.id', (int)$Request['idMatchBet'])->where('tipster_transaction.status', 5);


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
                ->addColumn('tipster_match_bet', function ($Data) {
                    return $Data->tipster_match_bet;
                })
                ->filterColumn('tipster_match_bet', function ($query, $keyword) {
                    $sql = "LOWER(odds_over LIKE '%" . $keyword . "%')
                    OR LOWER(odds_under LIKE '%" . $keyword . "%') OR LOWER(bet_price LIKE '%" . $keyword . "%')  OR LOWER(big_bet_price LIKE '%" . $keyword . "%')";
                    $query->whereRaw($sql);
                })
                ->orderColumn('tipster_match_bet', function ($query, $order) {
                    $query->orderBy("tipster_match_bet.odds_over", $order);
                })
                ->addColumn('username', function ($Data) {
                    return $Data->username;
                })
                ->filterColumn('username', function ($query, $keyword) {
                    $sql = "LOWER(tipster_user.username LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('username', function ($query, $order) {
                    $query->orderBy('tipster_user.username', $order);
                })
                ->addColumn('place_bet_time', function ($Data) {
                    return $Data->place_bet_time;
                })
                ->filterColumn('place_bet_time', function ($query, $keyword) {
                    $sql = "tipster_transaction.place_bet_time LIKE ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('place_bet_time', function ($query, $order) {
                    $query->orderBy('place_bet_time', $order);
                })
                ->addColumn('status', function ($Data) {
                    return $Data->status;
                })
                ->filterColumn('tipster_transaction.status', function ($query, $keyword) {
                    $sql = "case
                            when status = '1' then 'Waiting for match'
                            when status = '2'then 'Match in progress'
                            when status = '3'then 'Win the prize'
                            when status = '4'then 'Lose the prize'
                            when status = '5'then 'Canceled bet'
                            ELSE '-'
                        end LIKE ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status', $order);
                })
                ->addColumn('win_prize', function ($Data) {
                    return $Data->win_prize;
                })
                ->filterColumn('win_prize', function ($query, $keyword) {
                    $sql = "tipster_transaction.win_prize LIKE ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('win_prize', function ($query, $order) {
                    $query->orderBy('win_prize', $order);
                })
                ->editColumn('created_at', function ($Data) {
                    return date('d F Y H:i:s', strtotime($Data->created_at));
                })
                ->editColumn('updated_at', function ($Data) {
                    return date('d F Y H:i:s', strtotime($Data->updated_at));
                })
                ->addColumn('action', function ($Data) {
                    $Html = '<div class="dropdown dropdown-inline mr-1"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                    //* EDIT
                    $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/tipster/transaction-cancel/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                    //* DELETE
                    $Html .= '<li class="nav-item"><a class="nav-link btn-delete" href="' . url('admin/tipster/transaction-cancel/delete/' . $Data->id) . '"><i class="flaticon2-delete nav-icon"></i><span class="nav-text">Delete</span></a></li>';
                    $Html .= '</ul></div></div>';

                    return $Html;
                })
                ->rawColumns(['action'])
                ->toJson(true);
        }

        return view('pages.tipster.transaction-cancel.index');
    }

    public function DataMatchBet(Request $Request)
    {
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
            ]);

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
                $Html = '<div class="dropdown dropdown-inline mw-100"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                //* EDIT
                $Html .= '<li class="nav-item"><a class="nav-link" onclick="openTableTransactionCancel(' . $Data->id . ')"><i class="flaticon2-open-box nav-icon"></i><span class="nav-text">Transaction Cancel</span></a></li>';


                //* DELETE
                $Html .= '</ul></div></div>';

                return $Html;
            })
            ->rawColumns(['action'])
            ->toJson(true);
    }

    public function create()
    {

        $DataUser =  new TipsterTransaction();
        $DataUser = $DataUser->transactionCancelCreate();

        return view('pages.tipster.transaction-cancel.create', compact('DataUser'));
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'user_id' => "required",
            'tipster_transaction_id' => 'required',
        ];

        $Messages = [];

        $Attributes = [
            'user_id' => 'User Id',
            'tipster_transaction_id' => 'Transaction',
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

            $TipsterTransactionCancel = new TipsterTransactionCancel();

            $TipsterTransactionCancel->fill([
                'user_id' => $Data['user_id'],
                'tipster_transaction_id' => $Data['tipster_transaction_id'],
                'cancel_time' => date('Y-m-d H:i:s', strtotime(Carbon::now()->timezone("GMT"))),
            ])->save();


            $Transaction = TipsterTransaction::find($Data['tipster_transaction_id']);

            $DataTransaction = [
                'status' => 5
            ];

            $Transaction->fill($DataTransaction)->save();

            DB::commit();

            $Message = 'Transaction cancel created successfully';
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
                'redirect' => url('admin/tipster/transaction-cancel')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function edit($Id)
    {

        $Data = TipsterTransactionCancel::find($Id);

        if (empty($Data))
            return redirect('admin/tipster/transaction-cancel')->with(['error' => 'Data not exists']);

        $DataUser =  TipsterUser::orderBy('username')->get();

        $DataTransaction = new TipsterTransaction();
        $DataTransaction = $DataTransaction->transactionCancelEdit($Data);

        return view('pages.tipster.transaction-cancel.edit', compact('Data', 'DataUser', 'DataTransaction'));
    }

    public function update($Id, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'user_id' => "required",
            'tipster_transaction_id' => 'required',
        ];

        $Messages = [];

        $Attributes = [
            'user_id' => 'User',
            'tipster_transaction_id' => 'Transaction',
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

            $TipsterTransactionCancel = TipsterTransactionCancel::find($Id);

            $DataTipsterTransactionCancel = [
                'user_id' => $Data['user_id'],
                'tipster_transaction_id' => $Data['tipster_transaction_id'],
                'cancel_time' => date('Y-m-d H:i:s', strtotime(Carbon::now()->timezone("GMT"))),
            ];

            $TipsterTransactionCancel->fill($DataTipsterTransactionCancel)->save();

            $Transaction = TipsterTransaction::find($Data['tipster_transaction_id']);

            $DataTransaction = [
                'status' => 5
            ];

            $Transaction->fill($DataTransaction)->save();

            DB::commit();

            $Message = 'Transaction cancel updated successfully';
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
                'redirect' => url('admin/tipster/transaction-cancel')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id)
    {
        try {
            DB::beginTransaction();

            $TipsterTransactionCancel = TipsterTransactionCancel::find($Id);

            $TipsterTransaction = TipsterTransaction::find($TipsterTransactionCancel->tipster_transaction_id);

            dd($TipsterTransactionCancel->tipster_transaction_id);

            $DataTipsterTransaction = [
                'status' => 1
            ];

            $TipsterTransaction->fill($DataTipsterTransaction)->save();

            $delete = TipsterTransactionCancel::where('id', $Id)->delete();

            DB::commit();

            return redirect('admin/tipster/transaction-cancel')->with(['success' => 'Transaction Cancel has been deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
