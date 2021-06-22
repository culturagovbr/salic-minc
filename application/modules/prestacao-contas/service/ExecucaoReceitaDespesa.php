<?php

namespace Application\Modules\PrestacaoContas\Service\ExecucaoReceitaDespesa;


class ExecucaoReceitaDespesa implements \MinC\Servico\IServicoRestZend
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

    public function listarExecucaoReceitaDespesa()
    {
        $idPronac = $this->request->idPronac;
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $tbComprovante = new \tbComprovantePagamentoxPlanilhaAprovacao();
            $relatorioExecucaoReceita = $tbComprovante->buscarRelatorioExecucaoReceita($idPronac)->toArray();
            $relatorioExecucaoDespesa = $tbComprovante->buscarRelatorioExecucaoDespesa($idPronac)->toArray();

            $relatorio['relatorioExecucaoReceita'] = $relatorioExecucaoReceita;
            $relatorio['relatorioExecucaoDespesa'] = $relatorioExecucaoDespesa;

            return $relatorio;
        }
    }
}
