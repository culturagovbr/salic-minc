<?php

namespace Application\Modules\AvaliacaoResultados\Service;

class DetalhamentoItens
{
    private $request;

    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function obterDetalhamento()
    {
        $idPronac = (int) $this->request->idPronac;
        $planilhaAprovacaoModel = new \PlanilhaAprovacao();

        $resposta = $planilhaAprovacaoModel->parametrosBuscaComprovantes($idPronac);

        return $resposta->toArray();
    }
}
