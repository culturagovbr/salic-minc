<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class AvaliacaoComprovanteController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {


        return response()->json(['teste Avaliacao Comprovante'], 200);
    }



    public function post(Request $request){

        return response ()->json(['teste Post Avaliaçao Comprovante'] ,200);


    }

}

