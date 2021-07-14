<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class FluxosController extends Controller
{
    public function __construct()
    {
    }


    public function index(Request $request) {

        return response()->json(['teste Fluxos  INDEX'], 200);
    }


}
