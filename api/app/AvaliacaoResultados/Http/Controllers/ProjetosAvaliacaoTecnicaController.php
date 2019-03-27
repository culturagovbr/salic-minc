<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class ProjetosAvaliacaoTecnicaController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request) {

        return response()->json(['teste Projetos Avaliacao Tecnica INDEX'], 200);
    }


}
