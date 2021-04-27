<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Estado Get'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Estado Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Estado INDEX'], 200);
    }


}
