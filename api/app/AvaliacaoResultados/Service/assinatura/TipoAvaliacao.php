<?php

namespace Application\Modules\AvaliacaoResultados\Service;

class TipoAvaliacao
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function tipoAvaliacao()
    {
        $idPronac = $this->request->getParam('idPronac');

        if (!$idPronac) {
            throw new Exception('Não existe idPronac');
        }

        $informacoes = new \AvaliacaoResultados_Model_DbTable_vwInformacoesConsolidadasParaAvaliacaoFinanceira();
        $informacoes = $informacoes->informacoes($idPronac)->toArray();
        return [ $informacoes[0]];
    }

}
