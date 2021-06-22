<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class ProjetoAssinaturaController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Projeto Assinatura GET'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Projeto Assinatura Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Projeto Assinatura INDEX'], 200);
    }


}
