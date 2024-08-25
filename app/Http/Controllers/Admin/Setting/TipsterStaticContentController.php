<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\TipsterStaticContent;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TipsterStaticContentController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            $ReqDatatable  = $this->requestDatatables($Request->input());

            $Datas = DB::table('tipster_static_contents')->select([
                'tipster_static_contents.*',
                DB::raw('tipster_created_by.username as tipster_created_username'),
                DB::raw('IF(tipster_updated_by.username is null, "-", tipster_updated_by.username) as tipster_updated_username'),
                DB::raw('IF(is_active=1, "Active", "Not_Active") as is_active')
            ])->join(DB::RAW("tipster_user as tipster_created_by"), "tipster_created_by.id", "=", 'tipster_static_contents.created_by')
                ->leftJoin(DB::RAW("tipster_user as tipster_updated_by"), "tipster_updated_by.id", "=", 'tipster_static_contents.updated_by');

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
                ->addColumn('is_active', function ($Data) {
                    return $Data->is_active;
                })
                ->filterColumn('is_active', function ($query, $keyword) {
                    $sql = "LOWER(is_active LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('is_active', function ($query, $order) {
                    $query->orderBy('is_active', $order);
                })
                ->addColumn('is_active', function ($Data) {
                    return $Data->is_active;
                })
                ->filterColumn('is_active', function ($query, $keyword) {

                    if ($keyword == "Active") {
                        $keyword = 1;
                    } else if ($keyword == "Not_Active") {
                        $keyword = 2;
                    }
                    $sql = "LOWER(is_active LIKE '%" . $keyword . "%')";
                    $query->whereRaw($sql);
                })
                ->orderColumn('is_active', function ($query, $order) {
                    $query->orderBy('is_active', $order);
                })
                ->addColumn('tipster_created_username', function ($Data) {
                    return $Data->tipster_created_username;
                })
                ->filterColumn('tipster_created_username', function ($query, $keyword) {
                    $sql = "LOWER(tipster_created_by.username LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('tipster_created_username', function ($query, $order) {
                    $query->orderBy('tipster_created_by.username', $order);
                })
                ->addColumn('tipster_updated_username', function ($Data) {
                    return $Data->tipster_updated_username;
                })
                ->filterColumn('tipster_updated_username', function ($query, $keyword) {
                    $sql = "LOWER(tipster_updated_by.username LIKE ?)";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->orderColumn('tipster_updated_username', function ($query, $order) {
                    $query->orderBy('tipster_updated_by.username', $order);
                })
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
                    $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/setting/static-content/edit/' . $Data->id) . '"><i class="flaticon2-edit nav-icon"></i><span class="nav-text">Edit</span></a></li>';

                    //* ACTIVE OR DEACTIVE

                    if ($Data->is_active == "Active")
                        $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/setting/static-content/deactivate/' . $Data->id) . '"><i class="la la-exchange nav-icon"></i><span class="nav-text">Deactivate</span></a></li>';
                    else
                        $Html .= '<li class="nav-item"><a class="nav-link" href="' . url('admin/setting/static-content/activate/' . $Data->id) . '"><i class="la la-exchange nav-icon"></i><span class="nav-text">Activate</span></a></li>';

                    //* DELETE
                    $Html .= '<li class="nav-item"><a class="nav-link btn-delete" href="' . url('admin/setting/static-content/delete/' . $Data->id) . '"><i class="flaticon2-delete nav-icon"></i><span class="nav-text">Delete</span></a></li>';
                    $Html .= '</ul></div></div>';

                    return $Html;
                })
                ->rawColumns(['action', 'content'])
                ->toJson(true);
        }

        return view('pages.setting.static-content.index');
    }

    public function create()
    {
        return view('pages.setting.static-content.create');
    }

    public function store(Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'name' =>  ['required',  'unique:tipster_static_contents,name'],
            'content' => 'required',
        ];

        $Messages = [];

        $Attributes = [
            'name' => 'Name',
            'content' => 'Content'
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

            $TipsterStaticContent = new TipsterStaticContent();

            $TipsterStaticContent->fill([
                'name' => $Data['name'],
                'content' => $Data['content'],
                'slug' => Str::slug($Data['name']),
                'created_by' => Auth::user()->id
            ])->save();

            DB::commit();

            $Message = 'Static content created successfully';
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
                'redirect' => url('admin/setting/static-content')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function edit($Id)
    {

        $Data = TipsterStaticContent::find($Id);

        if (empty($Data))
            return redirect('admin/setting/static-content')->with(['error' => 'Data not exists']);

        return view('pages.setting.static-content.edit', compact('Data'));
    }

    public function update($Id, Request $Request)
    {
        $Data = $Request->all();

        unset($Data['_token']);

        $Rules = [
            'content' => 'required',
        ];

        $Messages = [];

        $Attributes = [
            'content' => 'Content'
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

            $TipsterStaticContent = TipsterStaticContent::find($Id);

            $DataTipsterStaticContent = [
                'content' => $Data['content'],
                'updated_by' => Auth::user()->id
            ];

            $TipsterStaticContent->fill($DataTipsterStaticContent)->save();

            DB::commit();

            $Message = 'Static content updated successfully';
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
                'redirect' => url('admin/setting/static-content')
            ], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
    }

    public function delete($Id)
    {
        try {
            DB::beginTransaction();

            $delete = TipsterStaticContent::where('id', $Id)->delete();

            DB::commit();

            return redirect('admin/setting/static-content')->with(['success' => 'Static content has been deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }

    public function activate($Id)
    {
        try {
            DB::beginTransaction();

            $TipsterStaticContent = TipsterStaticContent::find($Id);

            $DataTipsterStaticContent = [
                'is_active' => 1,
                'updated_by' => Auth::user()->id
            ];

            $TipsterStaticContent->fill($DataTipsterStaticContent)->save();

            DB::commit();

            return redirect('admin/setting/static-content')->with(['success' => 'Static Content has been activated']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }

    public function deactivate($Id)
    {
        try {
            DB::beginTransaction();

            $TipsterStaticContent = TipsterStaticContent::find($Id);

            $DataTipsterStaticContent = [
                'is_active' => 2,
                'updated_by' => Auth::user()->id
            ];

            $TipsterStaticContent->fill($DataTipsterStaticContent)->save();

            DB::commit();

            return redirect('admin/setting/static-content')->with(['success' => 'Static Content has been deactivated']);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['error' => 'Something went wrong, please try again']);
        }
    }
}
