<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class FluxoProjetoController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Fluxo Projeto Get'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Fluxo Projeto Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Fluxo Projeto INDEX'], 200);
    }


}
