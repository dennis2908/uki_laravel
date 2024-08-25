<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Exception;

class UsersController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('tipster_admin');

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
                    if (Auth::user()->id == $Data->id) {
                        return '';
                    } else {
                        $Html = '<div class="dropdown dropdown-inline mr-1"><a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-expanded="false"><i class="flaticon2-menu-1 icon-2x"></i></a><div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"><ul class="nav nav-hoverable flex-column">';
                        //* EDIT
                        $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/setting/users/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                        //* DELETE
                        $Html .= '<li class="nav-item"><a class="nav-link btn-delete" href="' . url('admin/setting/users/delete/' . $Data->id) . '"><i class="flaticon2-delete nav-icon"></i><span class="nav-text">Delete</span></a></li>';
                        $Html .= '</ul></div></div>';

                        return $Html;
                    }
                })
                ->rawColumns(['action'])
                ->toJson(true);
        }

        return view('pages.setting.users.index');
    }

    public function create()
    {
        return view('pages.setting.users.create');
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'username' => ['required', 'unique:tipster_admin,username'],
            'password' => ['required', 'same:password_confirmation'],
        ];

        $Messages = [];

        $Attributes = [
            'name' => 'Name',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
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

            $User = new User();

            $User->fill([
                'username' => $Data['username'],
                'email' => $Data['email'],
                'name' => $Data['name'],
                'password' => bcrypt($Data['password'])
            ])->save();

            DB::commit();

            $Message = 'User created successfully';
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
                'redirect' => url('admin/setting/users')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function edit($Id)
    {
        $Data = User::find($Id);

        if (empty($Data))
            return redirect('admin/setting/users')->with(['error' => 'Data not exists']);

        return view('pages.setting.users.edit', compact('Data'));
    }

    public function update($Id, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'username' => ['required', 'unique:tipster_admin,username,' . $Id],
            'password' => ['same:password_confirmation'],
        ];

        $Messages = [];

        $Attributes = [
            'name' => 'Name',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
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

            $User = User::find($Id);

            $DataUser = [
                'username' => $Data['username'],
                'email' => $Data['email'],
                'name' => $Data['name'],
            ];

            if (!empty($Data['password'])) {
                $DataUser['password'] = bcrypt($Data['password']);
            }

            $User->fill($DataUser)->save();

            DB::commit();

            $Message = 'User updated successfully';
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
                'redirect' => url('admin/setting/users')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id)
    {
        try {
            DB::beginTransaction();

            $delete = User::where('id', $Id)->delete();

            DB::commit();

            return redirect('admin/setting/users')->with(['success' => 'User has been deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
