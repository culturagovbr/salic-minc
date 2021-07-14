<?php

namespace Application\Modules\DadosBancarios\Service\ConciliacaoBancaria;

class ConciliacaoBancaria
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

    public function buscarConciliacaoBancaria()
    {
        $idPronac = $this->request->idPronac;
        $dtPagamentoInicio = $this->request->dtPagamentoInicio;
        $dtPagamentoFim = $this->request->dtPagamentoFim;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $where = array();
            $where['idPronac = ?'] = $idPronac;
            $DadosConciliacao = new \Projetos();

            if (!empty($dtPagamentoInicio) && !empty($dtPagamentoFim)) {
                $di = ConverteData($dtPagamentoInicio, 13)." 00:00:00";
                $df = ConverteData($dtPagamentoFim, 13)." 00:00:00";
                $where["dtPagamento BETWEEN '$di' AND '$df'"] = '';
            }

            $buscaDadosConciliacao = $DadosConciliacao->painelDadosConciliacaoBancaria($where, ['dtPagamento DESC'], null, null)->toArray();

            return $buscaDadosConciliacao;
        }
    }
}

