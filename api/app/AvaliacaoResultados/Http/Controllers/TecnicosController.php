<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class TecnicosController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Tecnicos GET'], 200);
    }

    public function index(Request $request) {

        return response()->json(['teste Tecnicos INDEX'], 200);
    }


}
