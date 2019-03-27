<?php

namespace App\AvaliacaoResultados\Http\Controllers;

use App\AvaliacaoResultados\Service\ParecerTecnico\Encaminhamento as EncaminhamentoService;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request, $idpronac) {
        $encaminhamentoService = new EncaminhamentoService();
        $resposta = $encaminhamentoService->buscarHistorico($idpronac);

        return response()->json($resposta, 200);
    }
}
