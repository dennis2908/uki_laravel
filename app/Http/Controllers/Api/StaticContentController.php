<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class StaticContentController extends Controller
{
    public function index()
    {
        $datas = DB::table('tipster_static_contents')
            ->select(['name', 'slug'])
            ->get();

        if(empty($datas)){
            return $this->returnJson(['error' => ['Data not found']], 404, false);
        }

        return $this->returnJson($datas, 200, true);
    }

    public function view($slug)
    {
        $data = DB::table('tipster_static_contents')
            ->select(['name', 'slug', 'content'])
            ->where('slug', $slug)
            ->first();

        if(empty($data)){
            return $this->returnJson(['error' => ['Data not found']], 404, false);
        }

        return $this->returnJson($data, 200, true);
    }
}
