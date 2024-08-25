<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\UserTipster;

class UserAPIController extends Controller
{
    public function getUser(Request $Request)
    {
        $Datas = UserTipster::where('id', $Request->user_id)->first();

        if ($Request->user_id === null)
            return $this->returnJson(["error" => "user id field is required"], 422, false);

        if (empty($Datas)) {
            return $this->returnJson($Datas, 404, false);
        }

        return $this->returnJson($Datas);
    }
}
