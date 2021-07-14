<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class TipoAvaliacaoRestController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Tipo Avaliacao GET'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Tipo Avaliacao Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Tipo Avaliacao INDEX'], 200);
    }


}
