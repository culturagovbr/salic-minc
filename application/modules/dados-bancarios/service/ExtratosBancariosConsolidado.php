<?php

namespace Application\Modules\DadosBancarios\Service\ExtratosBancariosConsolidado;

class ExtratosBancariosConsolidado
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarExtratosBancariosConsolidado()
    {
        $idPronac = $this->request->idPronac;

            $where = array();
            $where['idPronac = ?'] = $idPronac;

            $dadosExtrato = new \Projetos();
            $busca = $dadosExtrato->extratoContaMovimentoConsolidado($where, null, null, null)->toArray();

            return $busca;
    }
}

