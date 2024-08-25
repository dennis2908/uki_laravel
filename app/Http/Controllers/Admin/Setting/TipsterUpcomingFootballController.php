<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class TipsterUpcomingFootballController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('football_match')->select([
                'football_match.*',
                DB::RAW('tipster_season.name as season_name'),
                DB::RAW('FROM_UNIXTIME(match_time, "%d-%M-%Y %H:%i:%s") as unixdatetime')
            ]);

            $Join = ' tipster_season.start_date <= FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s")
            and tipster_season.end_date >= FROM_UNIXTIME(match_time, "%Y-%m-%d %H:%i:%s")
            and FROM_UNIXTIME(match_time, "%Y-%m-%d- %H:%i:%s") >= DATE_ADD(NOW(), INTERVAL 1 HOUR)
            ';

            if ($Request['idSession'] !== null) {
                $Join .= ' and tipster_season.id = ' . (int)$Request['idSession'];
            }

            $Datas->join('tipster_season', 'tipster_season.id', "=", DB::RAW($Join));


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
                ->addColumn('season_name', function ($Data) {
                    return $Data->season_name;
                })
                ->filterColumn('season_name', function ($query, $keyword) {
                    $sql = "LOWER(tipster_season.name LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('season_name', function ($query, $order) {
                    $query->orderBy('tipster_season.name', $order);
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
                ->addColumn('rownum', function () use (&$RowNum, $IsIncrease) {
                    if ($IsIncrease == true) {
                        return $RowNum++;
                    } else {
                        return $RowNum--;
                    }
                })
                ->toJson(true);
        }

        return view('pages.setting.upcoming-football-match.index');
    }

    public function dataSeason(Request $Request)
    {
        $ReqDatatable  = $this->requestDatatables($Request->input());

        $Datas = DB::table('tipster_season')->where('start_date', '<=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addHours(1))))->where('end_date', '>=', date('Y-m-d H:i:s', strtotime(Carbon::now()->addHours(1))));

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
                $Html .= '<li class="nav-item"><a class="nav-link" onclick="openTableUpcomingMatchBet(' . $Data->id . ')"><i class="flaticon2-open-box nav-icon"></i><span class="nav-text">Football Match</span></a></li>';


                //* DELETE
                $Html .= '</ul></div></div>';

                return $Html;
            })
            ->rawColumns(['action'])
            ->toJson(true);
    }
}
