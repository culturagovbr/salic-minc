<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use Illuminate\Http\Request;
use App\AvaliacaoResultados\Models\Projetos as ProjetosModel;

class ProjetosSimilaresController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request, $idpronac) {

        $projetos = new ProjetosModel();
        $projetoBase = $projetos
            ->where('idpronac', $idpronac)
            ->first();

        $projetos = $projetos
            ->where('Area', $projetoBase->Area)
            ->where('Segmento', $projetoBase->Segmento)
            ->where('AnoProjeto', $projetoBase->AnoProjeto)
            ->limit(5)
            ->get();

        $return['projetoBase'] = $projetoBase;
        $return['projetos'] = $projetos;
        return response()->json($return, 200);
    }
}
