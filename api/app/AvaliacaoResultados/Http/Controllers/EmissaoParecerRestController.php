<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class EmissaoParecerRestController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Emissao Parecer GET'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Emissao Parecer Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Emissao Parecer INDEX'], 200);
    }


}
