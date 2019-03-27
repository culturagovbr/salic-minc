<?php

namespace Application\Modules\DadosBancarios\Service\ExtratosBancarios;

class ExtratosBancarios
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

    public function buscarExtratosBancarios()
    {
        $idPronac = $this->request->idPronac;
        $dtLancamento = $this->request->dtLancamento;
        $dtLancamentoFim = $this->request->dtLancamentoFim;
        $tpConta = $this->request->tpConta;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $DadosExtrato = new \Projetos();

            $where = array();
            $where['idPronac = ?'] = $idPronac;

            if (!empty($dtLancamento) && !empty($dtLancamentoFim)) {
                $di = ConverteData($dtLancamento, 13)." 00:00:00";
                $df = ConverteData($dtLancamentoFim, 13)." 00:00:00";
                $where["dtLancamento BETWEEN '$di' AND '$df'"] = '';
            }

            if (!empty($tpConta)) {
                $where["stContaLancamento= ?"] = ($tpConta == 'captacao') ? 0 : 1;
            }
            $result = $DadosExtrato->painelDadosBancariosExtrato($where, ['dtLancamento DESC'], null, null, false)->toArray();
            return $result;
        }
    }
}

