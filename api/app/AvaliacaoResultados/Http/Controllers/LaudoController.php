<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;

class LaudoController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request) {

        return response()->json(['teste Laudo Get'], 200);
    }

    public function post(Request $request){

        return response ()->json(['teste Laudo Post'] ,200);

    }
    public function index(Request $request) {

        return response()->json(['teste Laudo INDEX'], 200);
    }


}
