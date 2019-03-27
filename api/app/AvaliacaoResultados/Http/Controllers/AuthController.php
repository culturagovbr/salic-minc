<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        /* $this->middleware('auth:api', ['except' => ['login']]); */
    }

    public function user(Request $request)
    {
        dd($request->auth);
    }
}
