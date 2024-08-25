<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\TipsterTransactionCancel;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class TipsterTransactionCancelAPIController extends Controller
{
    public function index()
    {
        $Datas = TipsterTransactionCancel::latest()->get();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }
        return $this->returnJson($Datas);
    }

    public function getDataById(Request $Request)
    {
        if ($Request->id === null)
            return $this->returnJson(["error" => "id field is required"], 422, false);

        $Datas = TipsterTransactionCancel::where('id', $Request->id)->first();
        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }
        return $this->returnJson($Datas);
    }
    public function store(Request $Request)
    {
        $Validator = Validator::make($Request->all(), [
            'user_id' => ['required', 'numeric'],
            'tipster_transaction_id' => ['required', 'numeric'],
        ]);

        if ($Validator->passes()) {
            $TipsterTransactionCancel = new TipsterTransactionCancel();
            $TipsterTransactionCancel->user_id  = $Request->user_id;
            $TipsterTransactionCancel->tipster_transaction_id  = $Request->tipster_transaction_id;
            date_default_timezone_set('GMT');
            $TipsterTransactionCancel->cancel_time  =  date('Y-m-d H:i:s', strtotime(Carbon::now()->timezone("GMT")));
            $TipsterTransactionCancel->save();

            if (Auth::user() == null)
                $ActorId = 0;
            else
                $ActorId =  Auth::user()->id;

            $InsertLog = DB::table('tipster_balance_log');
            date_default_timezone_set('Asia/Jakarta');
            $DataLog = [
                'user_id' => $Request->user_id,
                'type' => 'credit',
                'action_type' => 'bet',
                'tipster_transaction_id' => $TipsterTransactionCancel->tipster_transaction_id,
                'actor_id' => $ActorId,
                'created_at' => date('Y-m-d H:i:s', strtotime(Carbon::now())),
                'tipster_transaction_cancel_id' => $TipsterTransactionCancel->id
            ];
            $InsertLog->insert($DataLog);
            return $this->returnJson(['success' => 'Added new record.']);
        }
        return $this->returnJson(['error' => $Validator->errors()->all()], 422, false);
    }
}
