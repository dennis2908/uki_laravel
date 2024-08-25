<?php

namespace App\Http\Controllers\Admin\Tipster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TipsterUser;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Redirect;

class TipsterUserController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('tipster_user')
                ->select([
                    'tipster_user.*',
                    'user.name'
                ])
                ->join('user', 'user.id', '=', 'tipster_user.user_id');

            if ($ReqDatatable['orderable']) {
                foreach ($ReqDatatable['orderable'] as $Order) {
                    if ($Order['column'] == 'rownum') {
                        $Datas = $Datas->orderBy('id', $Order['dir']);
                    } else {
                        if (!empty($Val['column'])) {
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
                    $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/tipster/user-balances/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                    $Html .= '</ul></div></div>';

                    return $Html;
                })
                ->rawColumns(['action'])
                ->toJson(true);
        }

        return view('pages.tipster.user-balances.index');
    }

    public function create()
    {
        return view('pages.tipster.user-balances.create');
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();
        unset($Data['_token']);

        $Rules = [
            'balance' => ['required', 'numeric', 'between:0,999999999999.999'],
            'open_bet' => ['required', 'numeric', 'between:0,999999999999.999'],
        ];

        $Messages = [];

        $attributes = [
            'user_id' => 'User Id',
            'balance' => 'Balance',
            'open_bet' => 'Open Bet',
        ];

        $Validator = Validator::make($Data, $Rules, $Messages, $attributes);

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

            $TipsterUser = new TipsterUser();

            $TipsterUser->fill([
                'user_id' => Auth::user()->id,
                'balance' => $Data['balance'],
                'open_bet' => $Data['open_bet']
            ])->save();

            DB::commit();

            $Message = 'User balance created successfully';
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
                'redirect' => url('admin/tipster/user-balances')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function edit($Id)
    {
        $Data = TipsterUser::find($Id);

        if (empty($Data))
            return redirect('admin/tipster/user-balances')->with(['error' => 'Data not exists']);


        return view('pages.tipster.user-balances.edit', compact('Data'));
    }

    public function update($Id, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'balance' => ['required', 'numeric', 'between:0,999999999999.999'],
        ];

        $Messages = [];

        $attributes = [
            'balance' => 'Balance',
        ];

        $Validator = Validator::make($Data, $Rules, $Messages, $attributes);

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

            $TipsterUser = TipsterUser::find($Id);

            $oldBalance = $TipsterUser->balance;

            $DataUserBalance = [
                'balance' => $Data['balance']
            ];

            $TipsterUser->fill($DataUserBalance)->save();

            $BalanceDiff = $Data['balance'] - $oldBalance;
            if ($BalanceDiff < 0) {
                $lLogType = 'credit';
            } else {
                $lLogType = 'debit';
            }

            $InsertLog = DB::table('tipster_balance_log');
            $DataLog = [
                'user_id' => $TipsterUser->user_id,
                'type' => $lLogType,
                'balance' => $BalanceDiff,
                'action_type' => 'adjustment',
                'tipster_transaction_id' => 0,
                'actor_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'tipster_transaction_cancel_id' => 0
            ];
            $InsertLog->insert($DataLog);

            DB::commit();

            $Message = 'User balance updated successfully';
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
                'redirect' => url('admin/tipster/user-balances')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id)
    {
        try {

            $Data = TipsterUser::where("user_id", Auth::user()->id)->find($Id);

            if ($Data == null) {
                return Redirect::route('user.balance.index');
            }
            DB::beginTransaction();

            TipsterUser::where('id', $Id)->delete();

            DB::commit();

            return redirect('admin/tipster/user-balances')->with(['success' => 'User balance has been deleted successfully']);
        } catch (Exception $E) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
