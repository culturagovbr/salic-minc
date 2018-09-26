<?php

namespace Application\Modules\AvaliacaoResultados\Service\Fluxo;

class FluxoProjeto 
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    public function estado($idPronac) {

        $fluxoProjeto = new \AvaliacaoResultados_Model_DbTable_FluxosProjeto();
        $estado = $fluxoProjeto->estado($idPronac);

        return $estado;
    }
}