<?php

namespace Application\Modules\DadosBancarios\Service\Captacao;

class Captacao
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

    public function buscarCaptacao()
    {
        $idPronac = $this->request->idPronac;
        $dtReciboInicio = $this->request->dtReciboInicio;
        $dtReciboFim = $this->request->dtReciboFim;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $Captacao = new \Captacao();

            $where = array();
            $where['p.idPronac = ?'] = $idPronac;

            if (!empty($dtReciboInicio) && !empty($dtReciboFim)) {
                $di = ConverteData($dtReciboInicio, 13)." 00:00:00";
                $df = ConverteData($dtReciboFim, 13)." 00:00:00";
                $where["DtRecibo BETWEEN '$di' AND '$df'"] = '';
            }

            $dadosCaptacao = $Captacao->painelDadosBancariosCaptacao($where, ['DtRecibo DESC'], null, null, false)->toArray();

            $valorTotal = $this->calculaValorTotalCaptado($dadosCaptacao);

            $result['vlTotal'] = $valorTotal;
            $result['captacao'] = $dadosCaptacao;

            return $result;
        }
    }

    private function calculaValorTotalCaptado($value) {
        foreach ($value as &$item) {
            $valorTotal += $item['CaptacaoReal'];
        }

        return $valorTotal;
    }
}

