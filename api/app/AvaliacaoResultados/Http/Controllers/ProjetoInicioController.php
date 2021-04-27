<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class ProjetoInicioController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Projeto Inicio GET'], 200);
    }

    public function index(Request $request) {

        return response()->json(['teste Projeto Inicio INDEX'], 200);
    }


}
