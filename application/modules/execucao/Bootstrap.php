<?php

class Execucao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                "execucao" => [
                    'fiscalizacao-rest',
                ]
            ]
        );

        $nomeConjuntoDeRotas = 'restExecucao';
        $frontController->getRouter()->addRoute(
            $nomeConjuntoDeRotas,
            $restRoute
        );
    }
}
