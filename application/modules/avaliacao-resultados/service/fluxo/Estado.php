<?php

namespace Application\Modules\AvaliacaoResultados\Service\Fluxo;

class Estado 
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    public function validar($atual, $proximoEstado) {
        $estado = new \AvaliacaoResultados_Model_DbTable_Estados();
        $estado = $estado->findBy($atual);
        $proximo = json_decode($estado['proximo']);

        if (!in_array($proximoEstado, $proximo->proximo)) {
            throw new \Exception('Esse fluxo não pode ser executado!');
        }

        return true;
    }

    public function eventos($atual, $params) {
        $estado = new \AvaliacaoResultados_Model_DbTable_Estados();
        $estado = $estado->findBy($atual);
        $proximo = json_decode($estado['proximo']);

        include(APPLICATION_PATH . $proximo->path);

        $eventClass = new $proximo->class();

        $eventClass->run($params);
    }
}