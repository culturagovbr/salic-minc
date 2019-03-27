<?php

namespace Application\Modules\PrestacaoContas\Service\RelatorioFisico;


class RelatorioFisico implements \MinC\Servico\IServicoRestZend
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

    public function listarRelatorioFisico()
    {
        $idPronac = $this->request->idPronac;
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $tbComprovante = new \tbComprovantePagamentoxPlanilhaAprovacao();
            $relatorioFisico = $tbComprovante->buscarRelatorioFisico($idPronac)->toArray();

            return $relatorioFisico;
        }
    }

}
