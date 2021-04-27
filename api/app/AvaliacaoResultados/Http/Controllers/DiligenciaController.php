<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class DiligenciaController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Diligencia GET'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Diligencia POST'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Diligencia INDEX'], 200);
    }


}
