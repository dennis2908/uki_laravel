<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForgetPasswordController extends Controller
{
    public function index()
    {
        return view('forget_password/forget_password');
    }
}
