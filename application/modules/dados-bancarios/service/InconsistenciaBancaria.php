<?php

namespace Application\Modules\DadosBancarios\Service\InconsistenciaBancaria;

class InconsistenciaBancaria
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

    public function buscarInconsistenciaBancaria()
    {
        $idPronac = $this->request->idPronac;
        $dtPagamentoInicio = $this->request->dtPagamentoInicio;
        $dtPagamentoFim = $this->request->dtPagamentoFim;
        $where = array();
        $where['idPronac = ?'] = $idPronac;

        if (!empty($dtPagamentoInicio) && !empty($dtPagamentoFim)) {
            $di = ConverteData($dtPagamentoInicio, 13)." 00:00:00";
            $df = ConverteData($dtPagamentoFim, 13)." 00:00:00";
            $where["dtPagamento BETWEEN '$di' AND '$df'"] = '';
        }

        $dadosProjeto = new \Projetos();
        $buscaDadosProjeto = $dadosProjeto->inconsistenciasComprovacao($where, ['dtPagamento DESC'], null, null)->toArray();
        
        return $buscaDadosProjeto;
    }
}

