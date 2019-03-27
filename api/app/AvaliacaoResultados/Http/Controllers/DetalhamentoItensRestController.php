<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class DetalhamentoItensRestController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {


        return response()->json(['teste detalhamento GET'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste detalhamento Post'] ,200);

    }
    public function index(Request $request) {


        return response()->json(['teste detalhamento INDEX'], 200);
    }
}
