<?php

namespace App\Http\Controllers\Admin\Tipster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\TipsterSeason;
use Yajra\DataTables\DataTables;
use Exception;

class TipsterSeasonController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
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
                    $Html = '<div class="dropdown dropdown-inline mr-1"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                    //* EDIT
                    $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/tipster/season/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                    //* DELETE
                    $Html .= '<li class="nav-item"><a class="nav-link btn-delete" href="' . url('admin/tipster/season/delete/' . $Data->id) . '"><i class="flaticon2-delete nav-icon"></i><span class="nav-text">Delete</span></a></li>';
                    $Html .= '</ul></div></div>';

                    return $Html;
                })
                ->rawColumns(['action'])
                ->toJson(true);
        }

        return view('pages.tipster.season.index');
    }

    public function create()
    {
        return view('pages.tipster.season.create');
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'name' =>  ['required',  'unique:tipster_season,name'],
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'initialize_balance' => ['required', 'numeric', 'between:0,999999999999.999'],
        ];

        $Messages = [];

        $Attributes = [
            'name' => 'Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'initialize_balance' => 'Initialize Balance',
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

            $Season = new TipsterSeason();

            $Season->fill([
                'name' => $Data['name'],
                'start_date' => $Data['start_date'],
                'end_date' => $Data['end_date'],
                'initialize_balance' => $Data['initialize_balance']
            ])->save();

            DB::commit();

            $Message = 'Season created successfully';
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
                'redirect' => url('admin/tipster/season')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function edit($Id)
    {
        $Data = TipsterSeason::find($Id);

        if (empty($Data))
            return redirect('admin/tipster/season')->with(['error' => 'Data not exists']);

        $Data->start_date = date("Y-m-d", strtotime($Data->start_date));
        $Data->end_date = date("Y-m-d", strtotime($Data->end_date));

        return view('pages.tipster.season.edit', compact('Data'));
    }

    public function update($Id, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'initialize_balance' => ['required', 'numeric', 'between:0,999999999999.999'],
        ];

        $Messages = [];

        $Attributes = [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'initialize_balance' => 'Initialize Balance',
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

            $Season = TipsterSeason::find($Id);

            $DataSeason = [
                'start_date' => $Data['start_date'],
                'end_date' => $Data['end_date'],
                'initialize_balance' => $Data['initialize_balance']
            ];

            $Season->fill($DataSeason)->save();

            DB::commit();

            $Message = 'Season updated successfully';
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
                'redirect' => url('admin/tipster/season')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id)
    {
        try {
            DB::beginTransaction();

            $delete = TipsterSeason::where('id', $Id)->delete();

            DB::commit();

            return redirect('admin/tipster/season')->with(['success' => 'Config has been deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
