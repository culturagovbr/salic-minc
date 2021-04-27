<?php

namespace Application\Modules\DadosBancarios\Service\Devolucoes;

class Devolucoes
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

    public function buscarDevolucoesIncentivador()
    {
        $idPronac = $this->request->idPronac;
        $dtDevolucaoInicio = $this->request->dtDevolucaoInicio;
        $dtDevolucaoFim = $this->request->dtDevolucaoFim;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $devolucoesIncentivador = new \tbAporteCaptacao();

            $where = array();
            $where['idPronac = ?'] = $idPronac;

            if (!empty($dtDevolucaoInicio) && !empty($dtDevolucaoFim)) {
                $di = ConverteData($dtDevolucaoInicio, 13)." 00:00:00";
                $df = ConverteData($dtDevolucaoFim, 13)." 00:00:00";
                $where["dtLote BETWEEN '$di' AND '$df'"] = '';
            }

            $result = $devolucoesIncentivador->devolucoesDoIncentivador($where, ['dtLote DESC'])->toArray();

            return $result;
        }
    }
}

