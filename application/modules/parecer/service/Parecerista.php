<?php

namespace Application\Modules\Parecer\Service;

class Parecerista implements \MinC\Servico\IServicoRestZend
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

    public function obterPareceristas()
    {
        $idOrgao = $this->request->getParam('idOrgao');
        $idArea = $this->request->getParam('idArea');
        $idSegmento = $this->request->getParam('idSegmento');
        $valor = $this->request->getParam('valor');

        $tbSpSelecionarParecerista = new \Parecer_Model_DbTable_SpSelecionarParecerista();
        return $tbSpSelecionarParecerista->obterPareceristas(
            $idOrgao,
            $idArea,
            $idSegmento,
            $valor
        );
    }
}
