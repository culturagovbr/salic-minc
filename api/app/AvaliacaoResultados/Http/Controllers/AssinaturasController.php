<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class AssinaturasController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {


        return response()->json(['teste'], 200);
    }

    public function post(Request $request){

        return response ()->json(['testePost'] ,200);


    }
}
