<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request) {

        return response()->json(['teste IndexController INDEX'], 200);
    }


}
